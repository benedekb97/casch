<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mail;

class Email extends Model
{
    public function send()
    {
        $subject = $this->subject;
        $body = $this->body;
        $from_name = $this->from_name;
        $from_email = $this->from_email;
        $to_name = $this->to_name;
        $to_email = $this->to_email;

        Mail::raw($body, function($m) use ($subject, $from_name, $from_email, $to_name, $to_email){
            $m->from($from_email, $from_name);

            $m->to($to_email, $to_name)->subject($subject);
        });

        $this->sent_at = date('Y-m-d H:i:s');
        $this->save();
    }
}
