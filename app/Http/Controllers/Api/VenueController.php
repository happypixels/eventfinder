<?php

namespace App\Http\Controllers\Api;

use App\Models\Venue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VenueController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Venue  $venue
     * @return \Illuminate\Http\Response
     */
    public function show(Venue $venue)
    {
        return response()->json(['venue' => $venue], 200);
    }
}
