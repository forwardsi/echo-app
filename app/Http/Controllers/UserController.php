<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\NfcTag;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\Services\SessionService;
use App\Logics\MainLogic;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    protected $sessionService;
    protected $mainLogic;

    public function __construct(SessionService $sessionService, MainLogic $mainLogic )
    {
        $this->sessionService = $sessionService;
        $this->mainLogic = $mainLogic;
    }

    public function store(){ 
        $email = request('email');
        $userName = request('full_name');
        $hotelId = session()->get('hotel_id');
        $nfcId = session()->get('nfc_id');

        

        $existingUser = $this->validateUser($email, $hotelId);
        if ($existingUser) {
            // User already exists, return appropriate response or view
            return $existingUser;
        }

        $userId = mt_rand(100000, 999999);
        session()->put('user_id', $userId);

        

        $user = new User;
        $user -> USER_ID = $userId;
        $user -> FULL_NAME = request('full_name');
        $user -> EMAIL = request('email');
        $user -> PROMOTIONAL_MESSAGE = request()->has('promotional_message');
        $user -> HOTEL_ID = $hotelId;
        $user -> PROGRESS = "registration";
        $user -> save();

        session()->put('user_email', $email);
        session()->put('user_name', $userName);
        

       
        // Send the user data to sendpulse
        $posted = 0;
        $this->addToSendpulse($posted);

        //step 1 is the question view
        $current_step = "questionare"; 
        $current_step = $this->sessionService->updateUserProgress($current_step);

        $data = $this->mainLogic->mainIndex($hotelId, $nfcId);

        
        return $data;
    }

    public function validateUser($email, $hotelId){

        // Check if the user already exists in the database
        $existingUser = User::where('EMAIL', $email)
        ->where('HOTEL_ID', $hotelId)
        ->first();

        if ($existingUser) {
            $userName = $existingUser["FULL_NAME"];
            $email = $existingUser["EMAIL"];
            $userId = $existingUser["USER_ID"];
            session()->put('user_id', $userId);
            session()->put('user_email', $email);
            session()->put('user_name', $userName);

            $progress = $existingUser->PROGRESS;
            $data = $this->mainLogic->secondaryIndex($progress);
            return $data;
        }

    }

    public function addToSendpulse($posted)
    {
        // Define the API endpoint
        $apiEndpoint = env('GENERATE_QR_CODE_API_ENDPOINT');
        
        $email = session()->get('user_email');
        $hotelId = session()->get('hotel_id');
        $userId = session()->get('user_id');

        // Get user details based on email and hotel_id
        $user = User::where('EMAIL', $email)
                ->where('HOTEL_ID', $hotelId)
                ->first();

        // Get hotel details
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $hotelName = $clientData->HOTEL_NAME;

        // Retrieve user-specific details
        $phone = $user->PHONE_NUMBER;
        $full_name =  $user->FULL_NAME;
        $country = $user->COUNTRY_OF_RESIDENCE;
        $hotel_name = $hotelName; 

        $nfcId = session()->get('nfc_id');  // or use another method to retrieve it
        $nfcTag = NfcTag::where('HOTEL_ID', $hotelId)
                        ->where('NFC_ID', $nfcId)
                        ->first();

        $freebie = $nfcTag->REWARD;

        // Generate a random freebie_code with 10 characters
        $freebie_code = Str::random(10);  // This replaces the manual loop for generating the code

        // Construct the payload
        $payload = [
            'email' => $email,
            'phone' => $phone,
            'full_name' => $full_name,
            'country' => $country,
            'hotel_name' => $hotel_name,
            'freebie' => $freebie,
            'freebie_code' => $freebie_code,
            'posted' => $posted,
        ];

        // Make the HTTP POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($apiEndpoint, $payload)->json();

    }

    
}