<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">

            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="#" target="_blank" class="logo logo-dark">
                    <h3 style="color: white;margin-top: 9px; font-size: 20px; font-weight: 700;">Bhardwaj Hospital</h3>
                    <p style="color: rgba(255, 255, 255, 0.8);margin: 5px 0 0 0; font-size: 12px;">Management System</p>
                </a>


            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="mdi mdi-menu"></i>
            </button>


        </div>

        <!-- Search input -->
        <div class="search-wrap" id="search-wrap">
            <div class="search-bar">
                <input class="search-input form-control" placeholder="Search" />
                <a href="#" class="close-search toggle-search" data-target="#search-wrap">
                    <i class="mdi mdi-close-circle"></i>
                </a>
            </div>
        </div>

        <div class="d-flex">
            <div class="dropdown d-none d-lg-inline-block">
                <button type="button" class="btn header-item toggle-search noti-icon waves-effect"
                    data-target="#search-wrap">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>


            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="mdi mdi-fullscreen"></i>
                </button>
            </div>

            @php
                $unreadNotifications = \App\Models\Notification::where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->latest()
                    ->take(5)
                    ->get();
                $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->count();
                $unreadChatCount = \App\Models\ChatMessage::where('sender_type', 'patient')
                    ->whereNull('read_at')
                    ->count();
            @endphp
 
            <div class="dropdown d-inline-block">
                <a href="{{ route('admin.chat.dashboard') }}" class="btn header-item noti-icon waves-effect" style="padding-top: 14px;">
                    <i class="mdi mdi-chat-processing-outline"></i>
                    @if($unreadChatCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadChatCount }}</span>
                    @endif
                </a>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-bell-outline"></i>
                    @if($unreadCount > 0)
                        <span class="badge bg-orange rounded-pill" style="background-color: #ff4900 !important;  ">{{ $unreadCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg border-0"
                    aria-labelledby="page-header-notifications-dropdown" style="border-radius: 12px; overflow: hidden;">
                    <div class="p-3 bg-orange-soft" style="background-color: rgba(255, 73, 0, 0.05);">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fw-bold text-orange"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.admin-notifications.index') }}" class="small text-orange fw-bold" style="color:#ff4900 !important;"> View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                        @forelse($unreadNotifications as $notification)
                            @php $meta = is_string($notification->meta_data) ? json_decode($notification->meta_data, true) : $notification->meta_data; @endphp
                            <a href="{{ route('admin.admin-notifications.index') }}" class="text-reset notification-item d-block p-3 border-bottom-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-orange rounded-circle font-size-16" style="background-color: #ff4900 !important; color: white !important;">
                                                <i class="mdi mdi-bell-ring-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-size-14">{{ $notification->title }}</h6>
                                        <div class="font-size-12 text-muted">
                                            <p class="mb-1 text-truncate" style="max-width: 200px;">{{ $meta['message'] ?? 'New notification' }}</p>
                                            <p class="mb-0 small"><i class="mdi mdi-clock-outline me-1"></i> {{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center">
                                <i class="mdi mdi-bell-off-outline d-block font-size-24 text-muted mb-2"></i>
                                <p class="mb-0 text-muted small">No new notifications</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top d-grid bg-light">
                        <form action="{{ route('admin.admin-notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-link font-size-14 text-center w-100  fw-bold text-decoration-none" style="color:#ff4900 !important;">
                                <i class="mdi mdi-check-all me-1"></i> Mark all as read
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                    <img src="{{ Auth::user()->profile_picture ? asset(Auth::user()->profile_picture) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=500&q=80' }}"
                        alt="Header Avatar" class="rounded-circle header-profile-user">

                    <span class="d-none d-xl-inline-block ms-1">
                        {{ Auth::user()->name }}
                    </span>

                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <a class="dropdown-item" href="{{ route('admin.profile') }}">
                        <i class="mdi mdi-account-circle-outline font-size-16 align-middle me-1"></i>
                        Profile
                    </a>


                    {{-- <a class="dropdown-item d-block" href=""><span
                            class="badge badge-success float-end">11</span><i
                            class="mdi mdi-cog-outline font-size-16 align-middle me-1"></i> Settings</a> --}}
                    <div class="dropdown-divider"></div>

                    @php
                        $user_type = Auth::user()->user_type;
                    @endphp
                    <a class="dropdown-item text-danger" href="{{ route($user_type . '.logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-power font-size-16 align-middle me-1 text-danger"></i> Logout
                    </a>

                    <form id="logout-form" action="{{ route($user_type . '.logout') }}" method="GET"
                        style="display: none;">
                        @csrf
                    </form>

                </div>
            </div>

            {{-- <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="mdi mdi-cog-outline font-size-20"></i>
                </button>
            </div> --}}

        </div>
    </div>
</header>


