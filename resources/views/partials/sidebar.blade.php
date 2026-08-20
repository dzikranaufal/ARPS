{{-- resources/views/partials/sidebar.blade.php --}}
<div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
    <div class="sidebar-header border-bottom">
        <div class="sidebar-brand me-auto">
            <h1>ARPS</h1>
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="var(--ci-primary-color, currentcolor)" d="M425.706 142.294A240 240 0 0 0 16 312v88h144v-32H48v-56c0-114.691 93.309-208 208-208s208 93.309 208 208v56H352v32h144v-88a238.43 238.43 0 0 0-70.294-169.706" class="ci-primary" />
                    <path fill="var(--ci-primary-color, currentcolor)" d="M80 264h32v32H80zm160-136h32v32h-32zm-104 40h32v32h-32zm264 96h32v32h-32zm-102.778 71.1 69.2-144.173-28.85-13.848-69.183 144.135a64.141 64.141 0 1 0 28.833 13.886M256 416a32 32 0 1 1 32-32 32.036 32.036 0 0 1-32 32" class="ci-primary" />
                </svg>
                Dashboard
            </a>
        </li>

        {{-- Membership group — links are placeholders (href="#") until those
             views/routes exist. Only Journals below is wired to a real route. --}}
        <li class="nav-group {{ request()->routeIs('admin.membership.*') ? 'show' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="var(--ci-primary-color, currentcolor)" d="M494 198.671a40.54 40.54 0 0 0-32.174-27.592l-115.909-18.837-53.732-104.414a40.7 40.7 0 0 0-72.37 0l-53.732 104.414-115.907 18.837a40.7 40.7 0 0 0-22.364 68.827l82.7 83.368-17.9 116.055a40.672 40.672 0 0 0 58.548 42.538L256 428.977l104.843 52.89a40.69 40.69 0 0 0 58.548-42.538l-17.9-116.055 82.7-83.368A40.54 40.54 0 0 0 494 198.671m-32.53 18.7L367.4 312.2l20.364 132.01a8.671 8.671 0 0 1-12.509 9.088L256 393.136 136.744 453.3a8.671 8.671 0 0 1-12.509-9.088L144.6 312.2l-94.069-94.83a8.7 8.7 0 0 1 4.778-14.706l131.841-21.426 61.119-118.767a8.694 8.694 0 0 1 15.462 0l61.119 118.767 131.841 21.426a8.7 8.7 0 0 1 4.778 14.706Z" class="ci-primary" />
                </svg>
                Membership
            </a>
            <ul class="nav-group-items compact">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        All Members
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <span class="nav-icon"><span class="nav-icon-bullet"></span></span>
                        Requests
                        <span class="badge badge-sm bg-warning ms-auto">3</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-title">Content Management</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-calendar nav-icon"></i>
                Events
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.journals.*') ? 'active' : '' }}"
                href="{{ route('admin.journals.index') }}">
                <i class="cil-description nav-icon"></i>
                Journals
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-bullhorn nav-icon"></i>
                News
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-file nav-icon"></i>
                Publications
            </a>
        </li>

        <li class="nav-title">Organization</li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.organization.edit') ? 'active' : '' }}"
            href="{{ route('admin.organization.edit') }}">
                <i class="cil-excerpt nav-icon"></i>
                Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.structure.*') ? 'active' : '' }}"
            href="{{ route('admin.structure.index') }}">
                <i class="cil-sitemap nav-icon"></i>
                Structure
            </a>
        </li>

        <li class="nav-title">Users</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-user-follow nav-icon"></i>
                Admin Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-lock-locked nav-icon"></i>
                Role &amp; Permissions
            </a>
        </li>

        <li class="nav-title">Settings</li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-settings nav-icon"></i>
                General Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="cil-share nav-icon"></i>
                Social Media
            </a>
        </li>

    </ul>
    <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
</div>