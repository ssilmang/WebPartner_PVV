<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
class MailController extends Controller
{
    public function sendMail(){
        $mailmessage = "Bonjour sarr pc";
        $tomail = "silmangsarr1998@gmail.com";
        $object = "testing mail";
       $response= Mail::to($tomail)->send(new SendMail($mailmessage,$object));
        dd($response);
    }
}
