<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->subject('New Contact Message: ' . $this->contact->subject)
                    ->view('emails.contact-notification')
                    ->with([
                        'contact' => $this->contact
                    ]);
    }
}

