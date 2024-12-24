<?php

namespace App\Notifications\User\Voucher;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RedeemVoucherMail extends Notification
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
        // dd($data);
        return (new MailMessage)
                    ->greeting("Hello ".$user->fullname." !")
                    ->subject("Voucher Redeem ".$data->request_amount. ' ' .$data->request_currency." Successfully")
                    ->line("Voucher Redeem successfully")
                    ->line("Voucher Code : " .$data->code)
                    ->line("Transaction ID : " .$trx_id)
                    ->line("Request Amount : " . get_amount($data->request_amount,$data->request_currency,2))
                    ->line("Exchange Rate : " ." 1 ". $data->request_currency.' = '. getAmount($data->exchange_rate,4).' '.$data->request_currency)
                    ->line("Fees & Charges : " . get_amount($data->total_charge,$data->request_currency,2))
                    ->line("Receiver Amount : " .  get_amount($data->total_payable,$data->request_currency,2))
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
