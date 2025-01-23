<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoadWalletRequest;
use App\Http\Requests\ConsultBalanceRequest;
use App\Services\SoapService;
use Illuminate\Http\JsonResponse;


class WalletController extends Controller
{
    protected $soapService;

    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    public function loadWallet(LoadWalletRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->soapService->loadWallet(
            $request->file('document'),
            $data['phone'],
            $data['value']
        );

        return response()->json($response);
    }

    public function consultWallet(ConsultBalanceRequest $request): JsonResponse
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
