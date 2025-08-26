<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - InOut System</title>
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

        .attendance-table {
            border-radius: 8px;
            overflow: hidden;
        }

        .attendance-table th {
            background-color: #4361ee;
            color: white;
            font-weight: 600;
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
                <i class="fas fa-users me-2"></i>InOut System
            </a>

            <div class="d-flex align-items-center">
                <div class="me-3 text-light">
                    <i class="fas fa-user-tie me-1"></i>
                    <span class="d-none d-md-inline">{{ $manager->name }}</span>
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
                    <h2 class="mb-1">Welcome, {{ $manager->name }}!</h2>
                    <p class="mb-0">Manager Dashboard • {{ now()->format('l, F j, Y') }}</p>
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
                    <div class="stats-number">
                        {{ $employees->sum(fn($e) => $e->attendanceRecords->where('type','check_in')->count()) }}
                    </div>
                    <div class="stats-label">Total Check-Ins Today</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number">
                        {{ $employees->sum(fn($e) => $e->attendanceRecords->where('type','check_out')->count()) }}
                    </div>
                    <div class="stats-label">Total Check-Outs Today</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $employees->count() }}</div>
                    <div class="stats-label">Employees in Team</div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <form method="GET" action="{{ route('manager.dashboard') }}" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label for="date" class="col-form-label fw-bold">Select Date:</label>
                </div>
                <div class="col-auto">
                    <input type="date" id="date" name="date"
                           value="{{ request('date', today()->toDateString()) }}"
                           class="form-control">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Attendance Table -->
        <div class="card dashboard-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Your Team's Attendance</h5>
            </div>
            <div class="card-body">
                @if($employees->count())
                    <div class="table-responsive">
                        <table class="table table-hover attendance-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Records ({{ request('date', today()->toDateString()) }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td class="align-middle fw-semibold">{{ $employee->name }}</td>
                                        <td>
                                            @if($employee->attendanceRecords->count())
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($employee->attendanceRecords as $record)
                                                        <li>
                                                            <span class="status-badge {{ $record->type === 'check_in' ? 'badge-checkin' : 'badge-checkout' }}">
                                                                <i class="fas {{ $record->type === 'check_in' ? 'fa-sign-in-alt' : 'fa-sign-out-alt' }} me-1"></i>
                                                                {{ ucfirst($record->type) }}
                                                            </span>
                                                            at {{ $record->recorded_at->format('h:i A') }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <em class="text-muted">No records for this date</em>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <h5>No employees assigned to you</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
