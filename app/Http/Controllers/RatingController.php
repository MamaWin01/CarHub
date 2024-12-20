<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel,Rating};

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, $id)
    {
        $rating = Rating::where([
            'user_id' => Auth()->user()->id,
            'user_name' => Auth()->user()->name,
            'vehicle_id' => $id,
        ])->first();

        if($rating) {
            $rating = Rating::where([
                'user_id' => Auth()->user()->id,
                'user_name' => Auth()->user()->name,
                'vehicle_id' => $id,
            ])->update([
                'rating' => $request->rate,
                'content' => $request->review
            ]);
        } else {
            $rating = Rating::insert([
                'user_id' => Auth()->user()->id,
                'user_name' => Auth()->user()->name,
                'vehicle_id' => $id,
                'rating' => $request->rate,
                'content' => $request->review,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back();
    }

    public function Destroy($id)
    {
        $user = Auth()->user();

        Rating::where('user_id', $user->id)->where('vehicle_id', $id)->delete();

        return back()->with('success', 'Rating dan ulasan berhasil dihapus.');
    }

}
