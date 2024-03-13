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

class ShippingMail extends Mailable
{
    use Queueable, SerializesModels;
    var $shippingdata;
    /**
     * Create a new message instance.
     */
    public function __construct($shippingdata)
    {
        //
        $this->shippingdata = $shippingdata;
        // dd($shippingdata);

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【機材管理システム】発送完了メール（予約No. {$this->shippingdata['orders']['order_no']}）",
            from: new Address('order@daioh-pc.com', '㈱大應 セミナー機材管理システム（テストサイト）'),
            cc: 'order@daioh-pc.com',
            bcc: 'pc-kanri@dai-oh.co.jp',
            replyTo: 'support@daioh-pc.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
                
        // dd($this->shippingdata['machines']);
        return new Content(
            with: [ 'orderdata' => $this->shippingdata, ],
            html: 'shippingmail',
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
