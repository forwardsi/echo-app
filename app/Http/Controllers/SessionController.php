<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function checkSession(Request $request)
    {
        $hotelId = $request->input('hotel-id');
        $nfcId = $request->input('nfc-id');
        $progress = $request->input('progress');

        // Check if a session already exists
        if (!$request->session()->has('hotel_id')) {
            // If not, create a new session and initialize data
            $request->session()->put('hotel_id', $request->input('hotel-id'));
            $request->session()->put('nfc_id', $request->input('nfc-id'));
            $request->session()->put('progress', 0);
        }

        $currentStep = $request->session()->get('progress');


        

        // Direct user to the appropriate view based on the current step
        switch ($currentStep) {
            case 0:
                return redirect('/viewOne');
            case 1:
                return view('step2');
            // Add more cases as needed
            default:
                return view('default-view');
        }
    }

    
    // Update session progress based on completed steps
    public function updateUserProgress(){
        // Check current step
        $currentStep = $request->session()->get('progress');

        // Direct user to the appropriate view based on the current step
        switch ($currentStep) {
            case 0:
                return view('step1');
            case 1:
                return view('step2');
            // Add more cases as needed
            default:
                return view('default-view');
        }
    }
}
