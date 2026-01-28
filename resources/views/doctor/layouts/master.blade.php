<?php

?>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>Blogs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- App favicon -->
    <link rel="shortcut icon" href="#">

    <!-- include head css -->
    @include('doctor.layouts.head-css')
</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- topbar -->
        @include('doctor.layouts.topbar')

        <!-- sidebar components -->
        @include('doctor.layouts.sidebar')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                @yield('content')
            </div>
            <!-- end page content-->

            <!-- footer -->
            @include('doctor.layouts.footer')
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- customizer -->
    {{-- @include('admin.layouts.right-sidebar') --}}

    <!-- Incoming Call Modal (global) -->
    <div class="modal fade" id="incomingCallModalGlobal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Incoming Video Call</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="incomingPatientNameGlobal">--</p>
                    <p class="small text-muted" id="incomingAppointmentInfoGlobal"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="declineCallBtnGlobal" class="btn btn-danger"
                        data-bs-dismiss="modal">Decline</button>
                    <button type="button" id="acceptCallBtnGlobal" class="btn btn-success">Accept</button>
                </div>
            </div>
        </div>
    </div>

    <!-- vendor-scripts -->
    @include('doctor.layouts.vendor-scripts')

    @push('scripts')
        <script>
            (function() {
                const doctorId = {{ Auth::user()->doctor_id ?? 'null' }};
                const startBase = '{{ url('doctor/appointments') }}';
                if (doctorId && window.Echo) {
                    Echo.private('doctor.' + doctorId)
                        .listen('CallRequested', (e) => {
                            const patientName = (e.patient.first_name || '') + ' ' + (e.patient.last_name || '');
                            document.getElementById('incomingPatientNameGlobal').innerText = patientName;
                            document.getElementById('incomingAppointmentInfoGlobal').innerText = 'Appointment ID: ' + e
                                .appointment_id;
                            try {
                                var incomingModal = new bootstrap.Modal(document.getElementById(
                                    'incomingCallModalGlobal'));
                                incomingModal.show();
                            } catch (err) {
                                $('#incomingCallModalGlobal').modal('show');
                            }

                            document.getElementById('acceptCallBtnGlobal').onclick = function() {
                                window.location.href = startBase + '/' + e.appointment_id + '/start';
                            };
                        });
                }
            })();
        </script>
    @endpush

</body>

</html>
