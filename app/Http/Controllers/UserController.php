<?php
namespace App\Http\Controllers;

use App\Services\SoapService;
use App\Http\Requests\RegisterClientRequest;
use Illuminate\Http\RedirectResponse;


class UserController extends Controller
{
    protected $soapService;

    public function __construct(SoapService $soapService)
    {
        $this->soapService = $soapService;
    }

    public function registerClient(RegisterClientRequest $request)
    {
        $data = $request->validated();

        $response = $this->soapService->registerClient(
            $data['document'],
            $data['name'],
            $data['email'],
            $data['phone']
        );

        return response()->json($response);
    }
}
