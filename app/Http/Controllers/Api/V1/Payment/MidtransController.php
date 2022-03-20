<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Controllers\Api\V1\Payment\XenditController;

class MidtransController extends Controller
{
    private $api_key= 'SB-Mid-server-Z0WqZ60NiCXVrKeoKr0D3miW';
    
    public function chargeCard($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;
        
        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'Card Payment';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            'transaction_details' => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->final_amount,
            ),
            'payment_type' => 'credit_card',
            'credit_card'  => array(
                'token_id'      => $data_payment['token_id'],
                'authentication'=> true,
            ),
            // 'item_details' => array(
            //     array(
            //         'id' => 'a1',
            //         'price' => 699000,
            //         'quantity' => 1,
            //         'name' => 'Talent Premium',
            //     )
            // ),
            // 'customer_details' => array(
            //     'first_name' => $request->first_name,
            //     'last_name' => $request->last_name,
            //     'email' => Auth::user()->email,
            //     'billing_address' => array(
            //         'first_name' => $request->first_name,
            //         'last_name' => $request->last_name,
            //         'email' => Auth::user()->email,
            //         'address' => $request->address,
            //         'city' => $request->city,
            //         'postal_code' => $request->postal_code,
            //         'country_code' => $request->country_code,
            //     ),
            // )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params)->status;
        
        $payment->transaction_id = $chargeData->transaction_id;
        $payment->status = $chargeData->transaction_status;
        $payment->save();

        if(isset($chargeData->redirect_url) && $chargeData->transaction_status == 'pending'){ //berarti membutuhkan 3ds
            return view('payment.3ds', ['chargeData' => $chargeData]);
        }

        return redirect($chargeData->actions['1']->url);
    }

    public function chargeGopay($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'GOPAY eWallet';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            'transaction_details' => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ),
            'payment_type' => 'gopay',
            'gopay' => array(
                'enable_callback' => true,                // optional
                'callback_url' => 'http://dreamguruapi.me'   // optional
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeBNIVA($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BNI Virtual Account';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "bank_transfer",
            "transaction_details" => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ),
            "bank_transfer" => array(
                "bank" => "bni"
            ),
            "customer_details" => array(
                // "first_name" => "Budi",
                // "last_name" => "Susanto",
                "email" => Auth::user()->email,
                "phone" => Auth::user()->profile->phone_number,
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeBCAVA($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "bank_transfer",
            "transaction_details" => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ),
            "bank_transfer" => array(
                "bank" => "bca"
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeBRIVA($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BRIVA (BRI VIrtual Account)';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "bank_transfer",
            "transaction_details" => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ),
            "bank_transfer" => array(
                "bank" => "bri"
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeMandiriVA($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "echannel",
            "transaction_details" => array(
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->amount,
            ),
            "echannel" => array(
                "bill_info1" => "Payment:",
                "bill_info2" => "Online purchase"
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargePermataVA($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_price'];
        $transaction->final_amount = $data_payment['course_price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "permata",
            "transaction_details" => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ),
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function cardPaymentStatus(Request $request){
        $response = json_decode($request['response'], true);
        
        if($response['transaction_status'] == 'capture'){
            if($response['fraud_status'] == 'challenge'){
                echo 'pembayaran anda sedang dalam pertimbangan, mohon pantau dashboard untuk mendapatkan informasi lebih lanjut..';
                
                echo "<script>setTimeout(function(){ window.location.href = '/home'; }, 5000);</script>"; 
            }
            else{
                echo 'Pembayaran berhasil, mengalihkan...';
                echo "<script>setTimeout(function(){ window.location.href = '/home'; }, 5000);</script>";
            }
        }
        if($response['transaction_status'] == 'deny'){
            echo 'Transaksi gagal';
            return header("Refresh", "5;url=/home"); 
        }
    }
}
