<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $new_password;
    public $topic = "Student Account Created";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $topic = null)
    {
        $this->new_password = $password;
        if ($topic) $this->topic = $topic;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new-password')->with([
            'new_password' => $this->new_password,
            'topic' => $this->topic
        ]);
    }
}
