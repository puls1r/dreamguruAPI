<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;
use Xendit\Xendit;

class TransactionController extends Controller
{
    public function index(){
        $transactions = Transaction::where('user_id', Auth::id())->get();
        
        return response($transactions);
    }

    public function show($order_id){
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();
        
        //get transaction detail from payment gateway
        if($transaction->gateway == 'midtrans'){
            \Midtrans\Config::$serverKey = 'SB-Mid-server-sCcKWBO76FPcN7c5TqMn3e0l';
            \Midtrans\Config::$isProduction = false;

            $status = \Midtrans\Transaction::status($order_id);
            $status = json_decode(json_encode($status) , true);
            if(isset($status['va_numbers'])){
                $transaction->va_number = $status['va_numbers'][0]['va_number'];
            }
            else if(isset($status['bill_key'])){
                $transaction->va_number = $status['bill_key'];
                $transaction->biller_code = $status['biller_code'];
            }
            else if(isset($status['permata_va_number'])){
                $transaction->va_number = $status['permata_va_number'];
            }
            else{
                $transaction->details = $status;
            }

            return response($transaction);
        }

        else{
            Xendit::setApiKey('xnd_development_WYk7MbVJYOgCE3UZakPuSf23UuCEHdqvMwX4ZkgiK74B4EkVI1KIRw5QrW5sDq');

            $status = \Xendit\EWallets::getEWalletChargeStatus($transaction->charge_id);
            $status = json_decode(json_encode($status) , true);
            $transaction->details = $status;

            return response($transaction);
        }
    }
}
