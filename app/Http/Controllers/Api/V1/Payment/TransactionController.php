<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(){
        $transactions = Transaction::where('user_id', Auth::id())->get();
        
        return response($transactions);
    }

    public function show($order_id){
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();
        
        //get transaction detail from payment gateway
        if($transaction->gateway == 'Midtrans'){
            \Midtrans\Config::$serverKey = 'SB-Mid-server-Z0WqZ60NiCXVrKeoKr0D3miW';
            \Midtrans\Config::$isProduction = false;

            $status = \Midtrans\Transaction::status($order_id);
            $status = json_decode(json_encode($status) , true);
            $transaction->details = $status;

            return response($transaction);
        }
    }
}
