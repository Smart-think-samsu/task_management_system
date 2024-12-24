@extends('layouts.app')

@section('title')
    Role Create
@endsection

@php
    $page = "Role view"
@endphp

@section('main_content')

    <section class="content">
        <div class="container-fluid">
            <!-- Card Example -->
            <div class="card">
                <!-- Card Header -->
                <div class="card-header">
                    <div class="text-left">
                        <a href="{{ url()->previous() }}" class="btn btn-primary">
                            <i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back
                        </a>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="form-group col-md-4">
                        <label for="roleName">Role Name:</label>
                        {{ $roleWithPermission->name }}
                    </div>
                    <!-- Responsive Table Wrapper -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                @foreach ($roleWithPermission->permissions as $index => $permission)
                                    @if ($index % 5 == 0 && $index != 0)
                            </tr><tr>
                                @endif
                                <td>{{ $permission->name }}</td>
                                @endforeach
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@section('add_js')
@endsection
