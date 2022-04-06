<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Transaction;
use App\Models\User;

class MidtransController extends Controller
{   
    public function chargeCard($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'Card Payment';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
            'item_details' => array(
                array(
                    'id' => $transaction->course_id,
                    'price' => $transaction->final_amount,
                    'quantity' => 1,
                    'name' => $data_payment['course_details']['title'],
                )
            ),
            'customer_details' => array(
                'first_name' => $data_payment['first_name'],
                'last_name' => $data_payment['last_name'],
                'email' => Auth::user()->email,
                'billing_address' => array(
                    'first_name' => $data_payment['first_name'],
                    'last_name' => $data_payment['last_name'],
                    'email' => Auth::user()->email,
                    'address' => $data_payment['address'],
                    'city' => $data_payment['city'],
                    'postal_code' => $data_payment['postal_code'],
                    'country_code' => $data_payment['country_code'],
                ),
            )
        );
         
        $chargeData = \Midtrans\CoreApi::charge($params);
        
        $transaction->charge_id = $chargeData->transaction_id;
        $transaction->status = $chargeData->transaction_status;
        $transaction->save();

        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeGopay($data_payment){
        \Midtrans\Config::$serverKey = $data_payment['api_key'];
        \Midtrans\Config::$isProduction = false;

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'GOPAY eWallet';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
                'callback_url' => 'http://dreamguru.me'   // optional
            ),
            'item_details' => array(
                array(
                    'id' => 'course-'. $transaction->course_id,
                    'price' => $transaction->final_amount,
                    'quantity' => 1,
                    'name' => $data_payment['course_details']['title'],
                )
            ),
            'customer_details' => array(
                'email' => Auth::user()->email,
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
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BNI Virtual Account';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BRIVA (BRI VIrtual Account)';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
        $transaction->status = 'pending';
        $transaction->save();

        $params = array(
            "payment_type" => "echannel",
            "transaction_details" => array(
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
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
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->order_id = 'DX-'.Str::random(5);
        $transaction->gateway = 'Midtrans';
        $transaction->payment_type = 'BCA Virtual Account';
        $transaction->amount = $data_payment['course_details']['price'];
        $transaction->final_amount = $data_payment['course_details']['price'];
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
