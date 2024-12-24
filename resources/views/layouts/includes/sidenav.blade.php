<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{asset('assets/dist/img/logo_1.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> Task Manager</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group mt-2" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link @if($page == 'Dashboard') active @endif">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @can('task-list')
                    <li class="nav-item">
                        <a href="{{Route('tasks.index')}}" class="nav-link @if($page == 'Task list' || $page == 'Task Create' || $page == 'Task edit') active @endif">
                            <i class="nav-icon fas fa-undo"></i>
                            <p>
                                Task Management
                            </p>
                        </a>
                    </li>
                @endcan

                @can('role')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link @if($page == 'Role List' || $page == 'Role Add' || $page == 'Role Edit'|| $page == 'Role view') active @endif">
                            <i class="nav-icon fas fa-user-cog"></i>
                            <p>
                                Role Management
                            </p>
                        </a>
                    </li>
                @endcan

                @can('permission')
                    <li class="nav-item">
                        <a href="{{ route('permissions.index') }}" class="nav-link @if($page == 'Permission List' || $page == 'Permission Add' || $page == 'Permission Edit'|| $page == 'Permission view') active @endif">
                            <i class="nav-icon fas fa-lock"></i>
                            <p>
                                Permission
                            </p>
                        </a>
                    </li>
                @endcan

                @can('user')
                    <li class="nav-item">
                        <a href="{{ route('user.index') }}" class="nav-link @if($page == 'User' || $page == 'User Create') active @endif">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                User Management
                            </p>
                        </a>
                    </li>
                @endcan

                @can('settings')
                    <li class="nav-item @if($page == 'Settings' || $page == 'Dashboard Setting' || $page == 'Box Operations Log' || $page == 'Chalan Log') menu-open @endif">
                        <a href="#" class="nav-link @if($page == 'Settings' || $page == 'Dashboard Setting' || $page == 'Box Operations Log' || $page == 'Chalan Log') active @endif">
                            <i class="nav-icon fas fa-list-alt"></i>
                            <p>
                                Settings
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
{{--                        <ul class="nav nav-treeview">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="{{route('settings.create')}}" class="nav-link @if($page == 'Dashboard Setting') active @endif">--}}
{{--                                    <i class="far fa-dot-circle nav-icon"></i>--}}
{{--                                    <p>Site Setting</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                        <ul class="nav nav-treeview">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="#" class="nav-link @if($page == 'Box Operations Log') active @endif">--}}
{{--                                    <i class="far fa-dot-circle nav-icon"></i>--}}
{{--                                    <p>#</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                        <ul class="nav nav-treeview">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="#" class="nav-link @if($page == 'Chalan Log') active @endif">--}}
{{--                                    <i class="far fa-dot-circle nav-icon"></i>--}}
{{--                                    <p>#</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                        <ul class="nav nav-treeview">--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="{{ route('settings.index') }}" class="nav-link  @if($page == 'Settings') active @endif">--}}
{{--                                    <i class="far fa-dot-circle nav-icon"></i>--}}
{{--                                    <p>SMS</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
                    </li>
                @endcan

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
