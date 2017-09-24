<?php

namespace App\Http\Controllers;

use App\Station;
use App\Zone;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Get the status of a station and send notification
     *
     * @return Response
     */
    public function station($station, $intent = 'rent', Request $request)
    {
        $silent = $request->input('silent');
        $call = $request->input('call');

        try {
            $station = new Station($station);
            $station->notify($intent, $silent, $call);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 503);
        }

        return response()->json([
            'station' => $station->status(),
            'message' => $station->notificationMessage($intent)
        ], 200);
    }

    /**
     * Get the status of a station and send notification
     *
     * @return Response
     */
    public function zone($stations, $intent = 'rent', Request $request)
    {
        $silent = $request->input('silent');
        $call = $request->input('call');

        try {
            $zone = new Zone($stations);
            $zone->notify($intent, $silent, $call);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 503);
        }

        return response()->json([
            'status' => 'OK',
            'zone' => $zone->statuses(),
            'message' => $zone->notificationMessage($intent)
        ], 200);
    }
}
