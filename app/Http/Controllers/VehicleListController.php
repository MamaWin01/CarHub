<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel};

class VehicleListController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $config = Config::whereIn('name', ['merek', 'warna', 'transmisi', 'jenis_bahan_bakar', 'tipe_mobil'])->get();
        $tempArr = [];
        foreach($config as $i) {
            $tempArr[$i->name] = $i->value;
        }
        $config = $tempArr;
        $filters = [
            'brands' => explode(',', $config['merek']),
            'model' => [],
            'transmision' => explode(',', $config['transmisi']),
            'fuel_type' => explode(',', $config['jenis_bahan_bakar']),
            'colour' => explode(',', $config['warna']),
            'body_type' => explode(',', $config['tipe_mobil']),
        ];
        // dd(Auth()->check());
        return view('vehicle_list.index')->with($filters);
    }

    public function show(Request $request)
    {
        $config = Config::whereIn('name', ['kondisi','merek', 'warna', 'transmisi', 'jenis_bahan_bakar', 'tipe_mobil'])->get();
        $tempArr = [];
        foreach($config as $i) {
            $tempArr[$i->name] = $i->value;
        }
        $config = $tempArr;
        $condition = array_flip(explode(',',$config['kondisi']));
        $ArrCond = [];
        foreach(explode(',', $request->condition) as $cond) {
            if($cond) {
                $ArrCond[] = $condition[$cond];
            }
        }

        $transmision = explode(',',$config['transmisi']);
        $ArrTrans = [];
        foreach($request->transmision as $trans) {
            if(isset($trans)) {
                $ArrTrans[] = $transmision[$trans];
            }
        }

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

        if($request->fuel) {
            $fuel = $request->fuel;
        } else {
            $fuel = [];
        }

        $vehicle = Vehicle::selectRaw('vehicle.*, (select avg(r.rating) from rating as r where r.vehicle_id = vehicle.id) as total_rating')
                            ->whereIn('condition', $ArrCond)
                            ->whereIn('fuel_type', $fuel)
                            ->whereIn('transmision', $ArrTrans)
                            ->orderby($by, $order);

        if($request->price != 'all' && $request->price > 0) {
            if(($request->minPrice < 0 && $request->maxPrice < 0) || ($request->maxPrice < $request->minPrice)) {
                $vehicle = $vehicle->where('price', $request->price);
            }
        } else {
            if(($request->minPrice > 0 && $request->maxPrice > 0) && ($request->maxPrice >= $request->minPrice)) {
                $vehicle = $vehicle->whereBetween('price', [$request->minPrice,$request->maxPrice]);
            }
        }

        if($request->year != 'all' && $request->year > 0) {
            if(($request->from < 0 && $request->to < 0) || ($request->to < $request->from)) {
                $vehicle = $vehicle->where('year', $request->year);
            }
        } else {
            if(($request->from > 0 && $request->to > 0) && ($request->to >= $request->from)) {
                $vehicle = $vehicle->whereBetween('year', [$request->from,$request->to]);
            }
        }

        if($request->search != null) {
            $vehicle = $vehicle->where('name', 'like', $request->search.'%');
        }

        if($request->status != 0) {
            $vehicle = $vehicle->where('status', $request->status);
        }

        if(!in_array('all', $request->brand)) {
            $vehicle = $vehicle->whereIn('brand', $request->brand);
        }

        if(!in_array('all', $request->model)) {
            $vehicle = $vehicle->whereIn('model', $request->model);
        }

        if(!in_array('all', $request->colour)) {
            $vehicle = $vehicle->whereIn('colour', $request->colour);
        }

        if(!in_array('all', $request->bodyType)) {
            $vehicle = $vehicle->whereIn('body_type', $request->bodyType);
        }

        $totalPages = round(count($vehicle->get()) / 3) < 1 ? 1 : round(count($vehicle->get()) / 3);
        $vehicle = $vehicle->take(3)->offset(3*($request->page-1))->get();

        if(count($vehicle) < 0) {
            $vehicle = [];
        }

        return response()->json([
            'cars' => view('vehicle_list.list', compact('vehicle'))->render(), // Rendered HTML
            'totalPages' => $totalPages,
        ]);
    }

    public function getBrandModel(Request $request)
    {
        $config = Config::where('name', 'merek')->first()->value;
        $brand = explode(',',$config);
        $reqBrand = explode(',',$request->brand);
        $tempArr = [];
        foreach($reqBrand as $br) {
            $tempArr[] = $brand[$br];
        }
        $model = VehicleModel::whereIn('brand', $tempArr)->get();

        return view('vehicle_list.brandModel', compact('model'));
    }
}
