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

        /* New Camera Interface Styles */
        .camera-interface {
            margin-bottom: 15px;
        }

        .camera-controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .camera-controls .btn {
            flex: 1;
        }

        #photo-preview {
            max-width: 100%;
            border-radius: 8px;
            border: 2px solid #4361ee;
        }

        .photo-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .photo-actions .btn {
            flex: 1;
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
        <!-- Check-in Section -->
        <div class="text-center mb-4 p-4 border rounded bg-light">
            <h6 class="text-success mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>Check In
            </h6>
            
            <!-- Camera Interface -->
            <div id="checkIn-interface">
                <div class="mb-3">
                    <video id="checkIn-video" width="100%" height="300" autoplay class="border rounded d-none"></video>
                    <canvas id="checkIn-canvas" class="d-none"></canvas>
                </div>
                
                <div class="camera-controls">
                    <button type="button" id="checkIn-start-btn" class="btn btn-success btn-lg py-3" style="font-size: 1.2rem;">
                        <i class="fas fa-camera me-2"></i> START CAMERA FOR CHECK-IN
                    </button>
                    <button type="button" id="checkIn-capture-btn" class="btn btn-primary btn-lg py-3 d-none" style="font-size: 1.2rem;">
                        <i class="fas fa-camera me-2"></i> CAPTURE CHECK-IN PHOTO
                    </button>
                </div>
                
                <!-- Preview -->
                <div id="checkIn-preview" class="mt-3 d-none">
                    <img id="checkIn-preview-img" class="img-thumbnail w-100 border-success">
                    <div class="mt-2">
                        <button type="submit" form="checkInForm" class="btn btn-success btn-lg w-100 py-3">
                            <i class="fas fa-check-circle me-2"></i> CONFIRM CHECK-IN
                        </button>
                        <button type="button" id="checkIn-retake-btn" class="btn btn-outline-warning w-100 mt-2">
                            <i class="fas fa-redo me-2"></i> RETAKE PHOTO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <!-- Check-out Section -->
        <div class="text-center p-4 border rounded bg-light">
            <h6 class="text-danger mb-3">
                <i class="fas fa-sign-out-alt me-2"></i>Check Out
            </h6>
            
            <!-- Camera Interface -->
            <div id="checkOut-interface">
                <div class="mb-3">
                    <video id="checkOut-video" width="100%" height="300" autoplay class="border rounded d-none"></video>
                    <canvas id="checkOut-canvas" class="d-none"></canvas>
                </div>
                
                <div class="camera-controls">
                    <button type="button" id="checkOut-start-btn" class="btn btn-danger btn-lg py-3" style="font-size: 1.2rem;">
                        <i class="fas fa-camera me-2"></i> START CAMERA FOR CHECK-OUT
                    </button>
                    <button type="button" id="checkOut-capture-btn" class="btn btn-primary btn-lg py-3 d-none" style="font-size: 1.2rem;">
                        <i class="fas fa-camera me-2"></i> CAPTURE CHECK-OUT PHOTO
                    </button>
                </div>
                
                <!-- Preview -->
                <div id="checkOut-preview" class="mt-3 d-none">
                    <img id="checkOut-preview-img" class="img-thumbnail w-100 border-danger">
                    <div class="mt-2">
                        <button type="submit" form="checkOutForm" class="btn btn-success btn-lg w-100 py-3">
                            <i class="fas fa-check-circle me-2"></i> CONFIRM CHECK-OUT
                        </button>
                        <button type="button" id="checkOut-retake-btn" class="btn btn-outline-warning w-100 mt-2">
                            <i class="fas fa-redo me-2"></i> RETAKE PHOTO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Forms -->
        <form action="{{ route('employee.check-in') }}" method="POST" id="checkInForm">
            @csrf
            <input type="hidden" name="photo_data" id="checkIn-photo-data">
        </form>

        <form action="{{ route('employee.check-out') }}" method="POST" id="checkOutForm">
            @csrf
            <input type="hidden" name="photo_data" id="checkOut-photo-data">
        </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Simple Camera Handler
class SimpleCamera {
    constructor(type) {
        this.type = type;
        this.video = document.getElementById(`${type}-video`);
        this.canvas = document.getElementById(`${type}-canvas`);
        this.previewImg = document.getElementById(`${type}-preview-img`);
        this.photoData = document.getElementById(`${type}-photo-data`);
        
        this.startBtn = document.getElementById(`${type}-start-btn`);
        this.captureBtn = document.getElementById(`${type}-capture-btn`);
        this.retakeBtn = document.getElementById(`${type}-retake-btn`);
        this.preview = document.getElementById(`${type}-preview`);
        
        this.stream = null;
        this.init();
    }

    init() {
        this.startBtn.addEventListener('click', () => this.startCamera());
        this.captureBtn.addEventListener('click', () => this.capturePhoto());
        this.retakeBtn.addEventListener('click', () => this.retakePhoto());
    }

    async startCamera() {
        try {
            // Show loading state
            this.startBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ACCESSING CAMERA...';
            this.startBtn.disabled = true;

            // Try to access camera
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'user'
                } 
            });

            // Success - show video
            this.video.srcObject = this.stream;
            this.video.classList.remove('d-none');
            this.startBtn.classList.add('d-none');
            this.captureBtn.classList.remove('d-none');

        } catch (err) {
            console.error('Camera error:', err);
            this.handleCameraError(err);
        }
    }

    capturePhoto() {
        if (!this.stream) return;

        // Draw video frame to canvas
        const context = this.canvas.getContext('2d');
        this.canvas.width = this.video.videoWidth;
        this.canvas.height = this.video.videoHeight;
        context.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);

        // Convert to base64
        const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
        
        // Show preview
        this.previewImg.src = imageData;
        this.photoData.value = imageData;
        
        // Update UI
        this.video.classList.add('d-none');
        this.captureBtn.classList.add('d-none');
        this.preview.classList.remove('d-none');

        // Stop camera
        this.stopCamera();
    }

    retakePhoto() {
        // Reset UI
        this.preview.classList.add('d-none');
        this.startBtn.classList.remove('d-none');
        this.startBtn.innerHTML = '<i class="fas fa-camera me-2"></i> START CAMERA FOR ' + (this.type === 'checkIn' ? 'CHECK-IN' : 'CHECK-OUT');
        this.startBtn.disabled = false;
        
        // Clear data
        this.photoData.value = '';
    }

    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }

    handleCameraError(err) {
        this.startBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> CAMERA ERROR - CLICK TO RETRY';
        this.startBtn.disabled = false;
        
        let errorMsg = 'Camera access denied. ';
        
        if (err.name === 'NotAllowedError') {
            errorMsg += 'Please allow camera permissions in your browser.';
        } else if (err.name === 'NotFoundError') {
            errorMsg += 'No camera found on your device.';
        } else {
            errorMsg += 'Error: ' + err.message;
        }
        
        alert(errorMsg);
    }
}

// Initialize cameras when page loads
document.addEventListener('DOMContentLoaded', function() {
    new SimpleCamera('checkIn');
    new SimpleCamera('checkOut');
});

// Auto-dismiss alerts
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        bootstrap.Alert.getOrCreateInstance(alert).close();
    });
}, 5000);
    </script>
</body>
</html>
