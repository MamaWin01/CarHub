<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel,Rating, Wishlist};

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

        $config = Config::whereIn('name', ['kondisi', 'merek', 'warna', 'transmisi', 'jenis_bahan_bakar', 'tipe_mobil'])->get();
        $tempArr = [];
        foreach($config as $i) {
            $tempArr[$i->name] = $i->value;
        }
        $config = $tempArr;
        $filters = [
            'condition' => explode(',', $config['kondisi']),
            'fuel_type' => explode(',', $config['jenis_bahan_bakar']),
            'body_type' => explode(',', $config['tipe_mobil']),
        ];

        $vehicle = Vehicle::where('id', $id)->first();

        if(Auth()->check()) {
            $user = Rating::where('user_id', Auth()->user()->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->first();

            $is_in_wishlist = Wishlist::where([
                    'user_id' => Auth()->user()->id,
                    'vehicle_id' => $id
                ])->first();
        } else {
            $user = [];
            $is_in_wishlist = [];
        }

        $userRating = !@$user->rating ? 0 : $user->rating;
        $userReview = !@$user->content ? '' : $user->content;

        $reviews = Rating::where('vehicle_id', $vehicle->id)->take(3)->orderBy('created_at', 'desc')->get();

        return view('vehicle_detail.index', compact('vehicle','userRating', 'userReview', 'reviews', 'is_in_wishlist', 'filters'));
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
