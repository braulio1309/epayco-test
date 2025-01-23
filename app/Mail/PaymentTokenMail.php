<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class PaymentTokenMail extends Mailable
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.payment_token')
                    ->with(['token' => $this->token]);
    }
}
