<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AttendanceRecord;
use App\Notifications\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use  Illuminate\support\str;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        // Get the authenticated user using Auth facade
        $user = Auth::user();
        
        // For absolute type safety, check if user exists and is the right type
        if (!$user instanceof User) {
            // Handle the case where user is not authenticated or not the expected type
            Auth::logout();
            return redirect('/login');
        }

        // Get today's records for the current user
        $records = $user->attendanceRecords()
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
        // Get the authenticated user - METHOD 2: Using request()->user()
        $user = $request->user();
        
        // Type check for safety
        if (!$user instanceof User) {
            Log::error("Process attendance failed: User not authenticated properly");
            return redirect()->route('employee.dashboard')->with('error', 'Authentication error. Please try again.');
        }

        // Prevent multiple check-ins/outs in the same day
        $alreadyRecorded = $user->attendanceRecords()
            ->whereDate('recorded_at', today())
            ->where('type', $type)
            ->exists();

        if ($alreadyRecorded) {
            Log::warning("User #" . $user->id . " attempted to $type multiple times on " . today()->toDateString());
            return redirect()->route('employee.dashboard')->with('error', 'You have already ' . str_replace('_', ' ', $type) . ' today.');
        }

        // Validate the photo
        $request->validate([
            'photo' => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png']
        ]);

        try {
             // Store the photo with a unique filename
            // Create a safe filename: timestamp_userid_type.extension
            $baseName = time() . '_' . $user->id . '_' . $type;
            $photoName = Str::slug($baseName) . '.' . $request->photo->extension(); 
            $photoPath = $request->file('photo')->storeAs('attendance', $photoName, 'public');

            // Create attendance record
            $attendanceRecord = $user->attendanceRecords()->create([
                'type' => $type,
                'recorded_at' => now(),
                'photo_path' => $photoPath
            ]);

            // NOTIFY THE MANAGER
            $this->notifyManager($user, $type, $photoPath);

            return redirect()->route('employee.dashboard')->with('success',
                ucfirst(str_replace('_', ' ', $type)) . ' recorded successfully! Manager notified.');

        } catch (\Exception $e) {
            Log::error("Error recording $type for User #" . $user->id . ": " . $e->getMessage());
            return redirect()->route('employee.dashboard')->with('error',
                'Failed to record attendance. Please try again.');
        }
    }
    private function notifyManager(User $user, $type, $photoPath)
    {
        if ($user->manager) {
            try {
                $user->manager->notify(new EmployeeAttendance(
                    $user->name,
                    $type,
                    now(),
                    $photoPath
                ));
                Log::info("Notification sent successfully to Manager #" . $user->manager->id . " for User #" . $user->id . "'s $type.");
            } catch (\Exception $e) {
                Log::error("Notification failed for User #" . $user->id . "'s $type: " . $e->getMessage());
            }
        } else {
            Log::warning("No manager assigned for User #" . $user->id . ". Cannot send $type notification.");
        }
    }
}