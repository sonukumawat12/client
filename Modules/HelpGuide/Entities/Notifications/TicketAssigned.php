<?php

namespace Modules\HelpGuide\Entities\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TicketAssigned extends Notification
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
                ->subject(__('notifications.ticket_assigned_to_you', ['name' => $this->user->name]))
                ->line( new HtmlString( __('notifications.ticket_assigned_to_you_details', [
                    'name' => '<strong>'.$this->user->name.'</strong>',
                    'ticket_title' => '<strong>'.$this->ticket->title.'</strong>'
                ]) )
                )->action(__('notifications.see_ticket'), $this->notificationUrl);
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
            'text' => __('notifications.ticket_assigned_to_you', ['name' => $notifiable->name]),
            'icon' => 'ticket-alt',
            'color' => 'primary',
            'link' => $this->notificationUrl
        ];
    }
}
