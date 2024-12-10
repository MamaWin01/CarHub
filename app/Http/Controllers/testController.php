<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config};

class testController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function test(Request $request)
    {
        $test = DB::table('konfigurasi')->where('nama', 'merek')->first()->value;
        $model = DB::table('model_kendaraan')
                ->select('merek', DB::raw('GROUP_CONCAT(model ORDER BY model ASC) as models'))
                ->groupBy('merek')
                ->get();

        $test = explode(',', $test);
        $newTest = [];
        foreach($test as $temp) {
            if(str_contains($temp, ':')) {
                $tempVal = explode(':', $temp);
                $newTest[$tempVal[0]] = $tempVal[1];
            } else {
                $newTest = $temp;
            }
        }
        dd($test, $model);
    }

    public function generateRandVehicle()
    {
        for($i=0;$i < 5;$i++) {
            DB::table('vehicle')->insert([
                'datetime' => date('Y-m-d H:i:s'),
                'name' => 'a'.$i,
                'owner_id' => rand(1,10),
                'status' => rand(1,4),
                'price' => rand(1000000,5000000),
                'brand' => 'a'.$i,
                'model' => 'a'.$i,
                'transmision' => 'a'.$i,
                'fuel_type' => rand(1,2),
                'colour' => 'a'.$i,
                'year' => rand(2019,2025),
                'kilometer' => rand(1000,2000),
                'body_type' => rand(1,6)
            ]);
        }
    }
}
