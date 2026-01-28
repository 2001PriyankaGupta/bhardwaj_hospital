@extends('admin.layouts.master')

@section('title', 'Edit Event')

<link rel="stylesheet" href="{{ asset('css/create-edit.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('content')
    <div class="form-container">
        <!-- Header Section -->
        <div class="form-header">
            <div class="header-content">
                <h1 class="form-title">
                    <i class="fas fa-edit"></i>
                    Edit Event
                </h1>
                <p class="form-subtitle">
                    Update event details
                </p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Events
            </a>
        </div>

        <!-- Main Form -->
        <div class="form-card">
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data"
                id="event-form" class="event-form">
                @csrf
                @method('PUT')

                <!-- Form Tabs -->
                <div class="form-tabs">
                    <button type="button" class="tab-button active" data-tab="basic">Basic Info</button>
                    <button type="button" class="tab-button" data-tab="details">Event Details</button>
                    <button type="button" class="tab-button" data-tab="contact">Contact Info</button>
                    <button type="button" class="tab-button" data-tab="social">Social Media</button>
                    <button type="button" class="tab-button" data-tab="settings">Settings</button>
                </div>

                <div class="tab-content active" id="tab-basic">
                    <div class="form-grid">
                        <!-- Event Title -->
                        <div class="form-group">
                            <label for="title" class="form-label">
                                Event Title <span class="required">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $event->title) }}"
                                class="form-control" placeholder="e.g., Annual Blood Donation Camp 2024" required>
                            <div class="form-hint">Enter a descriptive title for the event</div>
                            @error('title')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Type -->
                        <div class="form-group">
                            <label for="type" class="form-label">
                                Event Type <span class="required">*</span>
                            </label>
                            <div class="select-wrapper">
                                <select id="type" name="type" class="form-control" required>
                                    <option value="">Select Event Type</option>
                                    <option value="blood_donation"
                                        {{ old('type', $event->type) == 'blood_donation' ? 'selected' : '' }}>Blood Donation
                                        Camp</option>
                                    <option value="health_camp"
                                        {{ old('type', $event->type) == 'health_camp' ? 'selected' : '' }}>Health Checkup
                                        Camp</option>
                                    <option value="awareness"
                                        {{ old('type', $event->type) == 'awareness' ? 'selected' : '' }}>Awareness Program
                                    </option>
                                    <option value="seminar" {{ old('type', $event->type) == 'seminar' ? 'selected' : '' }}>
                                        Medical Seminar</option>
                                    <option value="workshop"
                                        {{ old('type', $event->type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                                    <option value="conference"
                                        {{ old('type', $event->type) == 'conference' ? 'selected' : '' }}>Conference
                                    </option>
                                    <option value="charity" {{ old('type', $event->type) == 'charity' ? 'selected' : '' }}>
                                        Charity Event</option>
                                    <option value="screening"
                                        {{ old('type', $event->type) == 'screening' ? 'selected' : '' }}>Health Screening
                                    </option>
                                </select>
                                <i class="select-icon fas fa-chevron-down"></i>
                            </div>
                            @error('type')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Description -->
                        <div class="form-group full-width">
                            <label for="description" class="form-label">
                                Description <span class="required">*</span>
                            </label>
                            <textarea id="description" name="description" rows="4" class="form-control"
                                placeholder="Describe the event details, objectives, and benefits..." required>{{ old('description', $event->description) }}</textarea>
                            <div class="form-hint">Provide detailed information about the event</div>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Date & Time -->
                        <div class="form-group">
                            <label for="event_date" class="form-label">
                                Event Date <span class="required">*</span>
                            </label>
                            <div class="date-input">
                                <input type="date" id="event_date" name="event_date"
                                    value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}"
                                    class="form-control" required>
                                <i class="date-icon fas fa-calendar-alt"></i>
                            </div>
                            @error('event_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group time-group">
                            <label class="form-label">
                                Event Time <span class="required">*</span>
                            </label>
                            <div class="time-inputs">
                                <div class="time-wrapper">
                                    <input type="time" name="start_time"
                                        value="{{ old('start_time', $event->start_time) }}" class="form-control" required>
                                    <span class="time-label">Start</span>
                                </div>
                                <span class="time-separator">to</span>
                                <div class="time-wrapper">
                                    <input type="time" name="end_time"
                                        value="{{ old('end_time', $event->end_time) }}" class="form-control" required>
                                    <span class="time-label">End</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-details">
                    <div class="form-grid">
                        <!-- Venue -->
                        <div class="form-group">
                            <label for="venue" class="form-label">
                                Venue <span class="required">*</span>
                            </label>
                            <input type="text" id="venue" name="venue"
                                value="{{ old('venue', $event->venue) }}" class="form-control"
                                placeholder="e.g., Hospital Main Auditorium" required>
                            @error('venue')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address"
                                value="{{ old('address', $event->address) }}" class="form-control"
                                placeholder="Street, City, State">
                        </div>

                        <!-- Event Image -->
                        <div class="form-group full-width">
                            <label class="form-label">Event Image</label>
                            <div class="image-upload-wrapper">
                                <div class="image-preview" id="imagePreview">
                                    @if ($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="Event Image"
                                            class="preview-image">
                                        <button type="button" class="remove-image" id="removeImage">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <div class="image-placeholder">
                                            <i class="fas fa-image"></i>
                                            <span>No image selected</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="image-upload-controls">
                                    <label for="image" class="upload-button">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        Change Image
                                    </label>
                                    <input type="file" id="image" name="image" accept="image/*"
                                        class="file-input">
                                    <div class="file-info" id="fileInfo">
                                        <p class="file-name">Current: {{ basename($event->image) ?? 'No image' }}</p>
                                        <p class="file-size">Max size: 2MB</p>
                                    </div>
                                </div>
                                @error('image')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="form-group full-width">
                            <label for="requirements" class="form-label">Special Requirements</label>
                            <textarea id="requirements" name="requirements" rows="3" class="form-control"
                                placeholder="Any special requirements, instructions, or notes...">{{ old('requirements', $event->requirements) }}</textarea>
                            <div class="form-hint">Add any special instructions for participants</div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-contact">
                    <div class="form-grid">
                        <!-- Organizer -->
                        <div class="form-group">
                            <label for="organizer" class="form-label">Organizer</label>
                            <input type="text" id="organizer" name="organizer"
                                value="{{ old('organizer', $event->organizer) }}" class="form-control"
                                placeholder="e.g., Hospital Administration">
                        </div>

                        <!-- Contact Person -->
                        <div class="form-group">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person"
                                value="{{ old('contact_person', $event->contact_person) }}" class="form-control"
                                placeholder="e.g., Dr. John Doe">
                        </div>

                        <!-- Contact Number -->
                        <div class="form-group">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="tel" id="contact_number" name="contact_number"
                                value="{{ old('contact_number', $event->contact_number) }}" class="form-control"
                                placeholder="+91 9876543210">
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $event->email) }}" class="form-control"
                                placeholder="events@hospital.com">
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-social">
                    <div class="form-grid">
                        <!-- Facebook URL -->
                        <div class="form-group">
                            <label for="facebook_url" class="form-label">
                                <i class="fab fa-facebook-f social-icon facebook"></i> Facebook URL
                            </label>
                            <div class="input-with-icon">
                                <i class="input-icon fab fa-facebook-f"></i>
                                <input type="url" id="facebook_url" name="facebook_url"
                                    value="{{ old('facebook_url', $event->facebook_url) }}" class="form-control"
                                    placeholder="https://facebook.com/event-page">
                            </div>
                        </div>

                        <!-- Twitter URL -->
                        <div class="form-group">
                            <label for="twitter_url" class="form-label">
                                <i class="fab fa-twitter social-icon twitter"></i> Twitter URL
                            </label>
                            <div class="input-with-icon">
                                <i class="input-icon fab fa-twitter"></i>
                                <input type="url" id="twitter_url" name="twitter_url"
                                    value="{{ old('twitter_url', $event->twitter_url) }}" class="form-control"
                                    placeholder="https://twitter.com/event-page">
                            </div>
                        </div>

                        <!-- Instagram URL -->
                        <div class="form-group">
                            <label for="instagram_url" class="form-label">
                                <i class="fab fa-instagram social-icon instagram"></i> Instagram URL
                            </label>
                            <div class="input-with-icon">
                                <i class="input-icon fab fa-instagram"></i>
                                <input type="url" id="instagram_url" name="instagram_url"
                                    value="{{ old('instagram_url', $event->instagram_url) }}" class="form-control"
                                    placeholder="https://instagram.com/event-page">
                            </div>
                        </div>

                        <!-- LinkedIn URL -->
                        <div class="form-group">
                            <label for="linkedin_url" class="form-label">
                                <i class="fab fa-linkedin-in social-icon linkedin"></i> LinkedIn URL
                            </label>
                            <div class="input-with-icon">
                                <i class="input-icon fab fa-linkedin-in"></i>
                                <input type="url" id="linkedin_url" name="linkedin_url"
                                    value="{{ old('linkedin_url', $event->linkedin_url) }}" class="form-control"
                                    placeholder="https://linkedin.com/event-page">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website_url" class="form-label">
                                <i class="fas fa-globe social-icon website"></i> Website URL
                            </label>
                            <div class="input-with-icon">
                                <i class="input-icon fas fa-globe"></i>
                                <input type="url" id="website_url" name="website_url"
                                    value="{{ old('website_url', $event->website_url) }}" class="form-control"
                                    placeholder="https://yourwebsite.com">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-settings">
                    <div class="form-grid">
                        <!-- Target Participants -->
                        <div class="form-group">
                            <label for="target_participants" class="form-label">Target Participants</label>
                            <div class="number-input">
                                <input type="number" id="target_participants" name="target_participants"
                                    value="{{ old('target_participants', $event->target_participants) }}"
                                    class="form-control" placeholder="e.g., 100" min="0">
                                <div class="number-controls">
                                    <button type="button" class="number-up"><i class="fas fa-chevron-up"></i></button>
                                    <button type="button" class="number-down"><i
                                            class="fas fa-chevron-down"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <div class="select-wrapper">
                                <select id="status12" name="status" class="form-control">
                                    <option value="upcoming"
                                        {{ old('status', $event->status) == 'upcoming' ? 'selected' : '' }}>Upcoming
                                    </option>
                                    <option value="ongoing"
                                        {{ old('status', $event->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed"
                                        {{ old('status', $event->status) == 'completed' ? 'selected' : '' }}>Completed
                                    </option>
                                    <option value="cancelled"
                                        {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                </select>
                                <i class="select-icon fas fa-chevron-down"></i>
                            </div>
                        </div>

                        <!-- Checkboxes -->
                        <div class="form-group full-width">
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                        {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}
                                        class="checkbox-input">
                                    <label for="is_featured" class="checkbox-label">
                                        <span class="checkbox-custom"></span>
                                        <i class="fas fa-star"></i>
                                        Mark as Featured Event
                                    </label>
                                </div>

                                <div class="checkbox-item">
                                    <input type="checkbox" id="is_published" name="is_published" value="1"
                                        {{ old('is_published', $event->is_published) ? 'checked' : '' }}
                                        class="checkbox-input">
                                    <label for="is_published" class="checkbox-label">
                                        <span class="checkbox-custom"></span>
                                        <i class="fas fa-eye"></i>
                                        Publish Event
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-prev-tab" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>

                    <div class="action-buttons">
                        <a href="{{ route('admin.events.index') }}" class="btn-cancel">
                            Cancel
                        </a>
                        <button type="button" class="btn-next-tab">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Update Event
                        </button>
                    </div>
                </div>

                <!-- Form Progress -->
                <div class="form-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 20%"></div>
                    </div>
                    <div class="progress-steps">
                        <span class="step active" data-step="1">Basic</span>
                        <span class="step" data-step="2">Details</span>
                        <span class="step" data-step="3">Contact</span>
                        <span class="step" data-step="4">Social</span>
                        <span class="step" data-step="5">Settings</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
@endsection

<script src="{{ asset('js/form-tabs.js') }}"></script>
