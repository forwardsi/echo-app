<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\User;
use App\Models\NfcTag;
use App\Models\Review;
use App\Models\Freebie;
use App\Models\FactSheet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use App\Services\SessionService;
use App\Logics\MainLogic;

class ApiController extends Controller
{

    protected $sessionService;
    protected $mainLogic;

    public function __construct(SessionService $sessionService, MainLogic $mainLogic )
    {
        $this->sessionService = $sessionService;
        $this->mainLogic = $mainLogic;
    }




public function generateReview(Request $request)
{
    $openaiApiKey = env('OPENAI_API_KEY');
    $openaiApiModel = env('OPENAI_API_MODEL_3');
    $openaiApiEndpoint = env('OPENAI_API_ENDPOINT');

    // Load submitted answers
    $data = $request->all();
    $filteredData = [];
    foreach ($data as $key => $value) {
        if (strpos($key, 'question_label') === 0 || strpos($key, 'question') === 0) {
            $filteredData[$key] = $value;
        }
    }

    // Extract question and answer pairs
    $qas = [];
    for ($i = 1; $i <= 5; $i++) {
        $labelKey = 'question_label' . $i;
        $answerKey = 'question' . $i;
        if (isset($filteredData[$labelKey]) && isset($filteredData[$answerKey])) {
            $qas[] = [$filteredData[$labelKey], $filteredData[$answerKey]];
        }
    }

    // Get hotel ID and fact sheet
    $hotelId = session()->get('hotel_id');
    $hotelName = $this->mainLogic->returnHotelName(); // fallback name
    $factSheetRow = FactSheet::where('hotel_id', $hotelId)->first();

    if ($factSheetRow) {
        $factSheet = json_decode($factSheetRow->fact_sheet, true);
        $hotelName = $factSheet['basic_info']['name'] ?? $hotelName;
    }

    // Generate tone and undertone
    $combinedTone = $this->getCombinedTone();
    

    // Hashtag
    $hashtag = $this->generateHashtagPhrase();
    session()->put('hashtag', $hashtag);

    // Random opener
    $reviewStart = $this->getRandomOpener($hotelName);

    // System Prompt
    $systemPrompt = "You're a writing assistant that crafts short reviews using a $combinedTone.";

    // User Prompt (updated for new format)
    $mainPrompt = <<<PROMPT
You're a guest at $hotelName (always use this as the hotel name!!) writing a short review of your recent stay. Use the clues below and this accommodation fact sheet. You can reference the accommodation’s features naturally, even if not mentioned directly. You MUST write in the first person (I/we).

Start with: "$reviewStart"

Feel free to include personal expressions, small anecdotes or feelings that match the clues, but stay realistic and consistent.

Structure your review in a natural, slightly varied way — do NOT follow(this is very important!!) the question order exactly. Focus more on what felt most important to you, and don’t be afraid to emphasise one part over the others if it stood out.

Use a natural, flowing tone — not robotic. Avoid listing answers mechanically. Blend the information into a coherent, engaging, and personal review. Try to vary your sentence structures and use natural synonyms where appropriate.

Keep your review between 50–75 words.
PROMPT;

    // Shuffle and assemble clues
    shuffle($qas);
    $clues = "Here are the clues: ";
    foreach ($qas as [$q, $a]) {
        $clues .= "$q $a. ";
    }
    $clues .= "In the end, include this code: $hashtag";

    // Add fact sheet to prompt
    $factSheetJson = json_encode($factSheet, JSON_PRETTY_PRINT);

    $payload = [
        "model" => $openaiApiModel,
        "messages" => [
            [
                "role" => "system",
                "content" => $systemPrompt,
            ],
            [
                "role" => "user",
                "content" => $mainPrompt,
            ],
            [
                "role" => "user",
                "content" => "FACT SHEET:\n" . $factSheetJson,
            ],
            [
                "role" => "user",
                "content" => $clues,
            ]
        ]
    ];
    

//dd($payload);
    // Debug (optional)
    // logger()->info(json_encode($payload));

    // Call OpenAI
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $openaiApiKey,
    ])->post($openaiApiEndpoint, $payload)->json();

    $assistantResponse = $response['choices'][0]['message']['content'] ?? 'Error generating response.';
    $reviewText = $assistantResponse;
    
    dd("PROMPT: " . json_encode($payload, JSON_PRETTY_PRINT) . "\nRESPONSE: " . $reviewText);

    // Save review
    $nfcId = session()->get('nfc_id');
    $userId = session()->get('user_id');
    $userEmail = session()->get('user_email');

    $clientData = Client::where('HOTEL_ID', $hotelId)->first();
    $gmbLink = $clientData->GMB_LINK ?? null;

    $this->saveReview($hotelId, $userId, $nfcId, $userEmail, $reviewText, $hashtag);

    return view('question')->with([
        'apiAnswer' => $assistantResponse,
        'gmbLink' => $gmbLink,
    ]);
}


