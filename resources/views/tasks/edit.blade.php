@extends('layouts.app')

@section('title')
    Task edit
@endsection
@php
    $page = "Task edit";
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
                        <form action="{{route('tasks.update',$task->id)}}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Title</label>
                                            <input type="text" name="title" class="form-control" value="{{$task->title}}" placeholder="Enter Title">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Description</label>
                                            <input type="text" name="description" class="form-control" value="{{$task->description}}"  placeholder="Enter task description">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Complete Last Date</label>
                                            <input type="datetime-local" name="completion_at" class="form-control"
                                                   value="{{ \Carbon\Carbon::parse($task->completion_at)->format('Y-m-d\TH:i') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Status</label>
                                            <select class="form-control" name="is_completed">
                                                <option value="0" @selected($task->is_completed == 0)>Pending</option>
                                                <option value="1" @selected($task->is_completed == 1)>Progress</option>
                                                <option value="2" @selected($task->is_completed == 2)>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
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
