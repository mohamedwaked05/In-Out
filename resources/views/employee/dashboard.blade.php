<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - InOut System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .dashboard-card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .attendance-btn {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-checkin {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
        }

        .btn-checkin:hover, .btn-checkout:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .attendance-table {
            border-radius: 8px;
            overflow: hidden;
        }

        .attendance-table th {
            background-color: #4361ee;
            color: white;
            font-weight: 600;
        }

        .photo-thumb {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            transition: transform 0.3s ease;
            cursor: pointer;
            border: 2px solid #e9ecef;
        }

        .photo-thumb:hover {
            transform: scale(1.8);
            z-index: 100;
            border-color: #4361ee;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-checkin {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
        }

        .badge-checkout {
            background-color: rgba(231, 76, 60, 0.15);
            color: #c0392b;
        }

        .welcome-header {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .stats-card {
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4361ee;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        #cameraModal .modal-content {
            border-radius: 12px;
        }

        #video {
            border-radius: 8px;
            width: 100%;
            background: #000;
        }

        .empty-state {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #4361ee, #3a0ca3);">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-clock me-2"></i>InOut System
            </a>

            <div class="d-flex align-items-center">
                <div class="me-3 text-light">
                    <i class="fas fa-user-circle me-1"></i>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Welcome Header -->
        <div class="welcome-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Welcome, {{ auth()->user()->name }}!</h2>
                    <p class="mb-0">Employee Dashboard • {{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="text-center">
                    <div class="display-4 fw-bold">{{ now()->format('h:i A') }}</div>
                    <small>{{ now()->format('T') }}</small>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $records->where('type', 'check_in')->count() }}</div>
                    <div class="stats-label">Check-Ins Today</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $records->where('type', 'check_out')->count() }}</div>
                    <div class="stats-label">Check-Outs Today</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $records->count() }}</div>
                    <div class="stats-label">Total Records</div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Attendance Records -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Today's Attendance Records</h5>
                    </div>
                    <div class="card-body">
                        @if($records->count())
                            <div class="table-responsive">
                                <table class="table table-hover attendance-table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Time</th>
                                            <th>Photo Proof</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($records as $record)
                                            <tr>
                                                <td>
                                                    <span class="status-badge {{ $record->type == 'check_in' ? 'badge-checkin' : 'badge-checkout' }}">
                                                        <i class="fas {{ $record->type == 'check_in' ? 'fa-sign-in-alt' : 'fa-sign-out-alt' }} me-1"></i>
                                                        {{ ucfirst($record->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $record->recorded_at->format('h:i A') }}</td>
                                                <td>
                                                    @if($record->photo_path)
                                                        <img src="{{ Storage::url($record->photo_path) }}"
                                                             alt="Proof" class="photo-thumb"
                                                             onclick="window.open('{{ Storage::url($record->photo_path) }}', '_blank')">
                                                    @else
                                                        <span class="text-muted">No photo</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <h5>No records for today</h5>
                                <p>Your attendance records will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Check-in/Check-out Panel -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Attendance Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <!-- Check-in Form -->
                            <form action="{{ route('employee.check-in') }}" method="POST" enctype="multipart/form-data" id="checkInForm">
                                @csrf
                                <input type="file" name="photo" id="checkInPhoto" accept="image/*" class="d-none" onchange="previewImage(this, 'checkIn')">
                                <button type="button" onclick="openCamera('checkIn')" class="btn btn-checkin attendance-btn w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i> Check In
                                </button>
                                <div id="checkInPreview" class="mt-2 d-none text-center">
                                    <img id="checkInPreviewImg" class="img-thumbnail mb-2" style="max-width: 100px; max-height: 100px;">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Confirm Check-In</button>
                                </div>
                            </form>

                            <!-- Check-out Form -->
                            <form action="{{ route('employee.check-out') }}" method="POST" enctype="multipart/form-data" id="checkOutForm">
                                @csrf
                                <input type="file" name="photo" id="checkOutPhoto" accept="image/*" class="d-none" onchange="previewImage(this, 'checkOut')">
                                <button type="button" onclick="openCamera('checkOut')" class="btn btn-checkout attendance-btn w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i> Check Out
                                </button>
                                <div id="checkOutPreview" class="mt-2 d-none text-center">
                                    <img id="checkOutPreviewImg" class="img-thumbnail mb-2" style="max-width: 100px; max-height: 100px;">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Confirm Check-Out</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card dashboard-card">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Today's Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Check-Ins:</span>
                            <strong>{{ $records->where('type', 'check_in')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Check-Outs:</span>
                            <strong>{{ $records->where('type', 'check_out')->count() }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Total Records:</span>
                            <strong>{{ $records->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Take Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="video" width="100%" autoplay></video>
                    <canvas id="canvas" class="d-none"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="capturePhoto()">
                        <i class="fas fa-camera me-1"></i> Capture
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentFormType = '';
        let stream = null;

        function openCamera(formType) {
            currentFormType = formType;
            const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
            modal.show();

            // Access camera
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(function(videoStream) {
                        stream = videoStream;
                        document.getElementById('video').srcObject = videoStream;
                    })
                    .catch(function(error) {
                        console.error('Camera error:', error);
                        // Fallback: open file input
                        document.getElementById(formType + 'Photo').click();
                    });
            } else {
                // Fallback for browsers without camera access
                document.getElementById(formType + 'Photo').click();
            }
        }

        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            // Set canvas dimensions to video dimensions
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw current video frame to canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert canvas to blob and create file
            canvas.toBlob(function(blob) {
                const file = new File([blob], 'photo.jpg', { type: 'image/jpeg' });

                // Create a DataTransfer object to simulate file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Set the file to the appropriate input
                const input = document.getElementById(currentFormType + 'Photo');
                input.files = dataTransfer.files;

                // Trigger preview
                previewImage(input, currentFormType);

                // Stop camera stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('cameraModal')).hide();
            }, 'image/jpeg');
        }

        function previewImage(input, formType) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(formType + 'PreviewImg').src = e.target.result;
                    document.getElementById(formType + 'Preview').classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            });
        }, 5000);
    </script>
</body>
</html>
