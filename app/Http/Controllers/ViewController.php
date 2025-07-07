<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\NfcTag;
use App\Models\Freebie;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Services\SessionService;

class ViewController extends Controller
{
    protected $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }



    public function returnView($currentProgress){

        $hotelId = session()->get('hotel_id');
        $nfcId = session()->get('nfc_id');
        $email = session()->get('user_email');

        switch ($currentProgress) {

            case "questionare":
                //about the hotel view (questions)
                return $this->viewTwo($hotelId);
                break;

            case "claim-reward":
                //reward view
                return $this->claimReward($hotelId, $email);
                break;

            case "used-reward":
                //reward claimed view
                return $this->usedReward($hotelId, );
                break;

            default:
                //about the user view
                return $this->viewOne($hotelId, $nfcId);
                break;
        }
    }


    public function viewOne($hotelId, $nfcId){
        $clientData = DB::table('clients')->where('HOTEL_ID', $hotelId)->first();
        //$clientData = Client::where('HOTEL_ID', $hotelId)->first();
        
        $hotelName = $clientData->HOTEL_NAME;
        $hexColor = $clientData->HEX_COLOR;
        $logoImg = $clientData->LOGO_IMG;
        $bannerImg = $clientData->BANNER_IMG;

        //Creating slug for the file path to the images
        $slug = Str::slug($hotelName, '-');
        $path = 'uploads/' . $slug . '/';
        $logoImgPath=$path. '/'. $logoImg;
        $bannerImgPath=$path. '/'. $bannerImg;

        //ADD YOUR CODE HERE !!
        $reward = NfcTag::where('HOTEL_ID', $hotelId)
            ->where('NFC_ID', $nfcId)
            ->first();

        $reward = $reward->REWARD;

        return view('home', [
            'logoImgPath' => $logoImgPath,
            'bannerImgPath' => $bannerImgPath,
            'reward' => $reward
        ]); 
    }


    public function viewTwo(){
        $current_step = "questionare"; //step 1 is the question view
        $this->sessionService->updateUserProgress($current_step);

        // Get the user email and hotel ID (assuming they are stored in the session)
        $userEmail = session()->get('user_email');
        $hotelId = session()->get('hotel_id');

        // Step 2: Check if the review already exists for the specific user email and hotel ID
        $review = Review::where('USER_EMAIL', $userEmail)
                        ->where('HOTEL_ID', $hotelId)
                        ->first();

        if ($review) {
            // A review already exists, so return the StoredReview view
            // Pass the necessary data to the view (e.g., review text and hashtag)
            $hashtag = $review->HASHTAG;
            session()->put('hashtag', $hashtag);
            $reviewText = $review->REVIEW; // assuming the review text is stored in the 'review_text' column
            $clientData = Client::where('HOTEL_ID', $hotelId)->first();
            $gmbLink = $clientData->GMB_LINK; // Fetch the GMB link
            
            return view('storedReview', compact('hashtag', 'reviewText', 'gmbLink'));
        }


        // Read and parse the JSON file
        $jsonPath = base_path('resources/json/questions.json');
        $jsonFile = File::get($jsonPath);
        $jsonData = json_decode($jsonFile, true);

        // Fetch the hotel name from the database using the hotel ID from the session
        $hotelId = session()->get('hotel_id');
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $hotelName = $clientData->LOCATION;
        // Replace placeholder with the actual hotel name in the questions

        foreach ($jsonData['ranking_features'] as &$feature) {
            foreach ($feature['questions'] as &$question) {
                $question['text'] = str_replace('{{Hotel name}}', $hotelName, $question['text']);
            }
        }
        
        // Pass the dynamic questions to the view
        return view('question', ['jsonData' => $jsonData]);
    }


    public function claimReward($hotelId, $email)
    {
        // Find the user based on email and hotel ID
        $user = User::where('EMAIL', $email)
                    ->where('HOTEL_ID', $hotelId)
                    ->first();

       
        if (!$user) {
            // If user is not found
            return redirect()->back()->with('error', 'User not found for this hotel');
        }

        // Retrieve the freebie based on HOTEL_ID and USER_ID
        $freebie = Freebie::where('HOTEL_ID', $hotelId)
                        ->where('USER_ID', $user->USER_ID)
                        ->first();

        if (!$freebie) {
            // If no freebie is found
            return redirect()->back()->with('error', 'No freebie found for this user at this hotel');
        }

        // Check if the freebie is already scanned
        if ($freebie->is_scanned) {
            // Redirect to the RewardClaimed view
            return view('RewardClaimed');
        }

        // Get the freebie QR code URL
        $qrCodeUrl = $freebie->freebie_qr_code_url;

        // Pass the QR code URL to the view
        return view('reward', compact('qrCodeUrl'));
    } 
        
        
    public function usedReward(){
        return view('tryAgain');
    }


}
