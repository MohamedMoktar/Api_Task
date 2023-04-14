<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;
use Exception;

class User_otp extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'otp',
        'expired_at',
    ];

     public function user(){
        
        return $this->belongsTo(User::class);
    }

    public function sendSms($recivedNumber){
        $massege='Register Otp is'.$this->otp;
        try{

            $token = getenv("TWILIO_TOKEN");
            $twilio_sid = getenv("TWILIO_SID");
            $twilio_verify_sid = getenv("TWILIO_FROM");
            $client = new Client($twilio_sid, $token);
            $client->messages->create($recivedNumber,[
                'From'=>$twilio_verify_sid,
                'Body'=>$massege,
            ]);
            info('sms sent successfully');
        }
        catch(Exception $e){
            info('Error :'.$e->getMessage());
        }
    }
}
