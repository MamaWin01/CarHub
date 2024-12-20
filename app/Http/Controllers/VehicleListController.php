<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel,Chat};

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
            'transmission' => explode(',', $config['transmisi']),
            'fuel_type' => explode(',', $config['jenis_bahan_bakar']),
            'colour' => explode(',', $config['warna']),
            'body_type' => explode(',', $config['tipe_mobil']),
        ];

        if(Auth()->check()) {
            $unread_count = Chat::where('user_id', Auth()->user()->id)->select('unread_count')->first()->unread_count;
        } else {
            $unread_count = 0;
        }
        // dd(Auth()->check());
        return view('vehicle_list.index', compact('unread_count'))->with($filters);
    }

    public function show(Request $request)
    {
        $reqTransmission = $request->transmission ?: [];
        $reqBrand = $request->brand ?: [];
        $reqModel = $request->model ?: [];
        $reqColour = $request->colour ?: [];
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

        $transmission = explode(',',$config['transmisi']);
        $ArrTrans = [];
        foreach($reqTransmission as $trans) {
            if(isset($trans)) {
                $ArrTrans[] = $transmission[$trans];
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
                            ->whereIn('transmission', $ArrTrans)
                            ->orderby($by, $order);

        if($request->price != 'all' && ($request->maxPrice <= $request->minPrice)) {
            if(($request->minPrice == 0 && $request->maxPrice == 0)) {
                if(str_contains($request->price, '-')) {
                    $price = explode('-', $request->price);
                    $minPrice = $price[0] * 1000000;
                    $maxPrice = $price[1] * 1000000;
                    $vehicle = $vehicle->whereBetween('price', [$minPrice, $maxPrice]);
                } else {
                    $vehicle->where('price', '>=', $request->price);
                }
            }
        } else {
            if(($request->minPrice > 0 && $request->maxPrice > 0) && ($request->maxPrice >= $request->minPrice)) {
                $vehicle = $vehicle->whereBetween('price', [$request->minPrice,$request->maxPrice]);
            }
        }

        if($request->year != 'all' && $request->year > 0 && ($request->to <= $request->from)) {
            if(($request->from == 0 || $request->to == 0)) {
                $vehicle = $vehicle->where('year', $request->year);
            }
        } else {
            if(($request->from > 0 && $request->to > 0) && ($request->to >= $request->from)) {
                $vehicle = $vehicle->whereBetween('year', [$request->from,$request->to]);
            }
        }

        if($request->kilometer != 'all' && ($request->maxRange <= $request->minRange)) {
            if(($request->minRange == 0 && $request->maxRange == 0)) {
                if(str_contains($request->kilometer, '-')) {
                    $price = explode('-', $request->kilometer);
                    $minRange = $price[0];
                    $maxRange = $price[1];
                    $vehicle = $vehicle->whereBetween('kilometer', [$minRange, $maxRange]);
                } else {
                    $vehicle->where('kilometer', '>=', $request->kilometer);
                }
            }
        } else {
            if(($request->minRange > 0 && $request->maxRange > 0) && ($request->maxRange >= $request->minRange)) {
                $vehicle = $vehicle->whereBetween('kilometer', [$request->minRange,$request->maxRange]);
            }
        }

        if($request->search != null) {
            $vehicle = $vehicle->where('name', 'like', $request->search.'%');
        }

        if($request->status != 0) {
            $vehicle = $vehicle->where('status', $request->status);
        }

        if($request->brand) {
            if(!in_array('all', $request->brand)) {
                $vehicleBrand = explode(',',$config['merek']);
                $ArrBrand = [];
                foreach($reqBrand as $brand) {
                    if(isset($brand)) {
                        $ArrBrand[] = $vehicleBrand[$brand];
                    }
                }
                $vehicle = $vehicle->whereIn('brand', $ArrBrand);
            }
        } else {
            $vehicle = $vehicle->whereIn('brand', []);
        }

        if($request->model) {
            if(!in_array('all', $request->model)) {
                $vehicleModel = VehicleModel::whereIn('id', $reqModel)->select('model')->get();
                $ArrModel = [];
                foreach($vehicleModel as $model) {
                    $ArrModel[] = $model->model;
                }
                $vehicle = $vehicle->whereIn('model', $ArrModel);
            }
        } else {
            $vehicle = $vehicle->whereIn('model', []);
        }

        if($request->colour) {
            if(!in_array('all', $request->colour)) {
                $vehicleColour = explode(',',$config['warna']);
                $ArrColour = [];
                foreach($reqColour as $colour) {
                    if(isset($colour)) {
                        $ArrColour[] = $vehicleColour[$colour];
                    }
                }
                $vehicle = $vehicle->whereIn('colour', $ArrColour);
            }
        } else {
            $vehicle = $vehicle->whereIn('colour', []);
        }

        if($request->bodyType) {
            if(!in_array('all', $request->bodyType)) {
                $vehicle = $vehicle->whereIn('body_type', $request->bodyType);
            }
        } else {
            $vehicle = $vehicle->whereIn('body_type', []);
        }

        $totalPages = round(count($vehicle->get()) / 18) < 1 ? 1 : round(count($vehicle->get()) / 18);
        $vehicle = $vehicle->take(18)->offset(18*($request->page-1))->get();

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
        if(!$request->brand) {
            $model = [];
        } else {
            $config = Config::where('name', 'merek')->first()->value;
            $brand = explode(',',$config);
            $reqBrand = explode(',',$request->brand);
            $tempArr = [];
            foreach($reqBrand as $br) {
                $tempArr[] = $brand[$br];
            }
            $model = VehicleModel::whereIn('brand', $tempArr)->get();
        }

        return view('vehicle_list.brandModel', compact('model'));
    }
}
