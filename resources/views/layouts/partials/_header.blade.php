<!-- Top Bar Start -->
<div class="topbar" id="header">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="/" class="logo">
            <span>
                {{-- <img src="{{ setting('logo') ? Storage::url(setting('logo')) : '' }}" alt="" height="30"> --}}
                <img src="{{ asset('images/logo.png') }}" alt="" height="20">
            </span>
            <i>
                {{-- <img src="{{ setting('small_logo') ? Storage::url(setting('small_logo')) : '' }}" alt=""
                height="30"
                width="50"> --}}
                <img src="{{ asset('images/logo.png') }}" alt="" height="20" width="50">
            </i>
        </a>
        {{-- <p class="" style="color: #ffffff; overflow: hidden; white-space: nowrap;">{{ auth()->user()->full_name ?? '' }}
        </p> --}}
    </div>

    <nav class="navbar-custom">
        <ul class="navbar-right d-flex list-inline float-right mb-0">

            <!-- notification -->
           <header-notification :user="{{ auth()->user() }}"></header-notification>
            
            <!-- full screen -->
            <li class="dropdown notification-list d-none d-md-block">
                <a class="nav-link waves-effect" href="#" id="btn-fullscreen">
                    <i class="mdi mdi-fullscreen noti-icon"></i>
                </a>
            </li>

            <li class="dropdown notification-list">
                <div class="dropdown notification-list nav-pro-img">

                    <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        @if(Auth::user()->image)
                        <img src="{{ asset('storage/user_images/' . Auth::user()->image) }}" alt="user"
                            class="rounded-circle">
                        @else
                        <img src="{{ asset('images/user_default.png') }}" alt="user" class="rounded-circle">
                        @endif

                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">

                        <a href="javascript:;" class="dropdown-item">
                            {{ \Illuminate\Support\Str::limit(Auth::user()->full_name, 15) ?? '' }}<br>
                            <small>{{ Auth::user()->email ?? '' }}</small>
                        </a>

                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i class="mdi mdi-power text-danger"></i>
                            Logout</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </li>

        </ul>

        <ul class="list-inline menu-left mb-0">
            <li class="float-left">
                <button class="button-menu-mobile open-left waves-effect">
                    <i class="mdi mdi-menu"></i>
                </button>
            </li>
        </ul>

    </nav>

</div>
<!-- Top Bar End -->