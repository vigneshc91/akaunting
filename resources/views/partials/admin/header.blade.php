<header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
        @if (setting('general.admin_theme', 'skin-green-light') == 'skin-green-light')
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src="{{ asset('public/img/akaunting-logo-white.png') }}" class="logo-image-mini" width="25" alt="Akaunting Logo"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src="{{ asset('public/img/akaunting-logo-white.png') }}" class="logo-image-lg" width="25" alt="Akaunting Logo"> <b>Billing</b></span>
        @else
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src="{{ asset('public/img/akaunting-logo-green.png') }}" class="logo-image-mini" width="25" alt="Akaunting Logo"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src="{{ asset('public/img/akaunting-logo-green.png') }}" class="logo-image-lg" width="25" alt="Akaunting Logo"> <b>Billing</b></span>
        @endif
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        @if ($user->picture)
                            @if (setting('general.use_gravatar', '0') == '1')
                                <img src="{{ $user->picture }}" class="user-image" alt="User Image">
                            @else
                                <img src="{{ Storage::url($user->picture->id) }}" class="user-image" alt="User Image">
                            @endif
                        @else
                            <i class="fa fa-user-o"></i>
                        @endif
                        @if (!empty($user->name))
                            <span class="hidden-xs">{{ $user->name }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            @if ($user->picture)
                                @if (setting('general.use_gravatar', '0') == '1')
                                    <img src="{{ $user->picture }}" class="img-circle" alt="User Image">
                                @else
                                    <img src="{{ Storage::url($user->picture->id) }}" class="img-circle" alt="User Image">
                                @endif
                            @else
                                <i class="fa fa-4 fa-user-o" style="color: #fff; font-size: 7em;"></i>
                            @endif
                            <p>
                                @if (!empty($user->name))
                                {{ $user->name }}
                                @endif
                                <small>{{ trans('header.last_login', ['time' => $user->last_logged_in_at]) }}</small>
                            </p>
                        </li>
                      
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            @permission('read-auth-profile')
                            <div class="pull-left">
                                <a href="{{ url('auth/users/' . $user->id . '/edit') }}" class="btn btn-default btn-flat">{{ trans('auth.profile') }}</a>
                            </div>
                            @endpermission
                            <div class="pull-right">
                                <a href="{{ url('auth/logout') }}" class="btn btn-default btn-flat">{{ trans('auth.logout') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