public function getRandomOpener(string $hotelName): string
{
    $openersWithHotel = [
        "Staying at $hotelName was truly a memorable experience.",
        "From the moment I arrived at $hotelName, I felt right at home.",
        "What stood out most during my time at $hotelName was the attention to detail.",
        "I spent a few days at $hotelName and here’s what I think.",
        "$hotelName had a lot to offer during my visit.",
        "My time at $hotelName was both relaxing and enjoyable.",
        "During my stay at $hotelName, I found myself pleasantly surprised.",
        "There’s a lot to love about $hotelName — here’s what stayed with me.",
        "I wasn’t sure what to expect from $hotelName, but it impressed me.",
        "I stayed at $hotelName recently, and here's how it went."
    ];

    $openersWithoutHotel = [
        "This place exceeded my expectations in so many ways.",
        "From the moment I arrived, I felt completely welcomed.",
        "There was something special about this stay.",
        "Everything about my experience felt thoughtfully curated.",
        "I can’t wait to share a few thoughts about this stay.",
        "I didn’t expect to enjoy it this much — but here we are!",
        "One part of my stay really stood out — let me explain.",
        "It’s rare for a stay to feel this smooth and personal.",
        "I walked away from this experience feeling genuinely refreshed.",
        "Let me tell you about one of the better stays I’ve had recently."
    ];

    $useHotelName = rand(0, 1); // 50% chance

    $openers = $useHotelName ? $openersWithHotel : $openersWithoutHotel;

    return $openers[array_rand($openers)];
}


