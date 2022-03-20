<?php

namespace App\Http\Controllers\Api\V1\Callback;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;
use App\Models\UserCourse;

class MidtransCallbackController extends Controller
{
    public function paymentStatus(){
        $charge = json_decode(request()->getContent(), true);
        
        //proses pembayaran berhasil
        if($charge['transaction_status'] == 'settlement' || $charge['transaction_status'] == 'capture'){ 

            $transaction = Transaction::where('order_id', $charge['order_id'])->first();
            $transaction->status = $charge['transaction_status'];  
            $transaction->save();

            //untuk card transaction
            if(isset($charge['fraud_status'])){
                if($charge['fraud_status'] == 'deny'){
                    return response('Transaksi Gagal', 200);
                }

                else if($charge['fraud_status'] == 'challenge'){
                    $transaction->status = $charge['fraud_status'];  //challenge, tahan perubahan status
                    $transaction->save();
                    return response('Pembayaran sedang dalam pertimbangan!', 201);
                }
                else{           //fraud status accept
                    $user = User::where('id', $transaction->user_id)->firstOrFail();
                    if($user->courses()->find($transaction->course_id) ){   //berarti callback berupa perubahan status capture ke settlement
                        return response('OK',200);
                    }
                    else{
                        $course = Course::findOrFail($transaction->course_id);
                        $user->courses()->attach($course->id, [
                            'status' => 'in_progress',
                            'is_purchased' => '1',
                            'certificate' => NULL,
                        ]);

                        return response('Transaction Complete!',200);
                    }
                }
            }
            //untuk pembayaran lain selain card transaction
            else{
                $user = User::where('id', $transaction->user_id)->first();
                $course = Course::findOrFail($transaction->course_id);
                $user->courses()->attach($course->id, [
                    'status' => 'in_progress',
                    'is_purchased' => '1',
                    'certificate' => NULL,
                ]);
                return response('Transaction Complete!', 201);
            }
            

        }
        //jika terjadi reversal atau cancel
        else if($charge['transaction_status'] == 'deny' || $charge['transaction_status'] == 'cancel'){
            $transaction = Transaction::where('order_id', $charge['order_id'])->first();
            $transaction->status = $charge['transaction_status'];  
            $transaction->save();


            $user = User::where('id', $transaction->user_id)->first();
            $user->level = 0;       //ubah ke premium user
            $user->save();

            return response('Status Pembayaran berhasil diupdate!', 200);
        }

        //status pembayaran berubah
        else{       

            $transaction = Transaction::where('order_id',$charge['order_id'])->first();
            $transaction->status = $charge['transaction_status'];  
            $transaction->save();

            return response('Status Pembayaran berhasil diupdate', 200);
        }

    }
}
