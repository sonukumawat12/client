<?php

namespace Modules\HelpGuide\Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewTicket extends Notification
{
    use Queueable;

    private $notificationUrl;
    private $ticket;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket, $user, $url)
    {
        $this->notificationUrl = $url;
        $this->ticket = $ticket;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->greeting(__('hello'))
                ->subject(__('notifications.new_ticket', ['name' => $this->user->name]))
                ->line( new HtmlString(__('notifications.new_ticket_has_been_submitted', ['name' => '<strong>'.$this->user->name.'</strong>'])))
                ->line( new HtmlString('<p style="padding: 10px;background-color:#ddd">' . strip_tags(\Illuminate\Support\Str::limit($this->ticket->latestReply->content), 300) . '</p>'))
                ->action(__('notifications.see_ticket'), $this->notificationUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }   

    public function toDatabase($notifiable)
    {
        return [
            'text' => __('notifications.new_ticket', ['name' => $notifiable->name]),
            'icon' => 'ticket-alt',
            'color' => 'primary',
            'link' => $this->notificationUrl
        ];
    }
}
