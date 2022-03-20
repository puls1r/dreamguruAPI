<?php

namespace App\Http\Controllers\Api\V1\Callback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Course;

class XenditCallbackController extends Controller
{

    public function __construct(){
        $token = request()->header('X-CALLBACK-TOKEN');
        $this->middleware('xendit:'.$token);
    }

    public function eWalletPaymentStatus(){
        $charge = json_decode(request()->getContent(), true);
        
        //proses pembayaran berhasil
        if($charge['data']['status'] == 'SUCCEEDED'){ 

            $transaction = Transaction::where('order_id',$charge['data']['reference_id'])->firstOrFail();
            $transaction->status = strtolower($charge['data']['status']);   //SUCCEEDED
            $transaction->save();

            $user = User::where('id', $transaction->user_id)->first();
            $course = Course::findOrFail($transaction->course_id);
            $user->courses()->attach($course->id, [
                'status' => 'in_progress',
                'is_purchased' => '1',
                'certificate' => NULL,
            ]);

            return response('Transaction Complete!', 201);
        }

        //status pembayaran berubah
        else{       

            $transaction = Transaction::where('order_id',$charge['data']['reference_id'])->firstOrFail();
            $transaction->status = strtolower($charge['data']['status']);   //SUCCEEDED
            $transaction->save();

            return response('Status Pembayaran berhasil diupdate', 200);
        }
    }

    public function retailPaymentStatus(){
        $charge = json_decode(request()->getContent(), true);

        $transaction = Transaction::where('charge_id', $charge['payment_code'])->firstOrFail();
        $transaction->status = strtolower($charge['data']['status']);
        $transaction->save();

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
