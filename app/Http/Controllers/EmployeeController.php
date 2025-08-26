<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Notifications\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        // Get today's records for the current user
        $records = auth()->user()->attendanceRecords()
            ->whereDate('recorded_at', today())
            ->latest()
            ->get();

        return view('employee.dashboard', compact('records'));
    }

    public function checkIn(Request $request)
    {
        return $this->processAttendance('check_in', $request);
    }

    public function checkOut(Request $request)
    {
        return $this->processAttendance('check_out', $request);
    }

    private function processAttendance($type, Request $request)
    {
        \Log::info("=== PROCESS ATTENDANCE STARTED ===");
        \Log::info("Type: " . $type);

        // DEBUG: Check file upload
        \Log::info("Has file: " . ($request->hasFile('photo') ? 'Yes' : 'No'));
        if ($request->hasFile('photo')) {
            \Log::info("File name: " . $request->file('photo')->getClientOriginalName());
            \Log::info("File size: " . $request->file('photo')->getSize());
        }

        // DEBUG: Check user and manager
        $user = auth()->user();
        \Log::info("User ID: " . $user->id);
        \Log::info("User manager_id: " . $user->manager_id);

        if ($user->manager_id) {
            $manager = User::find($user->manager_id);
            \Log::info("Manager exists: " . ($manager ? 'Yes' : 'No'));
            if ($manager) {
                \Log::info("Manager role: " . $manager->role);
            }
        }

        // Prevent multiple check-ins/outs in the same day
        $alreadyRecorded = auth()->user()->attendanceRecords()
            ->whereDate('recorded_at', today())
            ->where('type', $type)
            ->exists();

        if ($alreadyRecorded) {
            \Log::warning("Already recorded today: " . $type);
            return redirect()->route('employee.dashboard')->with('error', 'You have already ' . str_replace('_', ' ', $type) . ' today.');
        }

        // Validate the photo
        $request->validate([
            'photo' => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png']
        ]);

        try {
            // Store the photo with a unique filename
            $photoName = time() . '_' . auth()->id() . '_' . $type . '.' . $request->photo->extension();
            $photoPath = $request->file('photo')->storeAs('attendance', $photoName, 'public');

            \Log::info("Photo stored: " . $photoPath);

            // Create attendance record
            $attendanceRecord = auth()->user()->attendanceRecords()->create([
                'type' => $type,
                'recorded_at' => now(),
                'photo_path' => $photoPath
            ]);
            \Log::info("Attendance record created: " . $attendanceRecord->id);

            // NOTIFY THE MANAGER
            \Log::info("Calling notifyManager...");
            $this->notifyManager($type, $photoPath);
            \Log::info("NotifyManager completed");

            return redirect()->route('employee.dashboard')->with('success',
                ucfirst(str_replace('_', ' ', $type)) . ' recorded successfully! Manager notified.');

        } catch (\Exception $e) {
            \Log::error("Error in processAttendance: " . $e->getMessage());
            return redirect()->route('employee.dashboard')->with('error',
                'Failed to record attendance: ' . $e->getMessage());
        }
    }

    private function notifyManager($type, $photoPath)
    {
        \Log::info("NOTIFY MANAGER CALLED");
        \Log::info("Type: " . $type . ", Photo: " . $photoPath);

        $user = auth()->user();
        \Log::info("User ID: " . $user->id);

        if ($user->manager) {
            \Log::info("Manager found: " . $user->manager->id);
            try {
                $user->manager->notify(new EmployeeAttendance(
                    $user->name,
                    $type,
                    now(),
                    $photoPath
                ));
                \Log::info("Notification sent successfully!");
            } catch (\Exception $e) {
                \Log::error("Notification failed: " . $e->getMessage());
            }
        } else {
            \Log::warning("No manager found for user!");
        }
    }
}
