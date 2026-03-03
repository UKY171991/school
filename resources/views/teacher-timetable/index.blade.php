@extends('adminlte::page')

@section('title', 'Teacher Timetable')

@section('content_header')
    <h1>Teacher Timetable</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Timetable Entries</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="convertNewTimetable">
                        <i class="fas fa-plus"></i> Add Entry
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="filter_school_id" class="form-control select2">
                            <option value="">Filter by School</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filter_teacher_id" class="form-control select2">
                            <option value="">Filter by Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" data-school="{{ $teacher->school_id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <table class="table table-bordered table-striped" id="timetableTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>School</th>
                            <th>Teacher</th>
                            <th>Class - Section</th>
                            <th>Subject</th>
                            <th>Time Slot</th>
                            <th>Day</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="timetableList">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Timetable Modal -->
<div class="modal fade" id="timetableModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="timetableForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="timetableModalLabel">Add Timetable Entry</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="timetable_id" id="timetable_id">
                    
                    <div class="form-group mb-3">
                        <label>School <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="school_id" required style="width: 100%;">
                            <option value="">Select School</option>
                             @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Teacher <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="teacher_id" id="teacher_id" required style="width: 100%;">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" data-school="{{ $teacher->school_id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Class / Grade</label>
                        <select class="form-control" id="grade_id">
                            <option value="">Select Class</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Section</label>
                        <select class="form-control" name="section_id" id="section_id" required disabled>
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" data-grade="{{ $section->grade_id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <select class="form-control" name="subject_id" id="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name ?? 'Subject '.$subject->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Day</label>
                        <select class="form-control" name="day" id="day" required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" class="form-control" name="start_time" id="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Time</label>
                                <input type="time" class="form-control" name="end_time" id="end_time" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('.select2').select2({ theme: 'bootstrap4' });

        function loadTimetable() {
            let schoolId = $('#filter_school_id').val();
            let teacherId = $('#filter_teacher_id').val();
            
            $.get("{{ route('teacher-timetable.index') }}", { school_id: schoolId, teacher_id: teacherId }, function (data) {
                let rows = '';
                data.forEach(entry => {
                    let sectionName = entry.section ? (entry.section.name || 'Sec '+entry.section.id) : '-';
                    if(entry.section && entry.section.grade) { sectionName = `${entry.section.grade.name} - ${sectionName}`; }
                    let subjectName = entry.subject ? (entry.subject.name || 'Sub '+entry.subject.id) : '-';
                    
                    rows += `
                        <tr id="entry_${entry.id}">
                            <td>${entry.id}</td>
                            <td>${entry.teacher && entry.teacher.school ? `<span class="badge badge-light border">${entry.teacher.school.name}</span>` : '-'}</td>
                            <td><div class="font-weight-bold text-primary">${entry.teacher ? entry.teacher.name : '-'}</div></td>
                            <td>${sectionName}</td>
                            <td>${subjectName}</td>
                            <td><span class="badge badge-pill badge-light border"><i class="far fa-clock mr-1"></i> ${entry.start_time} - ${entry.end_time}</span></td>
                            <td><span class="badge badge-info">${entry.day}</span></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-outline-warning btn-sm border-0 editEntry" data-id="${entry.id}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm border-0 deleteEntry" data-id="${entry.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                if ($.fn.DataTable.isDataTable('#timetableTable')) {
                    $('#timetableTable').DataTable().destroy();
                }
                $('#timetableList').html(rows);
                $('#timetableTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "pageLength": 10
                });
            });
        }

        $('#filter_school_id, #filter_teacher_id').change(function() {
            loadTimetable();
        });
        
         // Dependent Dropdowns (Filter & Modal)
        $('#filter_school_id, #school_id').change(function() {
            let schoolId = $(this).val();
            let isModal = $(this).attr('id') == 'school_id';
            let teacherSelect = isModal ? $('#teacher_id') : $('#filter_teacher_id');
            
            teacherSelect.val('').trigger('change.select2');
            
            teacherSelect.find('option').each(function() {
                 let s = $(this).data('school');
                 if(!s || !schoolId || s == schoolId) {
                     $(this).prop('disabled', false);
                 } else {
                     $(this).prop('disabled', true);
                 }
            });
            teacherSelect.select2({ theme: 'bootstrap4', dropdownParent: isModal ? $('#timetableModal') : null });
             
             if(!isModal) loadTimetable();
        });

        loadTimetable();

        $('#convertNewTimetable').click(function () {
            $('#timetableForm').trigger("reset");
            $('#timetableModalLabel').html("Add New Entry");
            $('#timetable_id').val('');
            $('#grade_id').val('').trigger('change');
            $('#timetableModal').modal('show');
        });

        $('#timetableForm').submit(function (e) {
            e.preventDefault();
            let id = $('#timetable_id').val();
            let url = id ? `/admin/teacher-timetable/${id}` : "{{ route('teacher-timetable.store') }}";
            let type = id ? "PUT" : "POST";

            $.ajax({
                data: $(this).serialize(),
                url: url,
                type: type,
                dataType: 'json',
                success: function (data) {
                    $('#timetableForm').trigger("reset");
                    $('#timetableModal').modal('hide');
                    loadTimetable();
                    Swal.fire('Success', data.success, 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Something went wrong', 'error');
                }
            });
        });

        $('#grade_id').change(function() {
            let gradeId = $(this).val();
            let sectionSelect = $('#section_id');
            
            sectionSelect.val('').prop('disabled', true);
            sectionSelect.find('option').hide();
            sectionSelect.find('option[value=""]').show(); // Always show "Select Section" option
            
            if(gradeId) {
                let options = sectionSelect.find(`option[data-grade="${gradeId}"]`);
                if(options.length > 0) {
                    options.show();
                    sectionSelect.prop('disabled', false);
                } else {
                     // Maybe show no sections found?
                }
            } else {
                 sectionSelect.val('');
            }
        });

        $('body').on('click', '.editEntry', function () {
            let id = $(this).data('id');
            $.get(`/admin/teacher-timetable/${id}`, function (data) {
                $('#timetableModalLabel').html('<i class="fas fa-edit mr-2"></i>Edit Entry');
                $('#timetable_id').val(data.id);
                
                // Set School first
                if(data.teacher && data.teacher.school_id) {
                     $('#school_id').val(data.teacher.school_id).trigger('change');
                }
                setTimeout(() => {
                    $('#teacher_id').val(data.teacher_id).trigger('change');
                }, 100);
                
                // Pre-select grade and filter sections
                // We need to know the grade of the section. Since we have all sections in select, 
                // we can find the option and get its data-grade
                let sectionOption = $(`#section_id option[value="${data.section_id}"]`);
                let gradeId = sectionOption.data('grade');
                
                $('#grade_id').val(gradeId).trigger('change');
                $('#section_id').val(data.section_id);
                
                $('#subject_id').val(data.subject_id);
                $('#day').val(data.day);
                $('#start_time').val(data.start_time);
                $('#end_time').val(data.end_time);
                $('#timetableModal').modal('show');
            });
        });

        $('body').on('click', '.deleteEntry', function () {
            let id = $(this).data('id');
            if (confirm("Are you sure?")) {
                $.ajax({
                    type: "DELETE",
                    url: `/admin/teacher-timetable/${id}`,
                    success: function (data) {
                        $(`#entry_${id}`).remove();
                        Swal.fire('Deleted', data.success, 'success');
                    }
                });
            }
        });
    });
</script>
@stop
