<!DOCTYPE html>
<html lang="en">
@include('layouts.includes.head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Preloader -->
{{--        <div class="preloader flex-column justify-content-center align-items-center">--}}
{{--            <img class="animation__shake" src="{{asset('assets/dist/img/logo_1.png')}}" alt="AdminLTELogo" height="60" width="60">--}}
{{--        </div>--}}

    <!-- Navbar -->
    @include('layouts.includes.topnav')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('layouts.includes.sidenav')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $page }}</h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        @yield('main_content')
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <strong>Copyright &copy; @php echo date('Y') @endphp </strong>
        All rights reserved.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@include('layouts.includes.script')
</body>
</html>
