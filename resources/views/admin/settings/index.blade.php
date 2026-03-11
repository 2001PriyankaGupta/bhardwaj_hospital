@extends('admin.layouts.master')


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


@section('content')

    <body class="bg-gray-100 mt-4">
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
                                            <a href="#app_update"
                                                class="settings-nav flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                                <i class="fas fa-mobile-alt mr-3"></i>
                                                App Update Control
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

                            <!-- App Update Settings -->
                            <div id="app_update" class="settings-section bg-white rounded-lg shadow">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-800">
                                        <i class="fas fa-mobile-alt mr-2"></i>Mobile App Version Control
                                    </h2>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Latest App Version</label>
                                            <input type="text" name="latest_app_version"
                                                value="{{ old('latest_app_version', $settings['app_update']['latest_app_version']->value ?? '1.0.0') }}"
                                                placeholder="e.g. 1.0.1"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            <p class="text-xs text-gray-500 mt-1">If this is different from the app version, a popup will show.</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Play Store URL</label>
                                            <input type="text" name="play_store_url"
                                                value="{{ old('play_store_url', $settings['app_update']['play_store_url']->value ?? 'https://play.google.com/store/apps/details?id=com.bhardwaj.hospital') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Message</label>
                                        <textarea name="app_update_message" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('app_update_message', $settings['app_update']['app_update_message']->value ?? 'A new version of the app is available with latest features and improvements.') }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">This message will be shown to users in the update popup.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </body>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

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
