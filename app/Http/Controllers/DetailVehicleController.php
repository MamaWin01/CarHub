<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel};

class DetailVehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, $id)
    {
        if($request->action) {
            return $this->showModel($request, $id);
        }
        // dd($request->all(), $id);
        return view('vehicle_detail.index');
    }

    private function showModel($request, $id)
    {
        $vehicle = Vehicle::where('id', $id)->selectRaw('vehicle.*, (select avg(r.rating) from rating as r where r.vehicle_id = vehicle.id) as total_rating')->first();
        $response = [
            'name' => $vehicle->name,
            'owner_name' => $vehicle->owner_id,
            'rating' => $vehicle->total_rating
        ];
        return response()->json($response);
    }
}
