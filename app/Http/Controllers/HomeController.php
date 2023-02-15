<?php

namespace App\Http\Controllers;

use App\Models\LtiPlatform;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $platform = LtiPlatform::first();
        return view('lti-registration', ['platform' => $platform]);
    }
}
