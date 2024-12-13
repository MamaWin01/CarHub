<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{VehicleModel,Config,UserId,Vehicle};

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

    public function generateRandData()
    {
        // for($i=0;$i < 5;$i++) {
        //     DB::table('user')->insert([
        //         'name' => $this->randomName(),
        //         'email' => $this->randomName().$i.'@',
        //         'password' => $i
        //     ]);
        // }

        $users = UserId::all();
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
        foreach($users as $u) {
            $brand = $filters['brands'][rand(0, 30)];
            $model = VehicleModel::where('brand', $brand)->get();
            $tempArr = [];
            foreach($model as $key => $i) {
                $tempArr[$key] = $i->model;
            }
            $model = $tempArr;
            $id = DB::table('vehicle')->insertGetId([
                'datetime' => date('Y-m-d H:i:s'),
                'name' => $u->name,
                'owner_id' => $u->id,
                'status' => rand(0,2),
                'condition' => rand(1,4),
                'price' => rand(1000000,5000000),
                'brand' => $brand,
                'model' => $model[rand(0, count($tempArr) -1)],
                'transmision' => $filters['transmision'][rand(0, 3)],
                'fuel_type' => rand(0,3),
                'colour' => $filters['colour'][rand(0, 13)],
                'year' => rand(2019,2025),
                'kilometer' => rand(1000,2000),
                'body_type' => rand(1,15)
            ]);

            DB::table('rating')->insert([
                'user_id' => $u->id,
                'user_name' => $u->name,
                'vehicle_id' => $id,
                'rating' => rand(0,5),
                'content' => '',
            ]);
        }
    }

    function randomName() {
        $names = array(
            'Johnathon',
            'Anthony',
            'Erasmo',
            'Raleigh',
            'Nancie',
            'Tama',
            'Camellia',
            'Augustine',
            'Christeen',
            'Luz',
            'Diego',
            'Lyndia',
            'Thomas',
            'Georgianna',
            'Leigha',
            'Alejandro',
            'Marquis',
            'Joan',
            'Stephania',
            'Elroy',
            'Zonia',
            'Buffy',
            'Sharie',
            'Blythe',
            'Gaylene',
            'Elida',
            'Randy',
            'Margarete',
            'Margarett',
            'Dion',
            'Tomi',
            'Arden',
            'Clora',
            'Laine',
            'Becki',
            'Margherita',
            'Bong',
            'Jeanice',
            'Qiana',
            'Lawanda',
            'Rebecka',
            'Maribel',
            'Tami',
            'Yuri',
            'Michele',
            'Rubi',
            'Larisa',
            'Lloyd',
            'Tyisha',
            'Samatha',
        );
        return $names[rand ( 0 , count($names) -1)];
    }
}
