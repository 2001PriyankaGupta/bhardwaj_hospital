@extends('staff.layouts.master')

@section('title', 'Event Management')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

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

    /* Main Container */
    .page-container {
        padding: 30px;
        background-color: #f8fafc;
        min-height: 100vh;
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Header Styles */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-content {
        flex: 1;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .page-subtitle {
        font-size: 16px;
        color: #718096;
        margin: 0;
    }

    .btn-create-event {
        background-color: #ff6500;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.1);
    }

    .btn-create-event:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.2);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-left: 4px solid;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card-blue {
        border-left-color: #4299e1;
    }

    .stat-card-red {
        border-left-color: #f56565;
    }

    .stat-card-yellow {
        border-left-color: #ecc94b;
    }

    .stat-card-purple {
        border-left-color: #9f7aea;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-card-blue .stat-icon {
        background-color: #ebf8ff;
        color: #4299e1;
    }

    .stat-card-red .stat-icon {
        background-color: #fff5f5;
        color: #f56565;
    }

    .stat-card-yellow .stat-icon {
        background-color: #fffff0;
        color: #ecc94b;
    }

    .stat-card-purple .stat-icon {
        background-color: #faf5ff;
        color: #9f7aea;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #718096;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    /* Filter Container */
    .filter-container {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    @media (min-width: 768px) {
        .filter-form {
            flex-direction: row;
            align-items: center;
        }
    }

    .search-box {
        flex: 1;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px;
        padding-right: 50px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
    }

    .search-button {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: #667eea;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .search-button:hover {
        background: #5a67d8;
    }

    .filter-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .filter-select {
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        font-size: 14px;
        min-width: 150px;
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: #667eea;
    }

    .btn-filter {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.3s ease;
    }

    .btn-filter:hover {
        background: #5a67d8;
    }

    .btn-reset {
        background: white;
        color: #718096;
        border: 2px solid #e2e8f0;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
    }

    /* Table Styles */
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 25px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .events-table {
        width: 100%;
        border-collapse: collapse;
    }

    .events-table thead {
        background: #efeff0;
        border-bottom: 2px solid #e2e8f0;
    }

    .events-table th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .events-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .event-row {
        transition: background 0.3s ease;
    }

    .event-row:hover {
        background: #f8fafc;
    }

    .event-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .event-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }

    .event-image-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background-color: #ff6500;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .event-details {
        flex: 1;
    }

    .event-title-container {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }

    .event-title {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    .featured-badge {
        background: #fef3c7;
        color: #d97706;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .event-venue {
        font-size: 13px;
        color: #718096;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .event-organizer {
        font-size: 12px;
        color: #a0aec0;
        margin: 0;
    }

    /* Event Type Badges */
    .event-type {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .type-blood {
        background: #fed7d7;
        color: #c53030;
    }

    .type-health {
        background: #c6f6d5;
        color: #276749;
    }

    .type-seminar {
        background: #bee3f8;
        color: #2c5282;
    }

    .type-workshop {
        background: #feebc8;
        color: #c05621;
    }

    .type-awareness {
        background: #e9d8fd;
        color: #553c9a;
    }

    .type-vaccine {
        background: #b2f5ea;
        color: #234e52;
    }

    .type-other {
        background: #e2e8f0;
        color: #4a5568;
    }

    /* Event Date & Time */
    .event-date {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .event-time {
        font-size: 13px;
        color: #718096;
        margin-bottom: 8px;
    }

    .participant-count {
        font-size: 12px;
        color: #a0aec0;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 4px;
    }

    .participant-progress {
        width: 100px;
    }

    .progress-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px;
        width: 0;
        transition: width 1s ease;
    }

    /* Event Status */
    .event-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .status-upcoming {
        background: #bee3f8;
        color: #2c5282;
    }

    .status-ongoing {
        background: #c6f6d5;
        color: #276749;
    }

    .status-completed {
        background: #e2e8f0;
        color: #4a5568;
    }

    .status-cancelled {
        background: #fed7d7;
        color: #c53030;
    }

    .status-selector {
        display: block;
        width: 100%;
        padding: 8px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        background: white;
        cursor: pointer;
    }

    .status-selector:focus {
        outline: none;
        border-color: #667eea;
    }

    /* Social Links */
    .social-links {
        display: flex;
        gap: 8px;
    }

    .social-link {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .social-link:hover {
        transform: translateY(-2px);
    }

    .facebook {
        background: #3b5998;
    }

    .twitter {
        background: #1da1f2;
    }

    .instagram {
        background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d);
    }

    .linkedin {
        background: #0077b5;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        color: white;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .view-btn {
        background: #4299e1;
    }

    .edit-btn {
        background: #38b2ac;
    }

    .feature-btn {
        background: #ecc94b;
    }

    .publish-btn {
        background: #9f7aea;
    }

    .delete-btn {
        background: #f56565;
    }

    .delete-form {
        display: inline;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 48px;
        color: #cbd5e0;
        margin-bottom: 16px;
    }

    .empty-state h3 {
        font-size: 20px;
        color: #4a5568;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #a0aec0;
        margin-bottom: 24px;
    }

    .btn-create-event-empty {
        background: #667eea;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-create-event-empty:hover {
        background: #5a67d8;
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid #e2e8f0;
        background: #f7fafc;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .page-container {
            padding: 20px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filter-controls {
            flex-direction: column;
            width: 100%;
        }

        .filter-select {
            width: 100%;
        }

        .btn-filter,
        .btn-reset {
            width: 100%;
            justify-content: center;
        }

        .action-buttons {
            flex-wrap: wrap;
            justify-content: center;
        }

        .events-table td,
        .events-table th {
            padding: 12px 8px;
        }
    }
</style>

@section('content')
    <div class="page-container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">Event Management</h1>
                <p class="page-subtitle">Manage all hospital events, blood donation camps, and awareness programs</p>
            </div>
            <a href="{{ route('staff.events.create') }}" class="btn-create-event">
                <i class="fas fa-plus"></i> Create New Event
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card-blue">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Events</p>
                    <h3 class="stat-value">{{ $events->total() }}</h3>
                </div>
            </div>

            <div class="stat-card stat-card-red">
                <div class="stat-icon">
                    <i class="fas fa-tint"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Blood Donation</p>
                    <h3 class="stat-value">{{ \App\Models\Event::where('type', 'blood_donation')->count() }}</h3>
                </div>
            </div>

            <div class="stat-card stat-card-yellow">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Featured</p>
                    <h3 class="stat-value">{{ \App\Models\Event::where('is_featured', true)->count() }}</h3>
                </div>
            </div>

            <div class="stat-card stat-card-purple">
                <div class="stat-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Upcoming</p>
                    <h3 class="stat-value">{{ \App\Models\Event::where('status', 'upcoming')->count() }}</h3>
                </div>
            </div>
        </div>

        {{-- <!-- Filter and Search -->
        <div class="filter-container">
            <form method="GET" action="{{ route('admin.events.index') }}" class="filter-form">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search events by title, description, or venue..." class="search-input">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="filter-controls">
                    <select name="type" class="filter-select">
                        <option value="all">All Types</option>
                        <option value="blood_donation" {{ request('type') == 'blood_donation' ? 'selected' : '' }}>Blood
                            Donation</option>
                        <option value="health_camp" {{ request('type') == 'health_camp' ? 'selected' : '' }}>Health Camp
                        </option>
                        <option value="seminar" {{ request('type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="workshop" {{ request('type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="awareness_program" {{ request('type') == 'awareness_program' ? 'selected' : '' }}>
                            Awareness Program</option>
                        <option value="vaccination_drive" {{ request('type') == 'vaccination_drive' ? 'selected' : '' }}>
                            Vaccination Drive</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>

                    <select name="status" class="filter-select">
                        <option value="all">All Status</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>

                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('admin.events.index') }}" class="btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div> --}}

        <!-- Events Table -->
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="events-table" id="eventsTable">
                    <thead>
                        <tr>
                            <th>Event Details</th>
                            <th>Type</th>
                            <th style="width: 166.05px;">Date & Time</th>
                            <th>Status</th>
                            <th>Social Media</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr class="event-row">
                                <td>
                                    <div class="event-info">

                                        <div class="event-details">
                                            <div class="event-title-container">
                                                <h4 class="event-title">
                                                    {{ \Illuminate\Support\Str::limit($event->title, 40) }}
                                                </h4>
                                                @if ($event->is_featured)
                                                    <span class="featured-badge">
                                                        <i class="fas fa-star"></i> Featured
                                                    </span>
                                                @endif
                                            </div>


                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @php
                                        $typeColors = [
                                            'blood_donation' => 'type-blood',
                                            'health_camp' => 'type-health',
                                            'seminar' => 'type-seminar',
                                            'workshop' => 'type-workshop',
                                            'awareness_program' => 'type-awareness',
                                            'vaccination_drive' => 'type-vaccine',
                                            'other' => 'type-other',
                                        ];
                                        $typeClass = $typeColors[$event->type] ?? 'type-other';

                                        $typeIcons = [
                                            'blood_donation' => 'tint',
                                            'health_camp' => 'heartbeat',
                                            'seminar' => 'chalkboard-teacher',
                                            'workshop' => 'tools',
                                            'awareness_program' => 'bullhorn',
                                            'vaccination_drive' => 'syringe',
                                            'other' => 'calendar-alt',
                                        ];
                                        $typeIcon = $typeIcons[$event->type] ?? 'calendar-alt';
                                    @endphp
                                    <span class="event-type {{ $typeClass }}">
                                        <i class="fas fa-{{ $typeIcon }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="event-date">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                                    </div>
                                    <div class="event-time">
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                                    </div>

                                </td>

                                <td>
                                    @php
                                        $statusColors = [
                                            'upcoming' => 'status-upcoming',
                                            'ongoing' => 'status-ongoing',
                                            'completed' => 'status-completed',
                                            'cancelled' => 'status-cancelled',
                                        ];
                                        $statusClass = $statusColors[$event->status] ?? 'status-upcoming';
                                    @endphp
                                    <span class="event-status {{ $statusClass }}">
                                        {{ ucfirst($event->status) }}
                                    </span>

                                    <select class="status-selector" data-id="{{ $event->id }}">
                                        <option value="upcoming" {{ $event->status == 'upcoming' ? 'selected' : '' }}>
                                            Upcoming</option>
                                        <option value="ongoing" {{ $event->status == 'ongoing' ? 'selected' : '' }}>
                                            Ongoing</option>
                                        <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </td>

                                <td>
                                    <div class="social-links">
                                        @if ($event->facebook_url)
                                            <a href="{{ $event->facebook_url }}" target="_blank"
                                                class="social-link facebook" title="Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        @endif
                                        @if ($event->twitter_url)
                                            <a href="{{ $event->twitter_url }}" target="_blank" class="social-link twitter"
                                                title="Twitter">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        @endif
                                        @if ($event->instagram_url)
                                            <a href="{{ $event->instagram_url }}" target="_blank"
                                                class="social-link instagram" title="Instagram">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        @endif
                                        @if ($event->linkedin_url)
                                            <a href="{{ $event->linkedin_url }}" target="_blank"
                                                class="social-link linkedin" title="LinkedIn">
                                                <i class="fab fa-linkedin-in"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('staff.events.show', $event) }}" class="action-btn view-btn"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.events.edit', $event) }}" class="action-btn edit-btn"
                                            title="Edit Event">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="action-btn feature-btn toggle-feature" data-id="{{ $event->id }}"
                                            title="{{ $event->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}">
                                            <i class="fas {{ $event->is_featured ? 'fa-star' : 'fa-star' }}"></i>
                                        </button>
                                        <button class="action-btn publish-btn toggle-publish" data-id="{{ $event->id }}"
                                            title="{{ $event->is_published ? 'Unpublish' : 'Publish' }}">
                                            <i class="fas {{ $event->is_published ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                        </button>
                                        <form action="{{ route('staff.events.destroy', $event) }}" method="POST"
                                            class="delete-form"
                                            onsubmit="return confirm('Are you sure you want to delete this event?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn" title="Delete Event">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                        <h3>No Events Found</h3>
                                        <p>Create your first event to get started</p>
                                        <a href="{{ route('staff.events.create') }}" class="btn-create-event-empty">
                                            <i class="fas fa-plus"></i> Create Event
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($events->hasPages())
                <div class="pagination-wrapper">
                    {{ $events->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let baseUrl = "{{ config('app.url') }}";
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

            $('#eventsTable').DataTable({
                pageLength: 5,
                order: [
                    [0, 'asc']
                ]
            });
        });
        // Initialize progress bars
        function initProgressBars() {
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const current = parseInt(bar.dataset.current) || 0;
                const target = parseInt(bar.dataset.target) || 1;
                const percentage = Math.min((current / target) * 100, 100);

                const fill = bar.querySelector('.progress-fill');
                if (fill) {
                    fill.style.width = percentage + '%';
                }
            });
        }

        // Status update function
        function updateEventStatus(eventId, status) {
            return new Promise((resolve, reject) => {
                fetch(`${baseUrl}/staff/events/${eventId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => resolve(data))
                    .catch(error => reject(error));
            });
        }

        // Toggle feature status
        function toggleFeature(eventId) {
            return new Promise((resolve, reject) => {
                fetch(`${baseUrl}/staff/events/${eventId}/toggle-feature`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => resolve(data))
                    .catch(error => reject(error));
            });
        }

        // Toggle publish status
        function togglePublish(eventId) {
            return new Promise((resolve, reject) => {
                fetch(`${baseUrl}/staff/events/${eventId}/toggle-publish`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => resolve(data))
                    .catch(error => reject(error));
            });
        }

        // Show success message
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#fff',
                color: '#374151'
            });
        }

        // Show error message
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                background: '#fff',
                color: '#374151'
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize progress bars
            initProgressBars();

            // Add animation delay to table rows
            document.querySelectorAll('.event-row').forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
            });

            // Status selector change event
            document.querySelectorAll('.status-selector').forEach(select => {
                select.addEventListener('change', function() {
                    const eventId = this.dataset.id;
                    const status = this.value;

                    updateEventStatus(eventId, status)
                        .then(response => {
                            showSuccess('Event status updated successfully');
                            // Reload page after delay
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            showError('Failed to update status');
                        });
                });
            });

            // Toggle feature button
            document.querySelectorAll('.toggle-feature').forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.dataset.id;

                    toggleFeature(eventId)
                        .then(response => {
                            if (response.is_featured) {
                                showSuccess('Event marked as featured');
                            } else {
                                showSuccess('Event removed from featured');
                            }
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            showError('Failed to update feature status');
                        });
                });
            });

            // Toggle publish button
            document.querySelectorAll('.toggle-publish').forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.dataset.id;

                    togglePublish(eventId)
                        .then(response => {
                            if (response.is_published) {
                                showSuccess('Event published successfully');
                            } else {
                                showSuccess('Event unpublished successfully');
                            }
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(error => {
                            showError('Failed to update publish status');
                        });
                });
            });

            // Confirm delete
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this event?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
