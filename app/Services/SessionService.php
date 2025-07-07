<?php

namespace App\Services;
use App\Models\User;

class SessionService
{
    //checks if the session already exists, if not, creates it
    public function checkSession($hotelId, $nfcId)
    {

        if (!session()->has('hotel_id')) {
            session()->put('hotel_id', $hotelId);
            session()->put('nfc_id', $nfcId);
            session()->put('progress', 0);
            session()->put('last_activity', now());
        }

        $lastActivity = session('last_activity');
        if(now()->diffInSeconds($lastActivity) > 7200){
            session()->flush();
            session()->put('hotel_id', $hotelId);
            session()->put('nfc_id', $nfcId);
            session()->put('progress', 0);
            session()->put('last_activity', now());
        }
        return session()->get('progress');
    }



    public function updateUserProgress($newProgress):void{
        session()->put('progress', $newProgress);

        // Update progress in the database for the current user identified by EMAIL and HOTEL_ID
        $email = session()->get('user_email');
        $hotelId = session()->get('hotel_id');

        $user = User::where('EMAIL', $email)
                    ->where('HOTEL_ID', $hotelId)
                    ->first();

        if ($user) {
            $user->PROGRESS = $newProgress;
            $user->save();
        }
    }



    


    //return the correct view based on the user progress
    public function returnProgress($progress){

        // Direct user to the appropriate view based on the current step
        switch ($progress) {
            case 0:
                return view('home', [
                    'logoImgPath' => $logoImgPath,
                    'bannerImgPath' => $bannerImgPath,
                ]);

            case 1:
                return view('questions', [
                    'logoImgPath' => $logoImgPath,
                    'bannerImgPath' => $bannerImgPath,
                ]);
            // Add more cases as needed
            default:
                return view('default-view');
        }
    }
}