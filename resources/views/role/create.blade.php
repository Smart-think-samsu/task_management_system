@extends('layouts.app')

@section('title')
    Role Add
@endsection

@php
    $page = "Role Add"
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
            <!-- SELECT2 EXAMPLE -->
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header">
                        <div class="text-left">
                            <a href="{{ url()->previous() }}" class="btn btn-primary"> <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>  Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Role Name">Role Name</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="role_name" id="role_name" required placeholder="Role name">

                                @error('role_name')
                                <p style="color: red">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="pl-5">
                                        <div class="custom-control custom-checkbox p-3" style="display:inline-block;">
                                            <input class="custom-control-input" type="checkbox" id="selectAll">
                                            <label for="selectAll" class="custom-control-label">Select All</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    @foreach ($permissions as $index => $permission)
                                        @if($index % 6 == 0 && $index != 0)
                                </tr><tr>
                                    @endif
                                    <td class="pl-5">
                                        <div class="custom-control custom-checkbox p-3" style="display:inline-block;">
                                            <input class="custom-control-input" type="checkbox" name="permissions[]" id="permissions{{ $permission->id }}" value="{{ $permission->id }}">
                                            <label for="permissions{{ $permission->id }}" class="custom-control-label">{{ $permission->name }}</label>
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-1">
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

@section('add_js')
    <script>
        $(document).ready(function(){
            $('#selectAll').click(function(){
                if($(this).is(':checked')){
                    $('.custom-control-input').prop('checked', true);
                } else {
                    $('.custom-control-input').prop('checked', false);
                }
            });
        });

    </script>
@endsection