public function getCombinedTone(): string
{
    $mainTones = [
        "Neutral",
        "Serious",
        "Positive",
        "Friendly",
        "Conversational",
        "Enthusiastic",
        "Evocative",
        "Witty",
        "Playful"
    ];

    $subTones = [
        "warm",
        "cheerful",
        "Welcoming",
        "chatty",
        "relatable",
        "natural",
        "Helpful",
        "bubbly",
        "passionate",
        "inspiring",
        "Informative",
        "nostalgic",
        "sensory",
        "clever",
        "quirky",
        "neutral-positive",
        "Balanced",
        "factual",
        "objective",
        "Formal"
    ];

    // Pick one at random from each list
    $selectedMainTone = $mainTones[array_rand($mainTones)];
    $selectedSubTone = $subTones[array_rand($subTones)];

    return "$selectedMainTone tone with a $selectedSubTone undertone";
}


    public function generateReviewOLD(Request $request){
        $openaiApiKey=env('OPENAI_API_KEY');
        $openaiApiModel=env('OPENAI_API_MODEL_3');
        $openaiApiEndpoint=env('OPENAI_API_ENDPOINT');
        // Retrieve form data
        
        $data = $request->all();
        $filteredData = [];
        
        foreach ($data as $key => $value) {
            // Check if the field starts with "question_label" or "question"
            if (strpos($key, 'question_label') === 0 || strpos($key, 'question') === 0) {
                // Add the field to the filtered data array
                $filteredData[$key] = $value;
            }
        }
        $q1 = 'question_label1'; 
        $a1 = 'question1';

        $q2 = 'question_label2'; 
        $a2 = 'question2';

        $q3 = 'question_label3'; 
        $a3 = 'question3';

        $q4 = 'question_label4'; 
        $a4 = 'question4';

        $q5 = 'question_label5'; 
        $a5 = 'question5';

        $q1 = $filteredData[$q1]; 
        $a1 = $filteredData[$a1]; 

        $q2 = $filteredData[$q2]; 
        $a2 = $filteredData[$a2]; 

        $q3 = $filteredData[$q3]; 
        $a3 = $filteredData[$a3]; 

        $q4 = $filteredData[$q4]; 
        $a4 = $filteredData[$a4]; 

        $q5 = $filteredData[$q5]; 
        $a5 = $filteredData[$a5]; 

        $hotelName = $this->mainLogic->returnHotelName();
        $rankingFeature = $this->mainLogic->returnRankingFeature();

        $hastagType = "notcode";
        if($hastagType == "code"){
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ!?';
    
            // Get the length of the characters string
            $charactersLength = strlen($characters);
            
            // Initialize the hashtag variable with '#'
            $hashtag = '#';
            
            // Generate the remaining 4 characters for the hashtag
        for ($i = 0; $i < 5; $i++) {
            // Choose a random character from the characters string
            $hashtag .= $characters[rand(0, $charactersLength - 1)];
        }
        }else{
            $hashtag = $this-> generateHashtagPhrase();
        }
        
        $openersWithHotel = [
            "Staying at $hotelName was truly a memorable experience.",
            "From the moment I arrived at $hotelName, I felt right at home.",
            "What stood out most during my time at $hotelName was the attention to detail.",
            "I spent a few days at $hotelName and here’s what I think.",
            "$hotelName had a lot to offer during my visit.",
            "My time at $hotelName was both relaxing and enjoyable.",
            "During my stay at $hotelName, I found myself pleasantly surprised.",
            "There’s a lot to love about $hotelName — here’s what stayed with me.",
            "I wasn’t sure what to expect from $hotelName, but it impressed me.",
            "I stayed at $hotelName recently, and here's how it went."
        ];
        
        $openersWithoutHotel = [
            "This place exceeded my expectations in so many ways.",
            "From the moment I arrived, I felt completely welcomed.",
            "There was something special about this stay.",
            "Everything about my experience felt thoughtfully curated.",
            "I can’t wait to share a few thoughts about this stay.",
            "I didn’t expect to enjoy it this much — but here we are!",
            "One part of my stay really stood out — let me explain.",
            "It’s rare for a stay to feel this smooth and personal.",
            "I walked away from this experience feeling genuinely refreshed.",
            "Let me tell you about one of the better stays I’ve had recently."
        ];
        
        // Randomly choose to include the hotel name or not
        $useHotelName = rand(0, 1); // 50/50 chance
        $openers = $useHotelName ? $openersWithHotel : $openersWithoutHotel;
        
        // Select a random opener
        $reviewStart = $openers[array_rand($openers)];
        
        $mainTones = [
            "Neutral",
            "Serious",
            "Positive",
            "Friendly",
            "Conversational",
            "Enthusiastic",
            "Evocative",
            "Witty",
            "Playful"
        ];

        $subTones = [
            "warm",
            "cheerful",
            "Welcoming",
            "chatty",
            "relatable",
            "natural",
            "Helpful",
            "bubbly",
            "passionate",
            "inspiring",
            "Informative",
            "nostalgic",
            "sensory",
            "clever",
            "quirky",
            "neutral-positive",
            "Balanced",
            "factual",
            "objective",
            "Formal"
        ];

        // Pick one at random from each list
        $selectedMainTone = $mainTones[array_rand($mainTones)];
        $selectedSubTone = $subTones[array_rand($subTones)];
        
        // Combine into a single tone description
        $combinedTone = "$selectedMainTone tone with a $selectedSubTone undertone";
        
        // Now use it in your system or user prompt
        $systemPrompt = "You're a writing assistant that crafts short reviews using a $combinedTone.";
        
        $mainPrompt = "You're a guest at ".$hotelName."(always use this as the hotel name!!) writing a short review of your recent stay. Use the clues below — do not invent or assume anything that isn't mentioned (other than generic accommodation experiences). You MUST write in the first person (I/we). You can start with this opener '.$reviewStart.' Feel free to include personal expressions, small anecdotes or feelings that match the clues, but stay realistic and consistent.
                    Structure your review in a natural, slightly varied way — you don’t need to follow the question order exactly. Focus more on what felt most important to you, and don’t be afraid to emphasise one part over the others if it stood out.
                    Use a natural, flowing tone — not robotic. Avoid listing answers mechanically. Blend the information into a coherent, engaging, and personal review. Try to vary your sentence structures and use natural synonyms where appropriate.";
        
        

        session()->put('hashtag', $hashtag);
        
        $answers ="Keep your response within 50 to 75 words!important! Here are the clues: ".$q1." ".$a1.". ".$q2." ".$a2.". ".$q3." ".$a3.". ".$q4." ".$a4.". ".$q5." ".$a5." In the end you need to include this code".$hashtag;
        
        // Original questions and answers
        $qas = [
            [$q1, $a1],
            [$q2, $a2],
            [$q3, $a3],
            [$q4, $a4],
            [$q5, $a5],
        ];
        
        // Shuffle the order randomly
        shuffle($qas);
        
        // Rebuild the answer prompt in mixed order
        $clues = "Keep your response within 50 to 75 words! Here are the clues: ";
        foreach ($qas as [$q, $a]) {
            $clues .= $q . " " . $a . ". ";
        }
        
        // Add the hashtag at the end
        $clues .= "In the end, you need to include this code: " . $hashtag;

        // Construct the request payload
        $payload = [
            "model" => $openaiApiModel, // Assuming this is the model value from the form
            "messages" => [
                [
                    "role" => "system",
                    "content" => $systemPrompt,
                ],
                [
                    "role" => "assistant",
                    "content" => ""
                ],
                [
                    "role" => "user",
                    "content" => $mainPrompt,
                ],
                [
                    "role" => "user",
                    "content" => $clues,
                ]
            ]
        ];
        
        echo "<script>console.log(" . json_encode($payload) . ");</script>";
        // Make the HTTP POST request to OpenAI API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $openaiApiKey,
        ])->post($openaiApiEndpoint, $payload)->json();

        $assistantResponse = $response['choices'][0]['message']['content'];
        $reviewText = $assistantResponse;

        $hotelId = session()->get('hotel_id');
        $nfcId = session()->get('nfc_id');
        $userId = session()->get('user_id');
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $gmbLink = $clientData->GMB_LINK; // Fetch the GMB link

        $userEmail = session()->get('user_email');
        $this->saveReview($hotelId, $userId, $nfcId, $userEmail, $reviewText, $hashtag);

        return view('question')->with([
            'apiAnswer' => $assistantResponse,
            'gmbLink' => $gmbLink, // Pass the GMB link to the view
        ]);

        //return view('question')->with('apiAnswer', $assistantResponse);

    }


    function generateHashtagPhrase(): string
    {
        $basePhrases = [
            "cozy", "superb", "greatplace", "greataccommodation", "cleanstay",
            "perfectspot", "comfybed", "homelyvibes", "peacefularea", "lovelyhost",
            "easycheckin", "stylishspace", "beautifulview", "quietlocation",
            "greatvalue", "sparklingclean", "topservice", "welcomingvibe",
            "idealstay", "feelslikehome"
        ];

        return '#' . implode('', collect($basePhrases)->random(rand(2, 3))->all());
    }


    public function saveReview($hotelId, $userId, $nfcId, $userEmail, $reviewText, $hashtag)
    {
        try {
             // Check if a review already exists for this user and hotel
            $existingReview = Review::where('USER_ID', $userId)
                ->where('HOTEL_ID', $hotelId)
                ->first();

            // If a review exists, delete it
            if ($existingReview) {
            $existingReview->delete();
            }

            // Create a new review record
            $review = new Review();
            $review->HOTEL_ID = $hotelId;
            $review->USER_ID = $userId;
            $review->NFC_ID = $nfcId;
            $review->USER_EMAIL = $userEmail;
            $review->REVIEW = $reviewText;
            $review->stars = 0;
            $review->HASHTAG = $hashtag;

            // Save the review
            $review->save();

            return response()->json(['success' => true, 'message' => 'Review saved successfully!']);
        } catch (\Exception $e) {
            // Return an error response in case of failure
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }





    public function fullGoogleReviewCheck(){
        
        //preden začnemo review check se pošlje request za generiranje qr kode samo zato da se doda uporabnika v bazo (posted = 0)
        $posted = 0;
        #$this->generateFreebieQr($posted);
        $hotelId = session()->get('hotel_id');
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $hotelName = $clientData->HOTEL_NAME;
        $accountName = $hotelName; 

       
        $googleAccounts = $this->gettGoogleAccount();
  
        $googleAccountNumber = null;
        foreach ($googleAccounts as $account) {
            if ($account['accountName'] === $accountName) {
                preg_match('/\d+/', $account['name'], $matches);
                $googleAccountNumber = $matches[0] ?? null;
                break;
            }
        }

        
        
        $googleLocation = $this->getGoogleLocation($googleAccountNumber);


        $location = $googleLocation;
        $userName = session()->get('user_name');
        $hashtag = session()->get('hashtag');
        $days = 30;
        
        $hashtag = ltrim($hashtag, '#');
        
        $test = false;
        if ($test !== true) {
            $maxRetries = 2; // Maximum number of retries
            $attempts = 0;    // Track the number of attempts
            $reviewCheck = null;
        
            $startTime = time();  // Store the start time in seconds
        
            while ($attempts < $maxRetries) {
                
                // Check if the elapsed time exceeds 75 seconds
                if (time() - $startTime > 75) {
                    // If elapsed time exceeds 75 seconds, break the loop
                    break;
                }
        
                $reviewCheck = $this->checkReview($location, $hashtag, $userName, $days);
                

                if($reviewCheck === null){
                    $reviewCheck = false;
                }else{
                    $posted = 1;
                    $response = $reviewCheck;
                    $reviewCheck = $reviewCheck['status_hashtag'];
                    $saveStars = $this->saveStarRating($response, $hashtag);
                }

                

                if ($reviewCheck !== false) {
                    // If the review check is successful, exit the loop
                    break;
                }
        
                // Wait for 20 seconds before the next attempt
                sleep(20);
                $attempts++;
            }
        } 
        else { 
            $posted = 1;
            $reviewCheck = true;
        }





        // Handle the result
        if ($reviewCheck === false) {
            // All retries failed

            $generatedHashtag = session()->get('hashtag'); // Get the hashtag from the session
            $generatedHashtag = ltrim($generatedHashtag, '#'); // Remove the leading '#' if present
        
            $errorMessage = "{$generatedHashtag}";

        
            return view('reviewFailed', compact('errorMessage')); // Return a view with the error message
        }

        // Update the reviews table for the matching hashtag and hotel ID
        Review::where('HASHTAG', "#".$hashtag)
        ->where('HOTEL_ID', $hotelId)
        ->update(['IS_POSTED' => 1]);

        $reviewCheck = $this->generateFreebieQr($posted);
        $current_step = "claim-reward";
       
        
        $hotelId = session()->get('hotel_id');
        $nfcId = session()->get('nfc_id');
        $current_step = $this->sessionService->updateUserProgress($current_step);
        $data = $this->mainLogic->mainIndex($hotelId, $nfcId);

        return $data;
    }
    


    public function gettGoogleAccount(){
        $authKey = env('GOOGLE_REVIEW_AUTH_KEY');
        $apiEndpoint = env('GOOGLE_REVIEW_ACCOUNTS_API_ENDPOINT');

        // Make the HTTP POST request to OpenAI API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $authKey,
        ])->post($apiEndpoint)->json();

        return($response);
    }



    public function getGoogleLocation($googleAccountNumber){
        $authKey = env('GOOGLE_REVIEW_AUTH_KEY');
        $apiEndpoint = env('GOOGLE_REVIEW_SINGLE_ACCOUNT_API_ENDPOINT').$googleAccountNumber;
        $hotelId = session()->get('hotel_id');

        $client = Client::where('HOTEL_ID', $hotelId)->first();
        if (!$client) {
            return null;
        }

        $expectedLocation = trim($client->LOCATION);


        // Make the HTTP POST request to OpenAI API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $authKey,
        ])->post($apiEndpoint)->json();

        foreach ($response as $entry) {
            if (Str::contains($entry, $expectedLocation)) {
                // Split at the colon and trim to get only the path
                $parts = explode(':', $entry);
                return isset($parts[1]) ? trim($parts[1]) : null;
            }
        }

        return null;
    }



    public function checkReview($location, $hashtag, $userName, $days){
        $authKey = env('GOOGLE_REVIEW_AUTH_KEY');
        $apiEndpoint = env('GOOGLE_REVIEW_API_ENDPOINT');
        
        // Construct the request payload
        $payload = [
            "location_id" => $location,
            "days" => $days,
            "reviewerDisplayName" => $userName,
            "hashtag" => $hashtag
        ];
        
        // Make the HTTP POST request to OpenAI API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $authKey,
        ])->post($apiEndpoint, $payload)->json();

        return $response;
    }
    
    
    
    public function saveStarRating($response, $hashtag)
    {
        // Get the star rating from the first matching review
        $reviewData = $response['review_matchedby_hashtag'][0] ?? null;
    
        
        if (!$reviewData || empty($reviewData['starRating'])) {
            return; // No rating found
        }
    
        // Convert rating from text to number
        $ratingText = $reviewData['starRating'];
       
        switch ($ratingText) {
            case 'FIVE':
                $stars = 5;
                break;
            case 'FOUR':
                $stars = 4;
                break;
            case 'THREE':
                $stars = 3;
                break;
            case 'TWO':
                $stars = 2;
                break;
            case 'ONE':
                $stars = 1;
                break;
            default:
                $stars = 0;
        }
       
        
        // Save the stars to the review if it's currently 0
        $dbSave= \DB::table('reviews')
            ->where('HASHTAG', '#'.$hashtag)
            ->where('stars', 0)
            ->update(['stars' => $stars]);
    }



    //Se uporabi ce je bil review potrjen
    public function generateFreebieQr($posted)
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

        if($posted == 0){
            return "note posted yet";
        }

        $freebieQrCodeUrl = $response['freebie_qr_code_url'];
        $freebie_code = $response['freebie_code'];

        $freebie = new Freebie();
        $freebie->freebie_code = $freebie_code;
        $freebie->freebie_qr_code_url = $freebieQrCodeUrl;
        $freebie->HOTEL_ID = $hotelId;
        $freebie->USER_ID = $userId;
        $freebie->save();
    
        $current_step = "claim-reward";
        $this-> sessionService->updateUserProgress($current_step);

        $data = $this->mainLogic->mainIndex($hotelId, $nfcId);
        return $data;
    }


    public function updateFreebieStatus(Request $request)
    {
        // Step 1: Check the API token for security
        $apiToken = $request->query('api_token');
        if ($apiToken !== env('EXTERNAL_SERVICE_API_TOKEN')) {
            return redirect()->route('scanedReview')->with('status', 'error')->with('message', 'Unauthorized access.');
        }

        // Step 2: Validate the freebie code and is_scanned values from the request
        $freebieCode = $request->query('freebie_code');
        $isScanned = $request->query('is_scanned');

        if (!$freebieCode || !is_numeric($isScanned)) {
            return redirect()->route('scanedReview')->with('status', 'error')->with('message', 'Invalid parameters.');
        }

        // Step 3: Find the freebie in your database
        $freebie = Freebie::where('freebie_code', $freebieCode)->first();

        if (!$freebie) {
            return redirect()->route('scanedReview')->with('status', 'error')->with('message', 'Freebie code not found.');
        }

        // Step 4: Update the freebie status if scanned
        $freebie->is_scanned = 1;
        $freebie->save();

        // Step 5: Redirect to the success page with a success message
        return redirect()->route('scanedReview')->with('status', 'success')->with('message', 'The reward has been successfully scanned and verified.');


        }

        public function successPage(Request $request)
        {
            return view('success', [
                'reward' => $request->session()->get('reward'),
                'email' => $request->session()->get('email')
            ]);
    }



    public function confirmReview($id)
    {
        $review = Review::findOrFail($id);
        $review->IS_POSTED = true;
        $review->confirmed_at = now();
        $review->save();

        // Call the function to generate the freebie
        $this->generateFreebie($review);

        return response()->json(['success' => true]);
    }



    public function showQuestionareSecondTime(){

        // Read and parse the JSON file
        $jsonPath = base_path('resources/json/questions.json');
        $jsonFile = File::get($jsonPath);
        $jsonData = json_decode($jsonFile, true);
    
        // Fetch the hotel name from the database using the hotel ID from the session
        $hotelId = session()->get('hotel_id');
        $clientData = Client::where('HOTEL_ID', $hotelId)->first();
        $hotelName = $clientData->HOTEL_NAME;
        // Replace placeholder with the actual hotel name in the questions
    
        foreach ($jsonData['ranking_features'] as &$feature) {
            foreach ($feature['questions'] as &$question) {
                $question['text'] = str_replace('{{Hotel name}}', $hotelName, $question['text']);
            }
        }
        
        // Pass the dynamic questions to the view
        return view('question', ['jsonData' => $jsonData]);
    }
    








}



