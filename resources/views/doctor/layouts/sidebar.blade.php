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
        cursor: pointer;
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

    /* Doctor Profile Section */
    .doctor-profile {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .doctor-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .doctor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doctor-info h5 {
        color: white;
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }

    .doctor-info p {
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        font-size: 12px;
    }

    .doctor-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        margin-top: 3px;
    }

    /* Badge for notifications */
    .menu-badge {
        background: #e74c3c;
        color: white;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
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

        .doctor-profile {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }
    }
</style>

<div class="vertical-menu">

    <ul>

        <!-- Dashboard -->
        <li class="{{ request()->routeIs('doctor.dashboard*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.dashboard') }}"
                class="waves-effect {{ request()->routeIs('doctor.dashboard*') ? 'active-link' : '' }}">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
        </li>



        <li class="{{ request()->routeIs('doctor.appointments.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.appointments.index') }}"
                class="waves-effect {{ request()->routeIs('doctor.appointments.*') ? 'active-link' : '' }}">
                <i class="fas fa-calendar-check"></i>
                Appointments
            </a>
        </li>



        <li class="{{ request()->routeIs('doctor.medical-reports.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.medical-reports.index') }}"
                class="{{ request()->routeIs('doctor.medical-reports.*') ? 'active-link' : '' }}">
                <i class="fas fa-file-medical-alt text-white"></i> Medical Reports
            </a>
        </li>

        <li class="{{ request()->routeIs('doctor.prescriptions.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.prescriptions.index') }}"
                class="{{ request()->routeIs('doctor.prescriptions.*') ? 'active-link' : '' }}">
                <i class="fas fa-prescription text-white"></i> Prescriptions
            </a>
        </li>

        {{-- <li class="{{ request()->routeIs('doctor.doctors.index') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.doctors.index') }}"
                class="{{ request()->routeIs('doctor.doctors.index') ? 'active-link' : '' }}">
                <i class="fas fa-user-clock text-white"></i> Profile Details
            </a>
        </li>

        @php $doctor = Auth::user()->doctor_id ?? Auth::user()->doctor->id; @endphp

        <li class="{{ request()->routeIs('doctor.doctors.schedules.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.doctors.schedules', $doctor) }}"
                class="{{ request()->routeIs('doctor.doctors.schedules.*') ? 'active-link' : '' }}">
                <i class="fas fa-calendar-alt text-white"></i> Schedule Management
            </a>
        </li>
        <li class="{{ request()->routeIs('doctor.doctors.leaves.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.doctors.leaves', $doctor) }}"
                class="{{ request()->routeIs('doctor.doctors.leaves.*') ? 'active-link' : '' }}">
                <i class="fas fa-calendar-times text-white"></i> Leave Applications
            </a>
        </li> --}}

        <li class="has-submenu
{{ request()->routeIs('doctor.doctors.*') ? 'mm-active active' : '' }}">

            <a href="#">
                <i class="fas fa-user-md"></i>
                My Profile
            </a>

            <ul class="submenu
    {{ request()->routeIs('doctor.doctors.*') ? 'show' : '' }}">

                <li class="{{ request()->routeIs('doctor.doctors.index.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('doctor.doctors.index') }}"
                        class="{{ request()->routeIs('doctor.doctors.index') ? 'active-link' : '' }}">
                        <i class="fas fa-id-card"></i>
                        Profile Details
                    </a>
                </li>

                @php $doctor = Auth::user()->doctor_id ?? Auth::user()->doctor->id; @endphp

                <li class="{{ request()->routeIs('doctor.doctors.schedules.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('doctor.doctors.schedules', $doctor) }}"
                        class="{{ request()->routeIs('doctor.doctors.schedules.*') ? 'active-link' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Slot Management
                    </a>
                </li>

                <li class="{{ request()->routeIs('doctor.doctors.leaves.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('doctor.doctors.leaves', $doctor) }}"
                        class="{{ request()->routeIs('doctor.doctors.leaves.*') ? 'active-link' : '' }}">
                        <i class="fas fa-calendar-times"></i>
                        Leave Applications
                    </a>
                </li>

            </ul>
        </li>




        <li class="{{ request()->routeIs('doctor.emergency.*') ? 'mm-active' : '' }}">
            <a href="{{ route('doctor.emergency.index') }}"
                class="{{ request()->routeIs('doctor.emergency.*') ? 'active-link' : '' }}">
                <i class="fas fa-ambulance"></i> Emergency Case
            </a>
        </li>



        {{-- <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
            <form id="logout-form" action="#" method="POST" style="display: none;">
                @csrf
            </form>
        </li> --}}
    </ul>
</div>

<script>
    // Dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
        const submenuParents = document.querySelectorAll('.has-submenu');

        // Set active menu based on current URL
        function setActiveMenu() {
            const currentPath = window.location.pathname;
            // You can add logic here to set active menu based on URL
        }

        // Set active menu on page load
        setActiveMenu();

        submenuParents.forEach(parent => {
            const link = parent.querySelector('a');

            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();

                    // Close other open dropdowns
                    submenuParents.forEach(otherParent => {
                        if (otherParent !== parent && otherParent.classList.contains(
                                'active')) {
                            otherParent.classList.remove('active');
                            const otherSubmenu = otherParent.querySelector('.submenu');
                            if (otherSubmenu) {
                                otherSubmenu.classList.remove('show');
                            }
                        }
                    });

                    // Toggle current dropdown
                    parent.classList.toggle('active');
                    const submenu = parent.querySelector('.submenu');
                    if (submenu) {
                        submenu.classList.toggle('show');
                    }
                }
            });
        });

        // Handle menu item clicks
        const menuItems = document.querySelectorAll('.vertical-menu a');
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') return;

                // Remove active class from all items
                menuItems.forEach(i => {
                    i.classList.remove('active-link');
                    i.parentElement.classList.remove('mm-active');
                });

                // Add active class to clicked item
                this.classList.add('active-link');
                this.parentElement.classList.add('mm-active');

                // Close all submenus
                submenuParents.forEach(parent => {
                    parent.classList.remove('active');
                    const submenu = parent.querySelector('.submenu');
                    if (submenu) {
                        submenu.classList.remove('show');
                    }
                });

                // If clicked item is in submenu, also activate parent
                const parentLi = this.closest('.submenu');
                if (parentLi) {
                    const parentItem = parentLi.closest('.has-submenu');
                    if (parentItem) {
                        parentItem.classList.add('active');
                        parentItem.querySelector('.submenu').classList.add('show');
                    }
                }
            });
        });
    });
</script>
<!-- Left Sidebar End -->
