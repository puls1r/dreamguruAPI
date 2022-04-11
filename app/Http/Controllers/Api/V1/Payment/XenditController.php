<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Xendit\Xendit;
use App\Models\Transaction;
use App\Models\User;

class XenditController extends Controller
{
    
    public function chargeDana($data_payment){
        Xendit::setApiKey($data_payment['api_key']);

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'dana';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $ChargeParams = [
            'reference_id' => $transaction->order_id,
            'currency' => 'IDR',
            'amount' => $transaction->final_amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_DANA',
            'channel_properties' => [
                'success_redirect_url' => 'http://dreamguru.id/course/' . $data_payment['course_details']['slug'],
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
        
        $chargeData = \Xendit\EWallets::createEWalletCharge($ChargeParams);
        
        $transaction->charge_id = $chargeData['id'];
        $transaction->status = $chargeData['status'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
        
    }

    public function chargeLinkAja($data_payment){

        Xendit::setApiKey($data_payment['api_key']);

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'linkAja';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $ChargeParams = [
            'reference_id' => $transaction->order_id,
            'currency' => 'IDR',
            'amount' => $transaction->final_amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_LINKAJA',
            'channel_properties' => [
                'success_redirect_url' => 'http://dreamguru.id/course/' . $data_payment['course_details']['slug'],
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
        
        $chargeData = \Xendit\EWallets::createEWalletCharge($ChargeParams);
        
        $transaction->charge_id = $chargeData['id'];
        $transaction->status = $chargeData['status'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;

        
    }

    public function chargeShopeePay($data_payment){
        Xendit::setApiKey($data_payment['api_key']);

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'shopeePay';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $ChargeParams = [
            'reference_id' => $transaction->order_id,
            'currency' => 'IDR',
            'amount' => $transaction->final_amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_SHOPEEPAY',
            'channel_properties' => [
                'success_redirect_url' => 'http://dreamguru.id/course/' . $data_payment['course_details']['slug'],
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
        
        $chargeData = \Xendit\EWallets::createEWalletCharge($ChargeParams);
        
        $transaction->charge_id = $chargeData['id'];
        $transaction->status = $chargeData['status'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeOVO($data_payment){
        Xendit::setApiKey($data_payment['api_key']);
        
        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'ovo';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $ChargeParams = [
            'reference_id' => $transaction->order_id,
            'currency' => 'IDR',
            'amount' => $transaction->final_amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'ID_OVO',
            'channel_properties' => [
                'mobile_number' => '+62'.$data_payment['phone_number'],
                'success_redirect_url' => 'http://dreamguru.id/course/' . $data_payment['course_details']['slug'],
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
        
        $chargeData = \Xendit\EWallets::createEWalletCharge($ChargeParams);
        
        $transaction->charge_id = $chargeData['id'];
        $transaction->status = $chargeData['status'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }


    public function chargeAlfamart($data_payment){

        Xendit::setApiKey($data_payment['api_key']);

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'alfamart';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $params = [
            'external_id' => $transaction->order_id,
            'retail_outlet_name' => 'ALFAMART',
            'name' => Auth::user()->profile->name,
            'expected_amount' => $transaction->final_amount,
            ];
        
        $chargeData = \Xendit\Retail::create($params);
        $transaction->charge_id = $chargeData['payment_code'];
        $transaction->status = $chargeData['status'];
        $transaction->retail_payment_id = $chargeData['id'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;
    }

    public function chargeIndomaret($data_payment){

        Xendit::setApiKey($data_payment['api_key']);

        $transaction = new Transaction;
        $transaction->user_id = Auth::id();
        $transaction->course_id = $data_payment['course_details']['id'];
        $transaction->gateway = 'xendit';
        $transaction->due_date = date('Y-m-d H:i:s', strtotime('+1 day'));
        $transaction->payment_type = 'indomaret';
        $transaction->amount = $data_payment['course_details']['price'];
        //check if course is on discount
        if($data_payment['course_details']['is_on_discount']){
            $transaction->final_amount = $data_payment['course_details']['discount_price'];
        }
        else{
            $transaction->final_amount = $data_payment['course_details']['price'];
        }
        $transaction->status = 'pending';
        $transaction->save();

        $params = [
            'external_id' => $transaction->order_id,
            'retail_outlet_name' => 'INDOMARET',
            'name' => Auth::user()->profile->name,
            'expected_amount' => $transaction->final_amount,
            ];
        
        $chargeData = \Xendit\Retail::create($params);
        $transaction->charge_id = $chargeData['payment_code'];
        $transaction->status = $chargeData['status'];
        $transaction->retail_payment_id = $chargeData['id'];
        $transaction->save();
        
        $chargeData = json_decode(json_encode($chargeData), true);
        return $chargeData;

    }
    
    public function payIndomaret($id){
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->post('https://api.xendit.co/fixed_payment_code/simulate_payment', [
            "headers" => [
                "Authorization" => "Basic eG5kX2RldmVsb3BtZW50X0dpSGFYQkIweFU3S1dCNVpTcklGSkthMGMxT2kybkN3V2xTbklUV3ZCTnFSWVFuQVJ5U0diOGhCdjNTd0RjOg=="
            ],
            "form_params" => [
                "retail_outlet_name" =>  "INDOMARET",
                "payment_code" =>  $id,
                "transfer_amount" =>  699000
            ]
        ]);

        return redirect('/home');
    }

    public function payAlfamart($id){
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->post('https://api.xendit.co/fixed_payment_code/simulate_payment', [
            "headers" => [
                "Authorization" => "Basic eG5kX2RldmVsb3BtZW50X0dpSGFYQkIweFU3S1dCNVpTcklGSkthMGMxT2kybkN3V2xTbklUV3ZCTnFSWVFuQVJ5U0diOGhCdjNTd0RjOg=="
            ],
            "form_params" => [
                "retail_outlet_name" =>  "ALFAMART",
                "payment_code" =>  $id,
                "transfer_amount" =>  699000
            ]
        ]);

        return redirect('/home');
    }
    
    
}
