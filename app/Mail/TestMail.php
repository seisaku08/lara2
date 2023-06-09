<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
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
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'オーダー受領確認メール('.$this->orderdata['order_no'].')',
            from: 'order@daioh-pc.com',
            to: 'foo@example.net',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
                
        // dd($this->orderdata['machines']);
        return new Content(
            with: [ 'orderdata' => $this->orderdata,],
            html: 'testmail',
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
