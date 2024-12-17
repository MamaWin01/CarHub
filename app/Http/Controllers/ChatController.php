<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,Wishlist};

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        dd($request, Auth()->user());
        return view('wishlist.index');
    }

    public function show(Request $request, $id)
    {
        if($request->wishlist) {
            $wishlist = Wishlist::insert([
                'user_id' => Auth()->user()->id,
                'vehicle_id' => $id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $wishlist = Wishlist::where([
                'user_id' => Auth()->user()->id,
                'vehicle_id' => $id
            ])->delete();
        }

        return response()->json(['success']);
    }
}
