<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config};

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
            'brand' => explode(',', $config['merek']),
            'model' => ['Avanza', 'Rush', 'Kijang Innova', 'Fortuner'],
            'transmision' => explode(',', $config['transmisi']),
            'fuel_type' => explode(',', $config['jenis_bahan_bakar']),
            'warna' => explode(',', $config['warna']),
            'body_type' => explode(',', $config['tipe_mobil']),
        ];

        return view('vehicle_list.index', compact('filters'));
    }

    public function show(Request $request)
    {
        $vehicle = Vehicle::get();
        // dd($vehicle);
        return view('vehicle_list.list', compact('vehicle'));
    }
}
