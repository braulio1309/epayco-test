<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoadWalletRequest;
use App\Http\Requests\PayRequest;
use App\Services\SoapService;


class WalletController extends Controller
{
    protected $soapService;

    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    public function loadWallet(LoadWalletRequest $request)
    {
        $data = $request->validated();

        $response = $this->soapService->loadWallet(
            $request->file('document'),
            $data['phone'],
            $data['value']
        );

        return response()->json($response);
    }

    
}
