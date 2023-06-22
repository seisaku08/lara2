<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NineDaysBeforeMail extends Mailable
{
    use Queueable, SerializesModels;
    var $orderdata;

    /**
     * Create a new message instance.
     */
    public function __construct($orderdata)
    {
        $this->orderdata = $orderdata;

        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        if($this->orderdata['order']['seminar_venue_pending'] == 1){
            $pend = '【配送先住所未入力です！】';
        }else{
            $pend = '【機材管理システム】';           
        }
        return new Envelope(
            subject: "{$pend}リマインドメール（予約No. {$this->orderdata['order']['order_no']}）",
            from: new Address('order@daioh-pc.com', '㈱大應 セミナー機材管理システム'),
            cc: 'order@daioh-pc.com',
            replyTo: 'support@daioh-pc.com',

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            with: [ 'orderdata' => $this->orderdata,],
            view: 'ninedaymail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
