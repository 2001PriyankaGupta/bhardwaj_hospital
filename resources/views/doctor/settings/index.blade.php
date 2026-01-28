@extends('admin.layouts.master')


@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .swal2-toast {
            font-size: 12px !important;
            padding: 6px 10px !important;
            min-width: auto !important;
            width: 220px !important;
            line-height: 1.3em !important;
        }

        .swal2-toast .swal2-icon {
            width: 24px !important;
            height: 24px !important;
            margin-right: 6px !important;
        }

        .swal2-toast .swal2-title {
            font-size: 13px !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 45px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: var(--primary-color);
            color: white;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Fix for modal positioning */
        .select2-container--open .select2-dropdown--below {
            z-index: 1060;
        }

        :root {
            --primary-color: #249722;
            --primary-light: #e8f5e8;
            --primary-dark: #1e7a1c;
        }

        .settings-nav .nav-link {
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .settings-nav .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .settings-nav .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .settings-tab-pane {
            display: none;
        }

        .settings-tab-pane.active {
            display: block;
        }

        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .api-key-display {
            background-color: #f8f9fa;
            border: 1px dashed #dee2e6;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-family: monospace;
            font-size: 0.875rem;
        }

        .permission-group {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .permission-group-header {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }

        .permission-items {
            padding: 1rem;
        }

        .logo-preview {
            max-width: 150px;
            max-height: 50px;
            object-fit: contain;
        }

        .swal2-toast {
            font-size: 12px !important;
            padding: 6px 10px !important;
            min-width: auto !important;
            width: 220px !important;
            line-height: 1.3em !important;
        }

        .swal2-toast .swal2-icon {
            width: 24px !important;
            height: 24px !important;
            margin-right: 6px !important;
        }

        .swal2-toast .swal2-title {
            font-size: 13px !important;
        }
    </style>
@endsection
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@section('content')

    <body class="bg-gray-100">
        <div class="min-h-screen">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
                        <div class="flex space-x-4">
                            <button type="submit" form="settingsForm"
                                class="btn text-white font-bold py-2 px-4 rounded btn-primary">
                                <i class="fas fa-save mr-2"></i>Save All Changes
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


                <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Sidebar - Navigation -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-lg shadow">
                                <nav class="p-6">
                                    <ul class="space-y-2">
                                        <li>
                                            <a href="#general"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-cog mr-3"></i>
                                                General Settings
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#users"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-users mr-3"></i>
                                                User Management
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#backup"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-database mr-3"></i>
                                                Backup & Restore
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#logs"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-clipboard-list mr-3"></i>
                                                System Logs
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#security"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-shield-alt mr-3"></i>
                                                Security Settings
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Right Content Area -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- General Settings -->
                            <div id="general" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-cog mr-2"></i>General System Configuration
                                    </h2>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                            <input type="text" name="site_name"
                                                value="{{ old('site_name', $settings['general']['site_name']->value ?? 'My Site') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                                            <input type="email" name="site_email"
                                                value="{{ old('site_email', $settings['general']['site_email']->value ?? 'admin@example.com') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                                        <textarea name="site_description" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('site_description', $settings['general']['site_description']->value ?? 'Welcome to our website') }}</textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                            <select name="timezone"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="UTC">UTC</option>
                                                <option value="Asia/Kolkata">India (IST)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance
                                                Mode</label>
                                            <select name="maintenance_mode"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="0">Disabled</option>
                                                <option value="1">Enabled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Management -->
                            <div id="users" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-users mr-2"></i>User Management
                                    </h2>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4">
                                        @foreach ($users as $user)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                                        <input type="text" name="users[{{ $user->id }}][name]"
                                                            value="{{ $user->name }}"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                                        <input type="email" name="users[{{ $user->id }}][email]"
                                                            value="{{ $user->email }}"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">New
                                                            Password</label>
                                                        <input type="password" name="users[{{ $user->id }}][password]"
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                                            placeholder="Leave blank to keep current">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Backup & Restore -->
                            <div id="backup" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-database mr-2"></i>Backup & Restore
                                    </h2>
                                </div>
                                <div class="p-6">
                                    <!-- Create Backup -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Backup</h3>
                                        <form action="{{ route('admin.settings.backup.create') }}" method="POST"
                                            class="flex gap-4">
                                            @csrf
                                            <input type="text" name="notes" placeholder="Backup notes..."
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md">
                                            <button type="submit"
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                <i class="fas fa-plus mr-2"></i>Create Backup
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Existing Backups -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Existing Backups</h3>
                                        <div class="space-y-3">
                                            @foreach ($backups as $backup)
                                                <div
                                                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                                    <div>
                                                        <div class="font-medium">{{ $backup->filename }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $backup->created_at->format('M d, Y H:i') }} •
                                                            {{ $backup->size_formatted }}
                                                        </div>
                                                    </div>
                                                    <div class="flex space-x-2">
                                                        <form
                                                            action="{{ route('admin.settings.backup.restore', $backup->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button type="submit"
                                                                class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                                                <i class="fas fa-redo mr-1"></i>Restore
                                                            </button>
                                                        </form>
                                                        <button
                                                            class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                                            <i class="fas fa-trash mr-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Logs -->
                            <div id="logs" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-clipboard-list mr-2"></i>System Logs
                                    </h2>
                                    <form action="{{ route('admin.settings.logs.clear') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            <i class="fas fa-trash mr-2"></i>Clear Old Logs
                                        </button>
                                    </form>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-3 max-h-96 overflow-y-auto">
                                        @foreach ($logs as $log)
                                            <div class="p-3 border border-gray-200 rounded-lg">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span
                                                        class="font-medium {{ $log->level == 'error' ? 'text-red-600' : ($log->level == 'warning' ? 'text-yellow-600' : 'text-green-600') }}">
                                                        {{ strtoupper($log->level) }}
                                                    </span>
                                                    <span
                                                        class="text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                                                </div>
                                                <div class="text-sm">{{ $log->message }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    IP: {{ $log->ip_address }} • User: {{ $log->user_id ?? 'System' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Security Settings -->
                            <div id="security" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-shield-alt mr-2"></i>Security Settings
                                    </h2>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Login
                                                Attempts</label>
                                            <input type="number" name="max_login_attempts"
                                                value="{{ old('max_login_attempts', $settings['security']['max_login_attempts']->value ?? '5') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout
                                                (minutes)</label>
                                            <input type="number" name="session_timeout"
                                                value="{{ old('session_timeout', $settings['security']['session_timeout']->value ?? '120') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Enable Two-Factor
                                                Auth</label>
                                            <select name="two_factor_auth"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                                <option value="0">Disabled</option>
                                                <option value="1">Enabled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Expiry
                                                (days)</label>
                                            <input type="number" name="password_expiry"
                                                value="{{ old('password_expiry', $settings['security']['password_expiry']->value ?? '90') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function() {
                @if (session('success'))
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: "{{ session('success') }}",
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: "{{ session('error') }}",
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                @endif
            });
            // Smooth scrolling for navigation
            document.querySelectorAll('.settings-nav').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Active navigation highlighting
            const sections = document.querySelectorAll('.settings-section');
            const navLinks = document.querySelectorAll('.settings-nav');

            window.addEventListener('scroll', () => {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (scrollY >= (sectionTop - 100)) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('bg-blue-50', 'text-blue-600');
                    if (link.getAttribute('href') === '#' + current) {
                        link.classList.add('bg-blue-50', 'text-blue-600');
                    }
                });
            });
        </script>
    @endsection
