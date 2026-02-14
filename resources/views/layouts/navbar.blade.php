<!-- partial:partials/_navbar.html -->
<nav class="navbar col-12 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <p class="fs-4 text-wrap fw-bolder" style="z-index: 999; color: #E91E63">Rangoon Food House</p>

    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <i class="icon-menu"></i>
        </button>
        <ul class="navbar-nav navbar-nav-right">
            {{-- <li class="nav-item dropdown me-3">
                <a class="nav-link count-indicator noti-icon dropdown-toggle position-relative" id="notificationDropdown"
                    data-bs-toggle="dropdown">
                    <i class="fa-regular fa-bell  menu-icon"></i>
                    <!-- Badge positioned at top-right corner -->

                    <!-- Comment notifications badge -->
                    @php
                        $commentNotiCount = auth()->user()->unreadNotifications()->where('type', 'App\Notifications\CommentNotification')->count();
                    @endphp
                    <span class="badge bg-danger rounded-pill position-absolute top-0 translate-middle {{ $commentNotiCount > 0 ? '' : 'd-none' }}"
                        id="notification-count" style="margin-left: -30px;">
                        {{ $commentNotiCount > 0 ? $commentNotiCount : 0 }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                    <div id="noti_list">

                    </div>
                </div>
            </li> --}}
            {{-- <li class="nav-item dropdown me-3">
                <a class="nav-link count-indicator chat-noti-icon dropdown-toggle position-relative" id="notificationDropdown"
                    data-bs-toggle="dropdown">
                     <i class="fa-regular fa-message"></i>

                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Message Notifications</p>
                    <div id="chat_noti_list">

                    </div>
                </div>
            </li> --}}

            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
                    <span class="">{{ Auth::user()->name }}</span>
                    <img src="{{ asset('src/assets/images/default-user-image.svg') }}" alt="profile" />
                    {{-- <img src="{{ asset('storage/'.  $LoggedUserInfo->picture) }}" alt="profile" /> --}}
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item">
                        <i class="ti-settings text-primary"></i> Settings </a>
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item">
                        @csrf
                        <button type="submit" class="btn btn-pink">Logout</button>
                    </form>

                </div>
            </li>
            {{-- <li class="nav-item nav-settings d-none d-lg-flex">
                <a class="nav-link" href="#">
                    <i class="icon-ellipsis"></i>
                </a>
            </li> --}}
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->
