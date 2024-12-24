@extends('layouts.app')
@section('title')
    Settings
@endsection

@php
    $page = "Settings"
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label>SMS Setting</label>
                                    <div class="form-group clearfix">
                                        <div class="icheck-success d-inline">
                                            <input type="radio" id="radioSuccess1" name="r1" <?php echo $smsSetting->status == "ON" ? 'checked' : ''; ?>>
                                            <label for="radioSuccess1">
                                                ON
                                            </label>
                                        </div>

                                        <div class="icheck-danger d-inline">
                                            <input type="radio" id="radioDanger1" <?php echo $smsSetting->status == "OFF" ? 'checked' : ''; ?> name="r1">
                                            <label for="radioDanger1">
                                                OFF
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@section('add_js')
    <script>
        $(document).ready(function() {
            $('input[name="r1"]').on('change', function() {
                var status = $(this).attr('id') === 'radioSuccess1' ? 'ON' : 'OFF';

                // Show the loading overlay
                $('#loadingOverlay').show();

                $.ajax({
                    url: '{{ route("settings.sms.update") }}',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'SMS setting has been updated successfully.'
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
@endsection
