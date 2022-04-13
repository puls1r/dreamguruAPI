<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\Payment\XenditController;
use App\Http\Controllers\Api\V1\Payment\MidtransController;
use App\Models\Course;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\Transaction;

class ChargeController extends Controller
{
    private $midtrans_key = 'SB-Mid-server-sCcKWBO76FPcN7c5TqMn3e0l';
    private $xendit_key = 'xnd_development_WYk7MbVJYOgCE3UZakPuSf23UuCEHdqvMwX4ZkgiK74B4EkVI1KIRw5QrW5sDq';

    public function __invoke(Request $request){
        $data_payment = $request->all();
        //step 1 : check apakah user sudah punya corse ini
        //step 2 : check apakah ada pembayaran yang sedang aktif untuk user 
        //step 3 : kalkulasi harga dari table course + voucher
        $course = Course::findOrFail($request->course_id);
        
        $data_payment['course_details'] = $course->toArray();
        //step 4 : charge
        switch($data_payment['payment_type']){
            case "credit_card":
                Validator::make($data_payment, [
                    "first_name" => ['required', 'string', 'max:30'],
                    "last_name" => ['required', 'string', 'max:30'],
                    "address" => ['required', 'string', 'max:100'],
                    "city" => ['required', 'string', 'max:30'],
                    "postal_code" => ['required', 'numeric', 'max:10'],
                    "country_code" => ['required', 'string', 'max:30'],
                ]);

                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeCard($data_payment));
            case "mandiriVA":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeMandiriVA($data_payment));
            case "bcaVA":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeBCAVA($data_payment));
            case "briVA":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeBRIVA($data_payment));
            case "permataVA":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargePermataVA($data_payment));
            case "bniVA":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeBNIVA($data_payment));
            case "gopay":
                $data_payment['api_key'] = $this->midtrans_key;
                return response(MidtransController::chargeGopay($data_payment));
            case "dana":
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeDana($data_payment));
            case "linkAja":
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeLinkAja($data_payment));
            case "shopeePay":
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeShopeePay($data_payment));
            case "ovo":
                $this->validate($request, [
                    'phone_number' => ['required', 'numeric']
                ]);
                
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeOVO($data_payment));
            case "alfamart":
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeAlfamart($data_payment));
            case "indomaret":
                $data_payment['api_key'] = $this->xendit_key;
                return response(XenditController::chargeIndomaret($data_payment));
            case "dreamguru":
                //check if course is free
                if($course->price == 0 || ($course->is_on_discount && $course->discount_price == 0)){
                    $transaction = new Transaction;
                    $transaction->user_id = Auth::id();
                    $transaction->course_id = $data_payment['course_details']['id'];
                    $transaction->gateway = 'dreamguru';
                    $transaction->due_date = null;
                    $transaction->payment_type = 'dreamguru';
                    $transaction->amount = $data_payment['course_details']['price'];
                    //check if course is on discount
                    if($data_payment['course_details']['is_on_discount']){
                        $transaction->final_amount = $data_payment['course_details']['discount_price'];
                    }
                    else{
                        $transaction->final_amount = $data_payment['course_details']['price'];
                    }
                    $transaction->status = 'settlement';
                    $transaction->save();

                    $user = User::where('id', $transaction->user_id)->first();
                    $user->courses()->attach($course->id, [
                        'status' => 'in_progress',
                        'is_purchased' => '1',
                        'certificate' => NULL,
                    ]);

                    return response('Transaction Complete!', 201);
                }
                else{
                    return response('forbidden', 403);
                }
            default :
                return response('payment not found!', 404);
            
        }
    }
}
