<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherTimetable;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Subject;

class TeacherTimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TeacherTimetable::with(['teacher.school', 'section.grade', 'subject']);
            
            if ($request->has('school_id') && !empty($request->school_id)) {
                $schoolId = $request->school_id;
                $query->whereHas('teacher', function($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                });
            }

            if ($request->has('teacher_id') && !empty($request->teacher_id)) {
                $query->where('teacher_id', $request->teacher_id);
            }

            // Sort by Grade Name (Class Name)
            $query->join('sections', 'teacher_timetables.section_id', '=', 'sections.id')
                  ->join('grades', 'sections.grade_id', '=', 'grades.id')
                  ->orderBy('grades.name', 'asc')
                  ->select('teacher_timetables.*');

            $timetables = $query->get(); 
            return response()->json($timetables);
        }

        $schools = \App\Models\School::all();
        $teachers = Teacher::all();
        $grades = \App\Models\Grade::orderBy('name', 'asc')->get();
        $sections = Section::with('grade')->get(); 
        $subjects = Subject::all();

        return view('teacher-timetable.index', compact('schools', 'teachers', 'grades', 'sections', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'section_id' => 'required', 
            'subject_id' => 'required',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        if ($this->hasConflict($request)) {
             return response()->json(['errors' => ['conflict' => ['This time slot is already occupied for this Teacher or Section.']]], 422);
        }
        
        if ($this->hasDuplicateSubject($request)) {
             return response()->json(['errors' => ['duplicate' => ['This Subject is already scheduled for this Section on this Day.']]], 422);
        }

        $timetable = TeacherTimetable::create($validated);

        return response()->json(['success' => 'Timetable entry created successfully.', 'timetable' => $timetable]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $timetable = TeacherTimetable::findOrFail($id);
        return response()->json($timetable);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $timetable = TeacherTimetable::findOrFail($id);

        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'section_id' => 'required',
            'subject_id' => 'required',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        if ($this->hasConflict($request, $id)) {
             return response()->json(['errors' => ['conflict' => ['This time slot is already occupied for this Teacher or Section.']]], 422);
        }
        
        if ($this->hasDuplicateSubject($request, $id)) {
             return response()->json(['errors' => ['duplicate' => ['This Subject is already scheduled for this Section on this Day.']]], 422);
        }

        $timetable->update($validated);

        return response()->json(['success' => 'Timetable entry updated successfully.', 'timetable' => $timetable]);
    }

    private function hasConflict(Request $request, $ignoreId = null)
    {
        $query = TeacherTimetable::where('day', $request->day)
            ->where(function ($q) use ($request) {
                // Check for Teacher Conflict OR Section Conflict
                $q->where('teacher_id', $request->teacher_id)
                  ->orWhere('section_id', $request->section_id);
            })
            ->where(function ($q) use ($request) {
                // Check Time Overlap
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
    
    private function hasDuplicateSubject(Request $request, $ignoreId = null)
    {
        // Prevent adding the same subject to the same section on the same day twice.
        // e.g. Math for Class 1A on Monday cannot happen twice.
        $query = TeacherTimetable::where('day', $request->day)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timetable = TeacherTimetable::findOrFail($id);
        $timetable->delete();

        return response()->json(['success' => 'Timetable entry deleted successfully.']);
    }
}
