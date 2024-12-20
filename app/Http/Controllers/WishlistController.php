<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,Wishlist,Chat};

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if(!Auth()->check()) {
            return redirect()->route('vehicle_list.index');
        }

        $unread_count = Chat::where('user_id', Auth()->user()->id)->select('unread_count')->first()->unread_count;

        if($request->action) {
            if($request->orderby == 'baru') {
                $order = 'desc';
                $by = 'datetime';
            } elseif($request->orderby == 'harga-termahal') {
                $order = 'desc';
                $by = 'price';
            } elseif($request->orderby == 'harga-termurah') {
                $order = 'asc';
                $by = 'price';
            } else {
                $order = 'desc';
                $by = 'total_rating';
            }

            $wishlist = Wishlist::from('wishlist as w')
                            ->leftjoin('vehicle as v', 'v.id', 'w.vehicle_id')
                            ->where('w.user_id', Auth()->user()->id)
                            ->selectRaw('v.*, (select avg(r.rating) from rating as r where r.vehicle_id = v.id) as total_rating')
                            ->orderby($by, $order);

            if($request->search != null) {
                $wishlist = $wishlist->where('name', 'like', $request->search.'%');
            }

            $totalPages = round(count($wishlist->get()) / 9) < 1 ? 1: round(count($wishlist->get()) / 9);

            $wishlist = $wishlist->take(9)->offset(9*($request->page-1))->get();
            return response()->json([
                'wishlist' => view('wishlist.list', compact('wishlist'))->render(),
                'totalPages' => $totalPages,
            ]);
        }

        return view('wishlist.index', compact('unread_count'));
    }

    public function store(Request $request, $id)
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
