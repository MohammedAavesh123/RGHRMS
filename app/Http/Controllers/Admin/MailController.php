<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Mail;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function basic_email()
    {

     $message = array('name'=>"pinky",'mobile'=>'7734012645','setPasswordURL'=> url('/setpassword/'.base64_encode(5)));
     Mail::send('Admin.employee.mail', $message, function ($m) use ($message){

       $m->from('info@gameking11.com', "GAMEKING");

       $m->to("agarwalpinky666@gmail.com")->subject("Subject");

     });
   }

}
