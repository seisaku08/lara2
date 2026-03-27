<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnomalyAlertMail extends Mailable
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
        return new Envelope(
            subject: "【警告】イレギュラーな予約が検出されました。確認してください。",
            from: new Address('order@daioh-pc.com', '㈱大應 セミナー機材管理システム'),
            // cc: 'order@daioh-pc.com',
            // bcc: 'pc-kanri@dai-oh.co.jp',
            replyTo: 'support@daioh-pc.com',

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            with: [ 'alertdata' => $this->orderdata['alertdata'] ?? $this->orderdata,],
            view: 'alertmail',
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
