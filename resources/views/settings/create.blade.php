@extends('layouts.app')

@section('title')
    User Create
@endsection
@php
    $page = "Dashboard Setting";
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="text-left">
                                <a href="{{ url()->previous() }}" class="btn btn-primary"> <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>  Back</a>
                            </div>
                        </div>
                        <form action="{{route('settings.update', $setting->id)}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control" value="{{ $setting->title }}" placeholder="Enter Title">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Logo</label>
                                            <input type="file" name="logo" id="logo" class="form-control" placeholder="Upload logo"> <br>
                                            @if($setting->logo)
                                                <img id="logo_preview" src="{{ asset($setting->logo) }}" alt="Logo not found" height="150" width="150"/>
                                            @else
                                                <img id="logo_preview" src="#" alt="No Logo Available" height="150" width="150"/>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fav Icon</label>
                                            <input type="file" name="fav_icon" id="fav_icon" class="form-control" placeholder="Upload fav icon"> <br>
                                            @if($setting->fav_icon)
                                                <img id="fav_logo_preview" src="{{ asset($setting->fav_icon) }}" alt="Fav Icon not found" height="100" width="100"/>
                                            @else
                                                <img id="fav_logo_preview" src="#" alt="No Fav Icon Available" height="100" width="100"/>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
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
        //upload logo show preview
        logo.onchange = evt => {
            const [file] = logo.files
            if (file) {
                logo_preview.src = URL.createObjectURL(file)
            }
        }

        fav_icon.onchange = evt => {
            const [file] = fav_icon.files
            if (file) {
                fav_logo_preview.src = URL.createObjectURL(file)
            }
        }

        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        })
    </script>
@endsection
