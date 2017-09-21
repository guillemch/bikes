<?php

namespace App\Http\Controllers;

use App\Station;
use App\Zone;

class ApiController extends Controller
{
    /**
     * Get the status of a station and send notification
     *
     * @return Response
     */
    public function station($station, $intent = 'rent')
    {
        try {
            $station = new Station($station);
            $station->notify($intent);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 503);
        }

        return response()->json([
            'status' => 'OK',
            'station' => $station->status(),
            'message' => $station->notificationMessage($intent)
        ], 200);
    }

    /**
     * Get the status of a station and send notification
     *
     * @return Response
     */
    public function zone($stations, $intent = 'rent')
    {
        try {
            $zone = new Zone($stations);
            $zone->notify($intent);
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
