<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;

class ContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)  // <-- was missing $contact parameter
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->replyTo($this->contact->email, $this->contact->name)
                    ->subject('New Contact Message: ' . $this->contact->subject)
                    ->view('emails.contact-notification');
    }
}
