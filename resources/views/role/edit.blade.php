@extends('layouts.app')

@section('title')
    Role Edit
@endsection

@php
    $page = "Role Edit"
@endphp

@section('main_content')
    <section class="content">
        <div class="container-fluid">
            <!-- SELECT2 EXAMPLE -->
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('roles.update', $roleWithPermissions->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <div class="text-left">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">
                                <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Role Name">Role Name</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="role_name" id="role_name" value="{{ $roleWithPermissions->name }}" required placeholder="Role name">
                                @error('role_name')
                                <p style="color: red">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <!-- Responsive table wrapper -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        @foreach ($permissions as $index => $permission)
                                            @if($index % 6 == 0 && $index != 0)
                                    </tr><tr>
                                        @endif
                                        <td class="pl-5">
                                            <div class="custom-control custom-checkbox p-3" style="display:inline-block;">
                                                <input class="custom-control-input" type="checkbox" name="permissions[]" id="permissions{{ $permission->id }}" value="{{ $permission->id }}"
                                                       @foreach($roleWithPermissions->permissions as $select_permission)
                                                           @if ($select_permission->id == $permission->id)
                                                               checked
                                                    @endif
                                                    @endforeach
                                                >
                                                <label for="permissions{{ $permission->id }}" class="custom-control-label">{{ $permission->name }}</label>
                                            </div>
                                        </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
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

@endsection
