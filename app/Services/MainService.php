<?php

namespace App\Services;

class MainService
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }



    public function index($hotelId, $nfcId){
        $test = $this->sessionService->checkSession($hotelId, $nfcId);

        $returnView= $this->ViewController->returnView();

    }
}