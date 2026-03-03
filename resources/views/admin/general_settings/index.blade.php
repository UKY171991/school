@extends('adminlte::page')

@section('title', __('General Settings'))

@section('content_header')
    <h1>{{ __('General Settings') }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">{{ __('General') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="#logos" data-toggle="tab">{{ __('Logos & Images') }}</a></li>
        </ul>
    </div>
    <div class="card-body">
        <form id="settingsForm" enctype="multipart/form-data">
            @csrf
            <div class="tab-content">
                <!-- General Tab -->
                <div class="active tab-pane" id="general">
                    <div class="form-group row">
                        <label for="school_name" class="col-sm-2 col-form-label">{{ __('School Name') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="school_name" name="school_name" value="{{ $settings->school_name }}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="school_address" class="col-sm-2 col-form-label">{{ __('Address') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="school_address" name="school_address" value="{{ $settings->school_address }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="school_phone" class="col-sm-2 col-form-label">{{ __('Phone') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="school_phone" name="school_phone" value="{{ $settings->school_phone }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="school_email" class="col-sm-2 col-form-label">{{ __('Email') }}</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="school_email" name="school_email" value="{{ $settings->school_email }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="currency_symbol" class="col-sm-2 col-form-label">{{ __('Currency Symbol') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="{{ $settings->currency_symbol }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="footer_text" class="col-sm-2 col-form-label">{{ __('Footer Text') }}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="footer_text" name="footer_text" value="{{ $settings->footer_text }}">
                        </div>
                    </div>
                </div>

                <!-- Logos Tab -->
                <div class="tab-pane" id="logos">
                    <div class="form-group row">
                        <label for="logo" class="col-sm-2 col-form-label">{{ __('Logo') }}</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo" name="logo">
                                    <label class="custom-file-label" for="logo">{{ __('Choose file') }}</label>
                                </div>
                            </div>
                            @if($settings->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($settings->logo) }}" alt="{{ __('Current Logo') }}" style="max-height: 50px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="favicon" class="col-sm-2 col-form-label">{{ __('Favicon') }}</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="favicon" name="favicon">
                                    <label class="custom-file-label" for="favicon">{{ __('Choose file') }}</label>
                                </div>
                            </div>
                            @if($settings->favicon)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($settings->favicon) }}" alt="{{ __('Current Favicon') }}" style="max-height: 32px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#settingsForm').submit(function(e) {
            e.preventDefault();
            
            // Validate form (basic)
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.general-settings.update') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Success!") }}',
                        text: data.success,
                    }).then(() => {
                        location.reload(); 
                    });
                },
                error: function (data) {
                     var errors = data.responseJSON.errors;
                     var errorMessage = "";
                     $.each(errors, function(key, value) {
                         errorMessage += value[0] + "\n";
                     });
                     
                     Swal.fire({
                        icon: 'error',
                        title: '{{ __("Error!") }}',
                        text: errorMessage,
                    });
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
        
        // Custom File Input
        bsCustomFileInput.init();
    });
</script>
@stop
