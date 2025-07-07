<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class MainController extends Controller
{
    public function index(){
        $client = Client::all();
        return view('home', ['client' => $client]);
    }

}
