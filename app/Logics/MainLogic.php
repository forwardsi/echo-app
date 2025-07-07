<?php

namespace App\Logics;
use App\Services\SessionService;
use App\Http\Controllers\ViewController;

use App\Models\Client;
use App\Models\User;

class MainLogic
{
    protected $sessionService;
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }





    public function mainIndex($hotelId, $nfcId){

        $currentProgress = session()->get('progress');
        $currentProgress = $this->sessionService->checkSession($hotelId, $nfcId);

        $viewController = new ViewController($this->sessionService);
        $returnView = $viewController->returnView($currentProgress);

        return $returnView;

    }

    //This function is only called if the user tries to register again and he didnt claim the reward in the first place
    public function secondaryIndex($currentProgress){
        $viewController = new ViewController($this->sessionService);
        $returnView = $viewController->returnView($currentProgress);

        return $returnView;
    }

    public function returnHotelName(){
         // Fetch the hotel name from the database using the hotel ID from the session
         $hotelId = session()->get('hotel_id');
         $clientData = Client::where('HOTEL_ID', $hotelId)->first();
         $hotelName = $clientData->LOCATION;
         return $hotelName;
    }

    public function returnRankingFeature(){
        $hotelId = session()->get('hotel_id');
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $rankingFeature = $clientData->RANKING_FEATURE;
        return $rankingFeature;

    }
}