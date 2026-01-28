<!-- ========== Left Sidebar Start ========== -->
<style>
    .vertical-menu {
        background-color: #ff4900;
        width: 260px;
        min-height: 100vh;
        padding: 20px 0;
    }

    .vertical-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .vertical-menu li {
        margin-bottom: 5px;
    }

    .vertical-menu a {
        display: flex;
        align-items: center;
        gap: 16px;
        color: #fff;
        background: transparent;
        padding: 12px 20px;
        font-size: 14px;
        transition: background 0.2s, color 0.2s;
        font-weight: 500;
        text-decoration: none;
    }

    .vertical-menu a i {
        font-size: 18px;
        width: 20px;
        text-align: center;
    }

    .vertical-menu a.active-link,
    .vertical-menu li.mm-active>a {
        background: #fff;
        color: #ff4900 !important;
        font-weight: 600;
    }

    .vertical-menu a.active-link i,
    .vertical-menu li.mm-active>a i {
        color: #ff4900 !important;
    }

    .vertical-menu a:hover:not(.active-link) {
        background: rgba(255, 255, 255, 0.15);
    }

    /* Dropdown styles */
    .submenu {
        background: rgba(255, 255, 255, 0.1);
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s ease;
    }

    .submenu.show {
        max-height: 500px;
    }

    .submenu li {
        margin: 0;
    }

    .submenu a {
        padding: 10px 20px 10px 50px;
        font-size: 13px;
    }

    .submenu a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .submenu a.active-link {
        background: rgba(255, 255, 255, 0.2);
        color: #fff !important;
        font-weight: 600;
    }

    .has-submenu>a {
        position: relative;
    }

    .has-submenu>a::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        margin-left: auto;
        transition: transform 0.3s ease;
    }

    .has-submenu.active>a::after {
        transform: rotate(180deg);
    }

    /* Logo section */
    .sidebar-logo {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 20px;
    }

    .sidebar-logo h3 {
        color: white;
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .sidebar-logo p {
        color: rgba(255, 255, 255, 0.8);
        margin: 5px 0 0 0;
        font-size: 12px;
    }

    /* Responsive optimization for smaller screens */
    @media (max-width: 600px) {
        .vertical-menu {
            width: 100%;
            min-width: unset;
            padding: 10px 0;
        }

        .vertical-menu a {
            padding: 12px 15px;
            font-size: 15px;
        }
    }
</style>

{{-- Modified Sidebar --}}
<div class="vertical-menu">
    <ul>
        @php
            $user = auth()->user();
        @endphp

        <!-- Dashboard -->
        @if ($user->hasPermission('view_dashboard'))
            <li class="{{ request()->routeIs('admin.dashboard*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"
                    class="waves-effect {{ request()->routeIs('admin.dashboard*') ? 'active-link' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Dashboards
                </a>
            </li>
        @endif

        <!-- Patient Management -->
        @if ($user->hasPermission('view_management'))
            <li
                class="has-submenu {{ request()->routeIs('admin.appointments.*') ||
                request()->routeIs('admin.doctors.*') ||
                request()->routeIs('admin.staff.*') ||
                request()->routeIs('admin.patients.*') ||
                request()->routeIs('admin.queue.*') ||
                request()->routeIs('admin.events.*') ||
                request()->routeIs('admin.payments.*') ||
                request()->routeIs('admin.beds.*') ||
                request()->routeIs('admin.emergency.*')
                    ? 'active'
                    : '' }}">
                <a href="#" class="waves-effect">
                    <i class="fas fa-user-injured"></i>
                    Management
                </a>
                <ul
                    class="submenu {{ request()->routeIs('admin.appointments.*') ||
                    request()->routeIs('admin.doctors.*') ||
                    request()->routeIs('admin.staff.*') ||
                    request()->routeIs('admin.patients.*') ||
                    request()->routeIs('admin.invoices.*') ||
                    request()->routeIs('admin.payments.*') ||
                    request()->routeIs('admin.events.*') ||
                    request()->routeIs('admin.beds.*') ||
                    request()->routeIs('admin.queue.*') ||
                    request()->routeIs('admin.emergency.*')
                        ? 'show'
                        : '' }}">

                    @if ($user->hasPermission('manage_beds'))
                        <li class="{{ request()->routeIs('admin.beds.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.beds.index') }}"
                                class="{{ request()->routeIs('admin.beds.*') ? 'active-link' : '' }}">
                                Bed Management
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_appointments'))
                        <li class="{{ request()->routeIs('admin.appointments.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.appointments.index') }}"
                                class="{{ request()->routeIs('admin.appointments.*') ? 'active-link' : '' }}">
                                Appointment Calendar
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_events'))
                        <li class="{{ request()->routeIs('admin.events.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.events.index') }}"
                                class="{{ request()->routeIs('admin.events.*') ? 'active-link' : '' }}">
                                Event Management
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_queue'))
                        <li class="{{ request()->routeIs('admin.queue.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.queue.dashboard') }}"
                                class="{{ request()->routeIs('admin.queue.*') ? 'active-link' : '' }}">
                                Queue Management
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_doctors'))
                        <li class="{{ request()->routeIs('admin.doctors.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.doctors.index') }}"
                                class="{{ request()->routeIs('admin.doctors.*') ? 'active-link' : '' }}">
                                Doctor Management
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_staff'))
                        <li class="{{ request()->routeIs('admin.staff.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.staff.index') }}"
                                class="{{ request()->routeIs('admin.staff.*') ? 'active-link' : '' }}">
                                Staff Management & Roster
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_patients'))
                        <li class="{{ request()->routeIs('admin.patients.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.patients.index') }}"
                                class="{{ request()->routeIs('admin.patients.*') ? 'active-link' : '' }}">
                                Patient Management
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_emergency'))
                        <li class="{{ request()->routeIs('admin.emergency.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.emergency.index') }}"
                                class="{{ request()->routeIs('admin.emergency.*') ? 'active-link' : '' }}">
                                Emergency Triage
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_payments'))
                        <li class="{{ request()->routeIs('admin.payments.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.payments.index') }}"
                                class="{{ request()->routeIs('admin.payments.*') ? 'active-link' : '' }}">

                                Payment Transactions
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_invoices'))
                        <li class="{{ request()->routeIs('admin.invoices.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.invoices.index') }}"
                                class="{{ request()->routeIs('admin.invoices.*') ? 'active-link' : '' }}">
                                Billing & Invoices
                            </a>
                        </li>
                    @endif


                </ul>
            </li>
        @endif

        <!-- Settings -->
        @if ($user->hasPermission('view_settings'))
            <li
                class="has-submenu {{ request()->routeIs('admin.departments.*') ||
                request()->routeIs('admin.services.*') ||
                request()->routeIs('admin.notifications.*') ||
                request()->routeIs('admin.settings.*')
                    ? 'active'
                    : '' }}">
                <a href="#" class="waves-effect">
                    <i class="fas fa-cogs"></i>
                    Settings
                </a>
                <ul
                    class="submenu {{ request()->routeIs('admin.departments.*') ||
                    request()->routeIs('admin.services.*') ||
                    request()->routeIs('admin.notifications.*') ||
                    request()->routeIs('admin.rooms.*') ||
                    request()->routeIs('admin.settings.*')
                        ? 'show'
                        : '' }}">

                    @if ($user->hasPermission('manage_departments'))
                        <li class="{{ request()->routeIs('admin.departments.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.departments.index') }}"
                                class="{{ request()->routeIs('admin.departments.*') ? 'active-link' : '' }}">
                                Department Setup
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_rooms'))
                        <li class="{{ request()->routeIs('admin.rooms.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.rooms.index') }}"
                                class="{{ request()->routeIs('admin.rooms.*') ? 'active-link' : '' }}">
                                Room / Ward
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_services'))
                        <li class="{{ request()->routeIs('admin.services.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.services.index') }}"
                                class="{{ request()->routeIs('admin.services.*') ? 'active-link' : '' }}">
                                Service Pricing
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_notifications'))
                        <li class="{{ request()->routeIs('admin.notifications.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.notifications.index') }}"
                                class="{{ request()->routeIs('admin.notifications.*') ? 'active-link' : '' }}">
                                Notification Templates
                            </a>
                        </li>
                    @endif

                    @if ($user->hasPermission('manage_system_settings'))
                        <li class="{{ request()->routeIs('admin.settings.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.settings.index') }}"
                                class="{{ request()->routeIs('admin.settings.*') ? 'active-link' : '' }}">
                                System Settings
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif


        @if ($user->hasPermission('manage_banner'))
            <li class="{{ request()->routeIs('admin.banner*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.banner.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.banner*') ? 'active-link' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Banner
                </a>
            </li>
        @endif

        @if ($user->hasPermission('manage_health_tips'))
            <li class="{{ request()->routeIs('admin.healthtips*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.healthtips.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.healthtips*') ? 'active-link' : '' }}">
                    <i class="fas fa-first-aid"></i>
                    Health Tips
                </a>
            </li>
        @endif

        <!-- Role Management (Only for admin) -->
        @if ($user->hasPermission('manage_roles') || $user->isAdmin())
            <li class="{{ request()->routeIs('admin.roles.*') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.roles.index') }}"
                    class="waves-effect {{ request()->routeIs('admin.roles.*') ? 'active-link' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    Role & Permissions
                </a>
            </li>
        @endif

        <!-- Logout -->
        <li>
            <a href="{{ route('admin.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</div>

<script>
    // Dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const submenuParents = document.querySelectorAll('.has-submenu');

        submenuParents.forEach(parent => {
            const link = parent.querySelector('a');

            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                    parent.classList.toggle('active');

                    const submenu = parent.querySelector('.submenu');
                    if (submenu) {
                        submenu.classList.toggle('show');
                    }
                }
            });
        });
    });
</script>
<!-- Left Sidebar End -->
