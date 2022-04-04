<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Http\Controllers\Api\V1\Payment\XenditController;
use App\Http\Controllers\Api\V1\Payment\MidtransController;

class ChargeController extends Controller
{
    private $midtrans_key = 'SB-Mid-server-Z0WqZ60NiCXVrKeoKr0D3miW';
    private $xendit_key = 'xnd_development_D52med3Ipx8gdSCISxQhpDcUnHoov9vBo0uqJxqVvw76a08Rmkek8JCIvBR6xxc';

    public function __invoke(Request $request){
        $data_payment = $request->all();
        //step 1 : check apakah user sudah punya corse ini
        //step 2 : check apakah ada pembayaran yang sedang aktif untuk user 
        //step 3 : kalkulasi harga dari table course + discount
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
            default :
                return response('payment not found!', 404);
            
        }
    }
}
