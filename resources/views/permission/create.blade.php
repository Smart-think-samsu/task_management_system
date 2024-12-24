@extends('layouts.app')

@section('title')
    Permission Add
@endsection

@php
    $page = "Permission Add"
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
            <!-- SELECT2 EXAMPLE -->
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('permissions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <div class="text-left">
                            <a href="{{ route('permissions.index') }}" class="btn btn-primary"> <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>  Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Role Name">Permission Name</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="permission_name" id="permission_name" required placeholder="Permission">

                                @error('permission_name')
                                <p style="color: red">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-block btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection
