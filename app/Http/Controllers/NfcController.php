<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\NfcScan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Services\SessionService;
use App\Logics\MainLogic;




class NfcController extends Controller
{
    protected $sessionService;
    protected $mainLogic;

    public function __construct(SessionService $sessionService, MainLogic $mainLogic)
    {
        $this->sessionService = $sessionService;
        $this->mainLogic = $mainLogic;
    }


    //Reads the NFC TAG FROM THE URL
    public function getNfcData(Request $request)
    {

        $hotelId = $request->input('hotel-id');
        $nfcId = $request->input('nfc-id');
        $this->logScanHistory($hotelId, $nfcId);
        $data = $this->mainLogic->mainIndex($hotelId, $nfcId);
        
        return $data;
    }
    
    
    public function logScanHistory($hotelId, $nfcId)
    {
        // Fetch LOCATION from the nfc_tags table
        $nfcRecord = \DB::table('nfc_tags')
            ->where('hotel_id', $hotelId)
            ->where('nfc_id', $nfcId)
            ->first();
        
        $nfcLocation = $nfcRecord ? $nfcRecord->LOCATION : 'Unknown';
        
        // Insert into nfc_scan_histories table
        \App\Models\NfcScanHistory::create([
            'hotel_id'   => $hotelId,
            'nfc_id'     => $nfcId,
            'nfc_name'   => $nfcLocation,
            'scanned_at' => now(),
        ]);
    }

}
