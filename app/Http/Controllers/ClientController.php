<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\NfcTag;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;




class ClientController extends Controller
{
    public function index(){
        $clients = Client::all();

         return view('clients', [
             'clients', $clients
         ]);
    }


    public function store(Request $request)
    {   
    try {
        $rules = [
            'hotel_name' => 'required|string',
            'logo_img' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner_img' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'colors' => 'required|string',
            'gmb_link'=> 'required|string',
            'ranking_features' => 'required|string',
            'nfc_tags' => 'required|integer|min:1',
        ];

        // Add validation for each NFC tag's action, reward, and location
        for ($i = 1; $i <= $request->input('nfc_tags'); $i++) {
            $rules['action' . $i] = 'required|string';
            $rules['reward' . $i] = 'required|string';
            $rules['location' . $i] = 'required|string'; // Add validation for location
        }

        $request->validate($rules);

        DB::beginTransaction();

        $client = new Client;
        $client->HOTEL_ID = mt_rand(100000, 999999);
        $client->HOTEL_NAME = $request->input('hotel_name');

        $slug = Str::slug($client->HOTEL_NAME, '-');
        $path = 'uploads/' . $slug . '/';
        File::makeDirectory(public_path($path), 0777, true);

        // Save logo image
        if ($request->hasFile('logo_img')) {
            $file = $request->file('logo_img');
            $extension = $file->getClientOriginalExtension();
            $filename = 'logo-' . time() . '.' . $extension;
            $file->move(public_path($path), $filename);
            $client->LOGO_IMG = $filename;
        }

        // Save banner image
        if ($request->hasFile('banner_img')) {
            $file = $request->file('banner_img');
            $extension = $file->getClientOriginalExtension();
            $filename = 'banner-' . time() . '.' . $extension;
            $file->move(public_path($path), $filename);
            $client->BANNER_IMG = $filename;
        }

        $client->HEX_COLOR = $request->input('colors');
        $client->GMB_LINK = $request->input('gmb_link');
        $client->RANKING_FEATURE = $request->input('ranking_features');
        $client->NUMBER_OF_NFC_TAGS = $request->input('nfc_tags');
        $client->save();

        $hotelId = $client->HOTEL_ID;

        // Store NFC Tags with action, reward, and location
        for ($i = 1; $i <= $request->input('nfc_tags'); $i++) {
            $nfcTag = new NfcTag();
            $nfcTag->hotel_ID = $hotelId;
            $nfcTag->NFC_ID = mt_rand(100000, 999999);
            $nfcTag->ACTION = $request->input('action' . $i);
            $nfcTag->REWARD = $request->input('reward' . $i);
            $nfcTag->LOCATION = $request->input('location' . $i); // Store location
            $nfcTag->save();
        }

        // Commit the transaction if everything is successful
        DB::commit();
    } catch (\Exception $e) {
        // Rollback transaction in case of error
        DB::rollback();

        // Log the exception
        Log::error($e->getMessage());

        // Handle the error and delete the uploaded files if needed
        if (isset($path)) {
            File::deleteDirectory(public_path($path));
        }

        return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred. Please try again.']);
    }

    return view('clients', [
        'message' => 'You are added to the system!',
    ]);
    }

}
