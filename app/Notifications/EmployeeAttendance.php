<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAttendance extends Notification
{
    use Queueable;

    protected $employeeName;
    protected $type;
    protected $time;
    protected $photoPath;

    public function __construct($employeeName, $type, $time, $photoPath)
    {
        $this->employeeName = $employeeName;
        $this->type = $type;
        $this->time = $time;
        $this->photoPath = $photoPath;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->employeeName . ' has ' . $this->type . ' at ' . $this->time->format('h:i A'),
            'photo_path' => $this->photoPath,
            'employee_name' => $this->employeeName,
            'type' => $this->type,
            'time' => $this->time,
            'url' => '/manager/dashboard' // Link to manager dashboard
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Employee ' . ucfirst($this->type) . ' Notification')
                    ->line($this->employeeName . ' has ' . $this->type . ' at ' . $this->time->format('h:i A'))
                    ->action('View Dashboard', url('/manager/dashboard'))
                    ->line('Thank you for using our application!');
    }
}
