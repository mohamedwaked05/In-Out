<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Notifications\EmployeeAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployeeApiController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user instanceof User) {
            return response()->json([
                'error' => 'Authentication failed'
            ], 401);
        }

        // Get today's records for the current user
        $records = $user->attendanceRecords()
            ->whereDate('recorded_at', today())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'today_records' => $records,
            'timestamp' => now()
        ]);
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
        $user = Auth::user();
        
        if (!$user instanceof User) {
            Log::error("Process attendance failed: User not authenticated properly");
            return response()->json([
                'error' => 'Authentication error'
            ], 401);
        }

        // Prevent multiple check-ins/outs in the same day
        $alreadyRecorded = $user->attendanceRecords()
            ->whereDate('recorded_at', today())
            ->where('type', $type)
            ->exists();

        if ($alreadyRecorded) {
            Log::warning("User #" . $user->id . " attempted to $type multiple times on " . today()->toDateString());
            return response()->json([
                'error' => 'You have already ' . str_replace('_', ' ', $type) . ' today.'
            ], 400);
        }

        // Validate the photo
        $request->validate([
            'photo' => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png']
        ]);

        try {
            // Store the photo with a unique filename
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

            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $type)) . ' recorded successfully!',
                'record_id' => $attendanceRecord->id,
                'photo_path' => $photoPath,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error("Error recording $type for User #" . $user->id . ": " . $e->getMessage());
            return response()->json([
                'error' => 'Failed to record attendance. Please try again.'
            ], 500);
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