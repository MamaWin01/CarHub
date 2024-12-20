<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel,Rating, Wishlist, Chat};

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

        if(Auth()->check()) {
            $unread_count = Chat::where('user_id', Auth()->user()->id)->select('unread_count')->first()->unread_count;
        } else {
            $unread_count = 0;
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

        return view('vehicle_detail.index', compact('vehicle','userRating', 'userReview', 'reviews', 'is_in_wishlist', 'filters', 'unread_count'));
    }

    private function showModel($request, $id)
    {
        $vehicle = Vehicle::from('vehicle as v')
                        ->join('user as u', 'u.id', 'v.owner_id')
                        ->where('v.id', $id)
                        ->selectRaw('v.*,u.name as owner_name, (select avg(r.rating) from rating as r where r.vehicle_id = v.id) as total_rating')
                        ->first();

        $vehiclePath = 'storage/images/vehicles/' . $vehicle->owner_id . '_' . @$vehicle->id . '.png';
        if(file_exists(public_path($vehiclePath))) {
            $image = asset($vehiclePath);
        } else {
            $image = asset('images/not_found.jpg');
        }

        $response = [
            'name' => $vehicle->name,
            'owner_name' => $vehicle->owner_name,
            'rating' => $vehicle->total_rating,
            'image' => $image
        ];
        return response()->json($response);
    }
}
