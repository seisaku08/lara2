<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;
    var $orderdata;
    /**
     * Create a new message instance.
     */
    public function __construct($orderdata)
    {
        //
        $this->orderdata = $orderdata;
        // dd($orderdata);

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【機材管理システム】予約受付完了メール（予約ID:{$this->orderdata['order']['order_no']}）",
            from: new Address('order@daioh-pc.com', '㈱大應 セミナー機材管理システム'),
            cc: 'order@daioh-pc.com',
            replyTo: 'support@daioh-pc.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
                
        // dd($this->orderdata['machines']);
        return new Content(
            with: [ 'orderdata' => $this->orderdata, ],
            html: 'ordermail',
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
