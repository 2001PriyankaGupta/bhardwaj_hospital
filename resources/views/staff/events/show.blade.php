@extends('staff.layouts.master')

@section('title', 'View Event')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary-color: #ff4900;
        --secondary-color: #2c3e50;
        --success-color: #2ecc71;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-color: #dee2e6;
        --radius: 8px;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Main Container */
    .event-view-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header Section */
    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--primary-color);
    }

    .header-content {
        flex: 1;
    }

    .event-title {
        font-size: 28px;
        color: var(--secondary-color);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .event-title i {
        color: var(--primary-color);
    }

    .event-subtitle {
        color: #666;
        font-size: 16px;
        margin: 0;
    }

    .event-status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-upcoming {
        background: #3498db;
        color: white;
    }

    .status-ongoing {
        background: #2ecc71;
        color: white;
    }

    .status-completed {
        background: #95a5a6;
        color: white;
    }

    .status-cancelled {
        background: #e74c3c;
        color: white;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--secondary-color);
        color: white;
        text-decoration: none;
        border-radius: var(--radius);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-back:hover {
        background: var(--primary-color);
        transform: translateX(-3px);
    }

    /* Main Content */
    .event-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .event-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 30px;
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 20px;
        color: var(--secondary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: var(--primary-color);
    }

    /* Event Image */
    .event-image-section {
        width: 100%;
    }

    .event-image {
        width: 100%;
        height: 300px;
        border-radius: var(--radius);
        overflow: hidden;
        background: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .event-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-placeholder {
        text-align: center;
        color: #999;
    }

    .image-placeholder i {
        font-size: 64px;
        margin-bottom: 10px;
    }

    /* Event Details */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .detail-item {
        padding: 15px;
        background: var(--light-color);
        border-radius: var(--radius);
        border-left: 4px solid var(--primary-color);
    }

    .detail-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 16px;
        color: var(--secondary-color);
        font-weight: 500;
    }

    .detail-value .fa {
        margin-right: 8px;
        color: var(--primary-color);
        width: 20px;
    }

    /* Full Width Details */
    .full-width {
        grid-column: 1 / -1;
    }

    .description-box {
        padding: 20px;
        background: var(--light-color);
        border-radius: var(--radius);
        line-height: 1.6;
        color: var(--secondary-color);
    }

    /* Requirements */
    .requirements-list {
        padding: 20px;
        background: #fff9e6;
        border-radius: var(--radius);
        border-left: 4px solid var(--warning-color);
    }

    .requirements-list ul {
        margin: 0;
        padding-left: 20px;
    }

    .requirements-list li {
        margin-bottom: 8px;
        color: var(--secondary-color);
    }

    /* Contact Info */
    .contact-info {
        display: grid;
        gap: 15px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: white;
        border-radius: var(--radius);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }

    .contact-item:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }

    .contact-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .contact-details {
        flex: 1;
    }

    .contact-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .contact-value {
        font-size: 14px;
        color: var(--secondary-color);
        font-weight: 500;
    }

    /* Social Media */
    .social-links {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .social-facebook {
        background: #1877f2;
    }

    .social-twitter {
        background: #1da1f2;
    }

    .social-instagram {
        background: #e4405f;
    }

    .social-linkedin {
        background: #0a66c2;
    }

    /* Event Stats */
    .event-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 15px;
        background: var(--light-color);
        border-radius: var(--radius);
    }

    .stat-icon {
        font-size: 24px;
        color: var(--primary-color);
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Featured Badge */
    .featured-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #4CAF50;
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-edit,
    .btn-delete {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        text-decoration: none;
        border-radius: var(--radius);
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: var(--primary-color);
        color: white;
    }

    .btn-edit:hover {
        background: #2980b9;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: var(--danger-color);
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .event-content {
            grid-template-columns: 1fr;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .event-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .event-view-container {
            padding: 15px;
        }

        .event-card {
            padding: 20px;
        }

        .event-title {
            font-size: 24px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

@section('content')
    <div class="event-view-container">
        <!-- Header Section -->
        <div class="event-header">
            <div class="header-content">
                <h1 class="event-title">
                    <i class="fas fa-calendar-alt"></i>
                    {{ $event->title }}
                    @if ($event->is_featured)
                        <span class="featured-badge">
                            <i class="fas fa-star" style="color: white!important;"></i> Featured
                        </span>
                    @endif
                    <span class="event-status-badge status-{{ $event->status }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </h1>
                <p class="event-subtitle">
                    {{ ucfirst(str_replace('_', ' ', $event->type)) }} •
                    {{ $event->event_date->format('F d, Y') }} •
                    {{ date('g:i A', strtotime($event->start_time)) }} - {{ date('g:i A', strtotime($event->end_time)) }}
                </p>
            </div>
            <a href="{{ route('staff.events.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Events
            </a>
        </div>

        <div class="event-content">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Event Image -->
                <div class="event-card event-image-section">
                    <h3 class="card-title"><i class="fas fa-image"></i> Event Image</h3>
                    <div class="event-image">
                        @if ($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-calendar-alt"></i>
                                <p>No image available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Event Details -->
                <div class="event-card">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Event Details</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Event Type</div>
                            <div class="detail-value">
                                <i class="fas fa-tag"></i>
                                {{ ucfirst(str_replace('_', ' ', $event->type)) }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Date & Time</div>
                            <div class="detail-value">
                                <i class="fas fa-calendar"></i>
                                {{ $event->event_date->format('F d, Y') }}
                                <br>
                                <i class="fas fa-clock"></i>
                                {{ date('g:i A', strtotime($event->start_time)) }} -
                                {{ date('g:i A', strtotime($event->end_time)) }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Venue</div>
                            <div class="detail-value">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $event->venue }}
                            </div>
                        </div>

                        @if ($event->address)
                            <div class="detail-item">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">
                                    <i class="fas fa-location-arrow"></i>
                                    {{ $event->address }}
                                </div>
                            </div>
                        @endif

                        <div class="detail-item full-width">
                            <div class="detail-label">Description</div>
                            <div class="description-box">
                                {{ $event->description }}
                            </div>
                        </div>

                        @if ($event->requirements)
                            <div class="detail-item full-width">
                                <div class="detail-label">Special Requirements</div>
                                <div class="requirements-list">
                                    {!! nl2br($event->requirements) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Contact Information -->
                <div class="event-card">
                    <h3 class="card-title"><i class="fas fa-address-book"></i> Contact Information</h3>
                    <div class="contact-info">
                        @if ($event->organizer)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Organizer</div>
                                    <div class="contact-value">{{ $event->organizer }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($event->contact_person)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Contact Person</div>
                                    <div class="contact-value">{{ $event->contact_person }}</div>
                                </div>
                            </div>
                        @endif

                        @if ($event->contact_number)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Contact Number</div>
                                    <div class="contact-value">
                                        <a href="tel:{{ $event->contact_number }}">{{ $event->contact_number }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($event->email)
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-details">
                                    <div class="contact-label">Email</div>
                                    <div class="contact-value">
                                        <a href="mailto:{{ $event->email }}">{{ $event->email }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Event Statistics -->
                <div class="event-card">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Event Statistics</h3>
                    <div class="event-stats">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value">{{ $event->target_participants ?? '0' }}</div>
                            <div class="stat-label">Target Participants</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value">{{ ucfirst($event->status) }}</div>
                            <div class="stat-label">Status</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-value">{{ $event->is_published ? 'Published' : 'Draft' }}</div>
                            <div class="stat-label">Visibility</div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value">
                                @php
                                    $daysLeft = now()->diffInDays($event->event_date, false);
                                @endphp
                                @if ($daysLeft > 0)
                                    {{ $daysLeft }} days
                                @elseif($daysLeft == 0)
                                    Today
                                @else
                                    Past
                                @endif
                            </div>
                            <div class="stat-label">
                                @if ($daysLeft > 0)
                                    Days Left
                                @elseif($daysLeft == 0)
                                    Event Day
                                @else
                                    Days Ago
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                @if ($event->facebook_url || $event->twitter_url || $event->instagram_url || $event->linkedin_url)
                    <div class="event-card">
                        <h3 class="card-title"><i class="fas fa-share-alt"></i> Social Media</h3>
                        <div class="social-links">
                            @if ($event->facebook_url)
                                <a href="{{ $event->facebook_url }}" target="_blank"
                                    class="social-link social-facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif

                            @if ($event->twitter_url)
                                <a href="{{ $event->twitter_url }}" target="_blank" class="social-link social-twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif

                            @if ($event->instagram_url)
                                <a href="{{ $event->instagram_url }}" target="_blank"
                                    class="social-link social-instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif

                            @if ($event->linkedin_url)
                                <a href="{{ $event->linkedin_url }}" target="_blank"
                                    class="social-link social-linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection
