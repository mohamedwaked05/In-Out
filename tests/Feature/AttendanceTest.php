<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_check_in_twice_on_the_same_day()
    {
        // Fake the storage disk for testing
        Storage::fake('public');

        // 1. Create a test user USING THE CORRECT METHOD
        // The 'create' method returns an Authenticatable model
        $user = User::factory()->create(['role' => 'employee']);

        // 2. Simulate a first check-in for the user
        // This should now work as $user is Authenticatable
        $response1 = $this->actingAs($user)->post('/employee/check-in', [
            'photo' => UploadedFile::fake()->image('first-checkin.jpg')
        ]);

        // 3. Assert the first check-in was successful
        $response1->assertRedirect(route('employee.dashboard'));

        // 4. Simulate a second check-in attempt on the same day
        $response2 = $this->actingAs($user)->post('/employee/check-in', [
            'photo' => UploadedFile::fake()->image('second-checkin.jpg')
        ]);

        // 5. Assert the second attempt fails and has error
        $response2->assertRedirect(route('employee.dashboard'));
        $response2->assertSessionHas('error'); // Check for error message

        // 6. Assert only one check-in record exists
        $this->assertDatabaseCount('attendance_records', 1);
    }
}