<?php

namespace App\Http\Controllers;

use App\Models\Location;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    //
    function getLocations(Request $request)
    {

        $locations = Location::all();
        $response = $locations;

        return response($response,201);
    }
}