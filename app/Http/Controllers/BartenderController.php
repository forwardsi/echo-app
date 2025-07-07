<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Client;
use App\Models\User;
use App\Models\NfcTag;
use App\Models\Freebie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\SessionService;

class BartenderController extends Controller
{
    protected $sessionService;
    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }
    
    public function showReviewsForHotel($hotelId)
    {
        $reviews = Review::where('HOTEL_ID', $hotelId)
            ->orderBy('created_at', 'desc') // ✅ newest first
            ->get();
    
        return view('manualCheck', compact('reviews'));
    }



    public function markAsPosted()
    {
        $hashtag = request('hashtag');
        $stars = request('stars'); // ⭐ Get the selected star rating
    
        $review = Review::where('HASHTAG', $hashtag)->first();
    
        if (!$review) {
            return response()->json(['success' => false, 'message' => 'Review not found'], 404);
        }
    
        // Update review fields
        $review->IS_POSTED = true;
        $review->confirmed_at = now();
    
        // Only update stars if it's a valid value (1–5) and not already set
        if (in_array($stars, ['1','2','3','4','5']) && $review->stars == 0) {
            $review->stars = $stars;
        }
    
        $review->save();
    
        $nfcId = $review->NFC_ID;
        $hotelId = $review->HOTEL_ID;
        $userId = $review->USER_ID;
    
        return $this->generateFreebieQrManual($nfcId, $hotelId, $userId);
    }



    public function generateFreebieQrManual($nfcId,$hotelId,$userId)
    {
        // Define the API endpoint
        $apiEndpoint = env('GENERATE_QR_CODE_API_ENDPOINT');
        

        // Get user details based on email and hotel_id
        $user = User::where('USER_ID', $userId)
                ->where('HOTEL_ID', $hotelId)
                ->first();

        // Get hotel details
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $hotelName = $clientData->HOTEL_NAME;

        // Retrieve user-specific details
        $phone = $user->PHONE_NUMBER;
        $full_name =  $user->FULL_NAME;
        $country = $user->COUNTRY_OF_RESIDENCE;
        $email = $user->EMAIL;
        $hotel_name = $hotelName; 
    

        $nfcTag = NfcTag::where('HOTEL_ID', $hotelId)
                        ->where('NFC_ID', $nfcId)
                        ->first();

        $freebie = $nfcTag->REWARD;
        $reward = $freebie;

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
            'posted' => 1,
        ];

        
        // Make the HTTP POST request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($apiEndpoint, $payload)->json();

        $freebieQrCodeUrl = $response['freebie_qr_code_url'];
        $freebie_code = $response['freebie_code'];

        $freebie = new Freebie();
        $freebie->freebie_code = $freebie_code;
        $freebie->freebie_qr_code_url = $freebieQrCodeUrl;
        $freebie->HOTEL_ID = $hotelId;
        $freebie->USER_ID = $userId;
        $freebie->save();
    
        $current_step = "claim-reward";
        $current_step_session = $this->sessionService->updateUserProgress($current_step);

        // Update progress in the database for the current user identified
        if ($user) {
            $user->PROGRESS = $current_step;
            $user->save();
        }


        return view('success', compact('reward', 'email'));
    }



    public function showQrCode()
    {
        $userId = request('userId');

        $user = User::where('USER_ID', $userId)
                ->first();

        $qrCode = Freebie::where('USER_ID', $userId) ->first();
        $qrCodeUrl = $qrCode -> freebie_qr_code_url;
       
       
        return view('success', compact('qrCodeUrl'));
        // Return a success response
      
    }


    public function manualCheck(Request $request)
{
    $query = Review::query()
    ->leftJoin('freebies', 'reviews.USER_ID', '=', 'freebies.USER_ID') // Join with freebies table
        ->select('reviews.*', 'freebies.is_scanned'); // Select is_scanned field

    // Filter by Is Posted
    if ($request->has('is_posted') && $request->is_posted !== 'all') {
        $query->where('IS_POSTED', $request->is_posted);
    }

    // Search by Hashtag, Review, or Email
    if ($request->has('search') && $request->search !== '') {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('HASHTAG', 'like', "%{$search}%")
              ->orWhere('REVIEW', 'like', "%{$search}%")
              ->orWhere('USER_EMAIL', 'like', "%{$search}%"); // Add search by email
        });
    }

    // Sort by Created At or Email
    $sortBy = $request->get('sort_by', 'desc'); // Default to descending
    if ($sortBy === 'email') {
        $query->orderBy('USER_EMAIL', 'asc'); // Sort alphabetically by email
    } else {
        $query->orderBy('created_at', $sortBy); // Sort by creation date
    }

    // Get the filtered and sorted reviews
    $reviews = $query->get();

    return view('manualCheck', compact('reviews'));
}




    
}
