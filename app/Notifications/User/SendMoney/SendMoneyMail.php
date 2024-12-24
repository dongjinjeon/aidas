<?php

namespace App\Notifications\User\SendMoney;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendMoneyMail extends Notification
{
    use Queueable;

    public $user;
    public $data;
    public $trx_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$data,$trx_id)
    {
        $this->user = $user;
        $this->data = $data;
        $this->trx_id = $trx_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $this->user;
        $data = $this->data;
        $trx_id = $this->trx_id;
        $date = Carbon::now(); 
        $dateTime = $date->format('Y-m-d h:i:s A'); 
        return (new MailMessage)
                    ->greeting("Hello ".$user->fullname." !")
                    ->subject("Send Money From ".$data->requestData->sender_amount. ' ' .$data->requestData->sender_currency." to ".$data->requestData->receiver_amount.' '. $data->requestData->receiver_currency." Successfully")
                    ->line("Your have sent money successfully to ".$data->receiver->username)
                    ->line("Transaction ID : " .$trx_id)
                    ->line("Sender Amount : " . get_amount($data->requestData->sender_amount,$data->requestData->sender_currency,2))
                    ->line("Exchange Rate : " ." 1 ". $data->requestData->sender_currency.' = '. getAmount($data->charges->exchange_rate,4).' '.$data->requestData->receiver_currency)
                    ->line("Fees & Charges : " . get_amount($data->charges->total_charge,$data->requestData->sender_currency,2))
                    ->line("Receiver Amount : " .  get_amount($data->requestData->receiver_amount,$data->requestData->receiver_currency,2))
                    ->line("Status : "."Success")
                    ->line("Date And Time: " .$dateTime)
                    ->line('Thank you for using our application!');
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
}
