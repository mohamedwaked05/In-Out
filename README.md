🚀 InOut — Photo-Verified Attendance System (Laravel)

A role-based attendance tracker where employees check in/out by taking a photo of their face. Managers get a clean dashboard to review daily activity and verify photo proof at a glance.

Built with Laravel, Blade, and Bootstrap.

✨ Key Features

👤 Employee Dashboard

One-tap Check-In / Check-Out with camera capture (HTML5 getUserMedia + Canvas → image upload)

Photo proof stored securely and displayed as thumbnails

Today’s history with time and photo

Modern, responsive UI

🧑‍💼 Manager Dashboard

Team overview with all employees’ records for a selected date

Visual verification of photo proofs

Quick stats (check-ins, check-outs, team size)

🔐 Security & Roles

Auth (Laravel), CSRF, input validation

Role-based access (employee, manager)

🗂 Storage & Files

Photos saved to storage/app/public/...

Served via Storage::url() (symlinked to public/storage)

Optional S3 support for production

🖼️ How Photo Check-In Works (High Level)

Employee clicks Check In / Check Out

Browser opens camera via navigator.mediaDevices.getUserMedia

A frame is captured to Canvas, converted to a Blob, and sent as a file input

Backend validates & stores the image, then creates an attendance_records row:

user_id, type (check_in / check_out), recorded_at, photo_path

⚠️ Browser Requirement: Camera access requires HTTPS in production (works on http://localhost during development). Use SSL (Let’s Encrypt) on your domain.

🛠 Tech Stack

Backend: Laravel (PHP 8+)

Frontend: Blade, Bootstrap 5, Font Awesome

Database: MySQL

Storage: Local public disk (or S3)

Auth: Laravel Breeze / default auth scaffolding

📂 Project Structure (Relevant)
app/
  Http/Controllers/
    EmployeeController.php
    ManagerController.php
  Models/
    AttendanceRecord.php
    User.php
resources/views/
  employee/dashboard.blade.php    # camera capture + photo preview + actions
  manager/dashboard.blade.php     # team view + stats + photo verification
routes/web.php                    # role-based routes, dashboards, actions

🚀 Getting Started
1) Clone & Install
git clone https://github.com/your-username/inout-attendance.git
cd inout-attendance
composer install
npm install && npm run build

2) Environment
cp .env.example .env
php artisan key:generate


Set DB credentials in .env:

APP_URL=https://your-domain.com   # important for Storage::url()
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

3) Migrate
php artisan migrate

4) Link Storage (for photo proof)
php artisan storage:link

5) Serve
php artisan serve


Visit: http://127.0.0.1:8000

🔁 Core Routes (Examples)

Adjust to your actual names; recommended dot-notation:

GET   /employee/dashboard         -> employee.dashboard
POST  /employee/check-in          -> employee.check.in
POST  /employee/check-out         -> employee.check.out

GET   /manager/dashboard          -> manager.dashboard


In Blade:

<form action="{{ route('employee.check.in') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- camera-driven file input filled by JS -->
</form>

✅ Validation & Storage (Controller Snippet)
$request->validate([
    'photo' => 'required|image|max:4096', // ~4MB
]);

$path = $request->file('photo')->store(
    'attendance/'.auth()->id().'/'.now()->toDateString(),
    'public'
);

AttendanceRecord::create([
    'user_id'     => auth()->id(),
    'type'        => 'check_in', // or check_out
    'recorded_at' => now(),
    'photo_path'  => $path,
]);


In views, display with:
Storage::url($record->photo_path)

🔒 Security Notes

CSRF tokens on all forms

Only authenticated users can access dashboards

Role gate for manager area

Validate uploaded images (MIME/size)

Use HTTPS in production (required for camera)

📸 Screenshots (Optional)
docs/screenshots/employee-dashboard.png
docs/screenshots/manager-dashboard.png


Add images and reference them here to impress reviewers.

🧭 Roadmap

⏱️ Late / absence rules & alerts

📊 Reports (CSV/PDF export, date ranges)

☁️ S3 storage, image resizing

🏢 Multi-tenant (per company), subscriptions

🔄 CI/CD (GitHub Actions), staging/prod

📱 PWA camera UX on mobile

🤝 Contributing

PRs are welcome! Please open an issue first to discuss significant changes.

📜 License

MIT

🔥 Suggested Repo Tagline (one-liner)

Photo-verified attendance tracking with role-based dashboards, built on Laravel.
