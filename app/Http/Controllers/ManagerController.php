<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ManagerController extends Controller
{
    /**
     * Display the manager dashboard.
     */
    public function dashboard()
    {
    $manager = auth()->user();
    $date = request('date', today());

$employees = $manager->employees()
    ->with(['attendanceRecords' => function($query) use ($date) {
        $query->whereDate('recorded_at', $date)
              ->orderBy('recorded_at', 'desc');
    }])
    ->get();

    return view('manager.dashboard', compact('employees', 'manager'));
}
}
