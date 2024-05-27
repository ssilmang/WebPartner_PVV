<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $request;
    /**
     * Create a new message instance.
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from : new Address('sarrpc1@gmail.com',"PVV"),
            replyTo:[
                new Address('sarrpc1@gmail.com',"PVV"),
            ],
            subject: "Visibilité de vos activités",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // return $this->from('example@gmail.com')->attach('path/to/file')->cc($users)->view('mail');
        // return $this->markdown('mail');
        return new Content(
            view:'mail'
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
