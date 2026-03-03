@extends('admin.layouts.master')

@section('title', 'Notifications Center')

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .text-orange { color: #ff4900 !important; }
        .bg-orange { background-color: #ff4900 !important; }
        .bg-orange-soft { background-color: rgba(255, 73, 0, 0.1); color: #ff4900 !important; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
        
        .notification-row { transition: all 0.2s; border-radius: 10px; margin-bottom: 8px; }
        .notification-row:hover { background-color: #f8f9fa; transform: translateX(5px); }
        .notification-unread { border-left: 4px solid #ff4900; background-color: rgba(255, 73, 0, 0.02); }
        
        .type-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .icon-leave { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        .icon-default { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; }
        
        .btn-orange { background-color: #ff4900; color: white; border-radius: 20px; padding: 6px 20px; font-weight: 600; font-size: 13px; border: none; }
        .btn-orange:hover { background-color: #e64200; color: white; box-shadow: 0 4px 10px rgba(255, 73, 0, 0.3); }
        
        .btn-outline-orange { border: 2px solid #ff4900; color: #ff4900; border-radius: 20px; padding: 5px 15px; font-weight: 600; font-size: 12px; background: transparent; }
        .btn-outline-orange:hover { background-color: #ff4900; color: white; }
        
        #notificationsTable_wrapper .dataTables_filter input { border-radius: 20px; padding: 6px 15px; border: 1px solid #ddd; margin-bottom: 15px; }
        #notificationsTable thead th { border: none; font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px; }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h3 class="fw-bold mb-0 text-orange">
                <i class="fas fa-bell me-2"></i>Notifications Center
            </h3>
            <p class="text-muted small mb-0">Stay updated with hospital activities and staff requests.</p>
        </div>
        <div>
            <form action="{{ route('admin.admin-notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-orange shadow-sm">
                    <i class="fas fa-check-double me-2"></i> Mark All as Read
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="notificationsTable">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Notification Details</th>
                                    <th>Received At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                    @php 
                                        $meta = is_string($notification->meta_data) ? json_decode($notification->meta_data, true) : $notification->meta_data; 
                                        $isUnread = !$notification->read_at;
                                    @endphp
                                    <tr class="notification-row {{ $isUnread ? 'notification-unread' : '' }}" id="notification-{{ $notification->id }}">
                                        <td class="text-center text-muted small">#{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="type-icon me-3 {{ $notification->type === 'leave_application' ? 'icon-leave' : 'icon-default' }}">
                                                    @if($notification->type === 'leave_application')
                                                        <i class="fas fa-calendar-alt"></i>
                                                    @else
                                                        <i class="fas fa-info-circle"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold fs-6 {{ $isUnread ? 'text-dark' : 'text-muted' }}">
                                                        {{ $notification->title }}
                                                        @if($isUnread)
                                                            <span class="badge bg-orange ms-2" style="font-size: 9px;">NEW</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $meta['message'] ?? 'New notification received' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium text-dark">{{ $notification->created_at->format('M d, Y') }}</span>
                                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $notification->created_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($isUnread)
                                                    <button class="btn btn-outline-orange mark-read" data-id="{{ $notification->id }}">
                                                        <i class="fas fa-check me-1"></i> Mark Read
                                                    </button>
                                                @endif
                                                
                                                @if($notification->type === 'leave_application' && isset($meta['leave_id']))
                                                    @if(isset($meta['doctor_id']))
                                                        <a href="{{ route('admin.doctors.leaves', $meta['doctor_id']) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                                                            <i class="fas fa-user-md me-1 text-info"></i> View Doctor
                                                        </a>
                                                    @elseif(isset($meta['staff_id']))
                                                        <a href="{{ route('admin.staff.leaves', ['staff_id' => $meta['staff_id']]) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">
                                                            <i class="fas fa-user-nurse me-1 text-primary"></i> View Staff
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#notificationsTable').DataTable({
                "pageLength": 10,
                "ordering": false, // Keep latest chronological by default
                "language": {
                    "search": "",
                    "searchPlaceholder": "Filter notifications...",
                }
            });

            // Mark as Read Logic
            $('.mark-read').click(function() {
                var id = $(this).data('id');
                var btn = $(this);
                var row = $('#notification-' + id);

                $.ajax({
                    url: "{{ url('admin/admin-notifications') }}/" + id + "/mark-read",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            row.removeClass('notification-unread');
                            row.find('.badge.bg-orange').fadeOut();
                            btn.fadeOut();
                            
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Marked as read',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
