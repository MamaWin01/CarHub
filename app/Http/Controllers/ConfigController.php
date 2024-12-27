<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,Wishlist,Chat};

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function configVerify(Request $request)
    {
        if($request->password == '12345') {
            $config = Config::where('name', $request->name)->first();
            if($config) {
                Config::where('id', $config->id)->update(['value' => $request->value]);
            }

            return "have been changed = ".$request->value;
        } else {
            return "wrong data";
        }
    }
}
