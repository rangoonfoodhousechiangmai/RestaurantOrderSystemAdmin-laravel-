<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'dashboard' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.index') }}">
                {{-- <i class="icon-grid menu-icon"></i> --}}
                <i class="fa-solid fa-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'tables' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tables.index') }}">
                {{-- <i class="icon-grid menu-icon"></i> --}}
                <i class="fa-solid fa-table menu-icon"></i>
                <span class="menu-title">Table</span>
            </a>
        </li>
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'categories' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('categories.index') }}">
                {{-- <i class="icon-grid menu-icon"></i> --}}
                <i class="fa-solid fa-newspaper fa-2x menu-icon"></i>
                <span class="menu-title">Category</span>
            </a>
        </li>
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'menus' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('menus.index') }}">
                {{-- <i class="icon-grid menu-icon"></i> --}}
                <i class="fa-solid fa-utensils menu-icon"></i>
                <span class="menu-title">Menu</span>
            </a>
        </li>
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'modifiers' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('modifiers.index') }}">
                {{-- <i class="icon-grid menu-icon"></i> --}}
                <i class="fa-regular fa-newspaper fa-2x menu-icon"></i>
                <span class="menu-title">Modifiers</span>
            </a>
        </li>
        <li class="nav-item {{ isset($elementActive) && $elementActive == 'orders' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('orders.index') }}">
                <i class="fa-solid fa-chart-bar fa-2x menu-icon"></i>
                <span class="menu-title">Orders</span>
            </a>
        </li>
        {{-- <li class="nav-item {{ isset($elementActive) && $elementActive == 'menu-modifiers' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('menu-modifiers.index') }}">
                <i class="fa-regular fa-newspaper fa-2x menu-icon"></i>
                <span class="menu-title">Menu Modifiers</span>
            </a>
        </li> --}}

        {{--
        <li class="nav-item {{ (isset($elementActive) && $elementActive == 'trend-posts') ? 'active' : '' }} }}">
            <a class="nav-link" href="{{ route('social-posts.index') }}">
                <i class="fa-regular fa-face-smile-beam menu-icon"></i>
                <span class="menu-title">Trending Posts</span>
            </a>
        </li>

        <li class="nav-item {{ (isset($elementActive) && $elementActive == 'chats') ? 'active' : '' }} }}">
            <a class="nav-link" href="{{ route('chats.index') }}">
                <i class="fa-regular fa-message menu-icon"></i>
                <span class="menu-title">Conversation</span>
            </a>
        </li> --}}

        {{-- @if (auth()->user()->role == 'user') --}}
        {{-- <li class="nav-item {{ (isset($elementActive) && $elementActive == 'academic-calendars') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('academic-calendars.index') }}">
                <i class="fa-regular fa-newspaper fa-2x menu-icon"></i>
                <span class="menu-title">Academic Calendar</span>
            </a>
        </li> --}}
        {{-- @endif --}}


        {{-- @if (auth()->user()->role == 'user') --}}
        {{-- <li class="nav-item {{ (isset($elementActive) && $elementActive == 'events') ? 'active' : '' }} }}">
            <a class="nav-link" href="{{ route('events.index') }}">
                <i class="fa-regular fa-calendar menu-icon"></i>
                <span class="menu-title">Events</span>
            </a>
        </li> --}}
        {{-- @endif --}}

        {{-- @if (auth()->user()->role == 'admin')
            <li class="nav-item {{ isset($elementActive) && $elementActive == 'department-types' ? 'active': '' }}">
                <a class="nav-link" href="{{ route('department-types.index') }}">
                    <i class="icon-grid menu-icon"></i>
                    <span class="menu-title">Department Types</span>
                </a>
            </li>

            <li class="nav-item {{ isset($elementActive) && $elementActive == 'departments' ? 'active': '' }}">
                <a class="nav-link" href="{{ route('departments.index') }}">
                    <i class="fa-regular fa-building menu-icon"></i>
                    <span class="menu-title">Departments</span>
                </a>
            </li>
        @endif

        <li class="nav-item {{ isset($elementActive) && $elementActive == 'profiles' ? 'active': '' }}">
            <a class="nav-link" href="{{ route('profiles.show', auth()->user()->id) }}">
                <i class="fa-regular fa-user menu-icon"></i>
                <span class="menu-title">Profile</span>
            </a>
        </li>

        <li class="nav-item {{ (isset($elementActive) && Str::startsWith($elementActive, 'campus-informations')) ? 'active' : '' }}">
            <a class="nav-link" data-bs-toggle="collapse" href="#campus-informations" aria-expanded="false"
                aria-controls="ui-basic">
                <i class="fa-regular fa-building menu-icon"></i>
                <span class="menu-title">Campus Information</span>
            </a>
            <div class="collapse {{ (isset($elementActive) && Str::startsWith($elementActive, 'campus-informations')) ? 'show' : '' }}" id="campus-informations">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item "> <a class="nav-link {{ $elementActive == 'campus-informations.hua-mak' ? 'active' : '' }}" href="{{ route('campus-informations.huamak') }}">Hua Mak</a></li>
                    <li class="nav-item "> <a class="nav-link {{ $elementActive == 'campus-informations.suvarnabhumi' ? 'active' : '' }}" href="{{ route('campus-informations.suvarnabhumi') }}">Suvarnabhumi</a></li>
                </ul>
            </div>
        </li> --}}


        {{-- <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#conversation" aria-expanded="false"
                aria-controls="conversation">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">C</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="conversation">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="#">Conversations</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('user.chats') }}">Conversations</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('user.profileedit') }}">Edit</a></li>
                    <li class="nav-item"> <a class="nav-link" href="#">Typography</a></li>
                </ul>
            </div>
        </li> --}}
    </ul>
</nav>
<!-- partial -->
