<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Requests\PayRequest;
use App\Services\SoapService;


class TransactionController extends Controller
{
    protected $soapService;

    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    
    public function pay(PayRequest $request)
    {
        $data = $request->validated();

        $response = $this->soapService->pay(
            $request->file('document'),
            $data['phone'],
            $data['amount']
        );

        return response()->json($response);
    }

    public function confirmPayment(ConfirmPaymentRequest $request)
    {
        $data = $request->validated();

        $response = $this->soapService->confirmPayment(
            $data['session_id'],
            $data['token']
        );

        return response()->json($response);
    }


    
}
