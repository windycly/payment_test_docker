<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;

use View;
use Validator;

use App\Payment;
use Redis;

use GatewayAController;

class PaymentController extends Controller
{
    protected $rules =
    [
        'customer_name' => ['required', 'min:5', 'max:255', 'regex:/^[a-z0-9 .,\'-]+$/i'],
        'customer_phone' => ['required', 'min:5', 'max:255', 'regex:/^[0-9 -\+]+$/i'],
        'currency' => 'required', 'min:3', 'max:3',
        'price' => ['required', 'max:255', 'regex:/^[0-9.]+$/i'],
        'card_holder' => ['required', 'min:5', 'max:255', 'regex:/^[a-z0-9 .,\'-]+$/i'],
        'card_numbers' => ['required', 'min:5', 'max:255', 'regex:/^[0-9 -]+$/i'],
        'card_expiration' => ['required', 'min:5', 'max:5', 'regex:/^(0[1-9]|10|11|12)\/[0-9]+$/i'],
        'card_cvv' => ['required', 'min:3', 'max:4', 'regex:/^[0-9]+$/i'],
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('payment.index', []);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => ['required', 'min:5', 'max:255', 'regex:/^[a-z0-9 .,\'-]+$/i'],
            'reference_code' => ['required', 'min:5', 'max:255', 'regex:/^[a-z0-9]+$/i'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'data' => $validator->getMessageBag()->toArray()
            ]);
        }

        // $payment = Payment::where('is_success', 1)
        //        ->where('customer_name', $request->get('customer_name'))
        //        ->where('reference_code', $request->get('reference_code'))
        //        ->first();

        $payment = json_decode(Redis::get($request->get('reference_code')), true);

        if (!empty($payment) && $payment['customer_name'] === $request->get('customer_name')) {
            return response()->json([
               'error' => false,
               'data' => view('payment.show')->with("data", $payment)->render()
            ]);
        }

        return response()->json([
            'error' => true,
            'data' => ["response_code" => ["Record not found."]]
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'data' => $validator->getMessageBag()->toArray()
            ]);
        }

        $payment = new Payment();
        $payment->customer_name = $request->get('customer_name');
        $payment->customer_phone = $request->get('customer_phone');
        $payment->currency = $request->get('currency');
        $payment->price = $request->get('price');

        // choose method and proceed to payment gateway
        $gatewayResponse = null;
        if ($this->isAMEX($request->get('card_numbers')) || GatewayAService::isGatewayACurrency($request->get('currency'))) {

            $payment->payment_gateway = "A";
            $gatewayAService = new GatewayAService();
            $gatewayResponse = $gatewayAService->submit($request);
        } else {
            $payment->payment_gateway = "B";
            $gatewayBService = new GatewayBService();
            $gatewayResponse = $gatewayBService->submit($request);
        }

        $payment->reference_code = $gatewayResponse->reference_code;
        $payment->is_success = $gatewayResponse->success;
        $payment->response_data = $gatewayResponse->data;
        $payment->save();

        Redis::set($payment->reference_code, $payment);
        // Redis::setex($payment->reference_code, 600, $payment);
        return response()->json(['error' => false, 'data' => json_decode(Redis::get($payment->reference_code))]);


        // return response()->json(['error' => false, 'data' => $payment]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function isAMEX($cardNumbers = '') {
        // https://www.cybersource.com/developers/getting_started/test_and_manage/best_practices/card_type_id/
        $requireDigits = ['34', '37'];
        return in_array(substr($cardNumbers, 0, 2), $requireDigits);
    }

}


class GatewayAService
{
    public static function isGatewayACurrency($currency = '') {
       $requireCurrencies = ['USD', 'AUD', 'EUR', 'JPY', 'CNY'];
       return in_array($currency, $requireCurrencies);
    }

    public function submit($data) {
        // call Gateway A API here and get response
        $responseSuccess = (bool)rand(0,1);
        $responseMessage = $responseSuccess ? "Gateway A: successful." : "Gateway A: payment is not successful.";

        // return response object in standard format, make sure reference_code is unique
        return (object)[
            "success" => $responseSuccess,
            "data" => $responseMessage,
            "reference_code" => md5($data->get('card_holder').$responseMessage.time()),
        ];
    }
}

class GatewayBService
{
    public function submit($data) {
        // call Gateway B API here and get response
        $responseSuccess = (bool)rand(0,1);
        $responseMessage = $responseSuccess ? "Gateway B: successful." : "Gateway B: payment is not successful.";

        // return response object in standard format, make sure reference_code is unique
        return (object)[
            "success" => $responseSuccess,
            "data" => $responseMessage,
            "reference_code" => md5($data->get('card_holder').$responseMessage.time()),
        ];
    }
}
