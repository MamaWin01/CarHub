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

        return view('vehicle_list.index')->with($filters);
    }

    public function show(Request $request)
    {
        $vehicle = Vehicle::selectRaw('vehicle.*, (select avg(r.rating) from rating as r where r.vehicle_id = vehicle.id) as total_rating')
                            ->take(3)
                            ->offset(3*($request->page-1))
                            ->get();

        $AllVehicle = Vehicle::get();
        $totalPages = round(count($AllVehicle) / 3);

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
