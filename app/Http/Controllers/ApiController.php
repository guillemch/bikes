<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class ApiController extends Controller
{
    public function station($station, $action = 'rent')
    {
        try {
            $status = Artisan::call('station:check', ['station' => $station, '--to' => $action, '--notify' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()], 503);
        }

        return response()->json(['status' => 'OK'], 200);
    }
}
