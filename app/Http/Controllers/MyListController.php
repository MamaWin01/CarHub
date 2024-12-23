<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\{Vehicle,Config,VehicleModel,Chat};

class MyListController extends Controller
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

        $config = Config::whereIn('name', ['merek', 'warna', 'transmisi', 'jenis_bahan_bakar', 'tipe_mobil'])->get();
        $tempArr = [];
        foreach($config as $i) {
            $tempArr[$i->name] = $i->value;
        }
        $config = $tempArr;
        $models = VehicleModel::get();
        $newModel = [];
        foreach($models as $model) {
            $newModel[$model->brand][] = $model->model;
        }
        $filters = [
            'brands' => explode(',', $config['merek']),
            'transmission' => explode(',', $config['transmisi']),
            'fuel_type' => explode(',', $config['jenis_bahan_bakar']),
            'colour' => explode(',', $config['warna']),
            'body_type' => explode(',', $config['tipe_mobil']),
            'model' => $newModel
        ];

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

            $myList = Vehicle::from('vehicle as v')
                            ->where('owner_id', Auth()->user()->id)
                            ->selectRaw('v.*, (select avg(r.rating) from rating as r where r.vehicle_id = v.id) as total_rating')
                            ->orderby($by, $order);

            if($request->search != null) {
                $myList = $myList->where('name', 'like', $request->search.'%');
            }

            $totalPages = round(count($myList->get()) / 9) < 1 ? 1: round(count($myList->get()) / 9);

            $myList = $myList->take(9)->offset(9*($request->page-1))->get();

            foreach ($myList as $key => $list) {
                $folderPath = 'public/images/vehicles/' . Auth()->user()->id . '_' . $list->id;

                if (Storage::exists($folderPath)) {
                    $files = Storage::files($folderPath); // Get all files in the folder
                    $list->img_count = count($files);
                } else {
                    $list->img_count = 0; // Folder doesn't exist
                }
            }

            return response()->json([
                'myList' => view('mylist.list', compact('myList','filters'))->render(),
                'totalPages' => $totalPages,
            ]);
        }

        return view('mylist.index', compact('filters', 'unread_count'));
    }

    public function store(Request $request)
    {
        try {
            // Validate input
            $this->checkRequest($request);

            // Insert data into the database
            $VehicleId = Vehicle::insertGetId([
                'datetime' => date('Y-m-d H:i:s'),
                'owner_id' => Auth()->user()->id,
                'name' => $request->name,
                'condition' => $request->condition,
                'status' => $request->status,
                'price' => str_replace('.', '',$request->price),
                'year' => $request->year,
                'brand' => $request->brand,
                'model' => $request->model,
                'transmission' => $request->transmission,
                'fuel_type' => $request->fuel,
                'colour' => $request->colour,
                'kilometer' => $request->kilometer,
                'body_type' => $request->body_type
            ]);

            // Handle photo upload
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $key => $photo) {
                    $photo->storeAs(
                        'images/vehicles/'.Auth()->user()->id.'_'.$VehicleId,
                        Auth()->user()->id.'_'.$VehicleId.'_'.($key+1).'.png',
                        'public'
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'Kendaraan berhasil ditambahkan']);
        } catch (\Exception $e) {
            // dd($e, $e->getMessage());
            return back()->with('store-vehicle-error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->checkRequest($request);

        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->name = $request->name;
            $vehicle->condition = $request->condition;
            $vehicle->price = str_replace('.', '', $request->price);
            $vehicle->brand = $request->brand;
            $vehicle->model = $request->model;
            $vehicle->status = $request->status;
            $vehicle->year = $request->year;
            $vehicle->kilometer = $request->kilometer;
            $vehicle->transmission = $request->transmission;
            $vehicle->fuel_type = $request->fuel;
            $vehicle->colour = $request->colour;
            $vehicle->body_type = $request->body_type;

            $folderPath = 'public/images/vehicles/' . Auth()->user()->id . '_' . $id;
            $allFiles = Storage::files($folderPath);

            // Handle Existing Images
            $existingImages = $request->existing_images ?? [];
            $remainingFiles = [];

            foreach($allFiles as $file) {
                $relativePath = str_replace('public/', '/storage/', $file);
                if(!in_array($relativePath, $existingImages)) {
                    Storage::delete($file);
                } else {
                    $remainingFiles[] = $file;
                }
            }

            // Rename remaining images sequentially
            $newIndex = 1;
            $renamedFiles = [];
            foreach ($remainingFiles as $file) {
                $newFileName = Auth()->user()->id . '_' . $id . '_' . $newIndex . '.png';
                $newFilePath = $folderPath . '/' . $newFileName;
                Storage::move($file, $newFilePath);
                $renamedFiles[] = $newFilePath;
                $newIndex++;
            }

            // Handle New Images
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $newFileName = Auth()->user()->id . '_' . $id . '_' . $newIndex . '.png';
                    $newImgPath = 'images/vehicles/' . Auth()->user()->id . '_' . $id;
                    $photo->storeAs(
                        $newImgPath,
                        $newFileName,
                        'public'
                    );
                    $newIndex++;
                }
            }

            $vehicle->save();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            $directoryPath = 'images/vehicles/' . Auth()->user()->id . '_' . $id;

            if (Storage::disk('public')->exists($directoryPath)) {
                $files = Storage::disk('public')->allFiles($directoryPath);
                Storage::disk('public')->delete($files);

                Storage::disk('public')->deleteDirectory($directoryPath);
            }

            $vehicle->delete();

            return response()->json(['success' => true, 'message' => 'Kendaraan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus kendaraan.']);
        }
    }

    private function checkRequest($request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'condition' => 'required|string',
            'status' => 'required|string',
            'price' => 'required|string',
            'year' => 'required|numeric',
            'brand' => 'required|string',
            'model' => 'required|string',
            'transmission' => 'required|string',
            'fuel' => 'required|string',
            'colour' => 'required|string',
            'kilometer' => 'required|string',
            'body_type' => 'required|string',
        ], [
            'name.required' => 'Nama mobil wajib diisi.',
            'name.string' => 'Nama mobil harus berupa teks.',
            'name.max' => 'Nama mobil tidak boleh lebih dari 255 karakter.',

            'condition.required' => 'Kondisi kendaraan wajib dipilih.',
            'condition.string' => 'Kondisi kendaraan harus berupa teks.',

            'status.required' => 'Status kendaraan wajib dipilih.',
            'status.string' => 'Status kendaraan harus berupa teks.',

            'price.required' => 'Harga wajib diisi.',

            'year.required' => 'Tahun wajib diisi.',
            'year.numeric' => 'Tahun harus berupa angka.',

            'brand.required' => 'Merek wajib dipilih.',
            'brand.string' => 'Merek harus berupa teks.',

            'model.required' => 'Model wajib dipilih.',
            'model.string' => 'Model harus berupa teks.',

            'transmission.required' => 'Transmisi wajib dipilih.',
            'transmission.string' => 'Transmisi harus berupa teks.',

            'fuel.required' => 'Bahan bakar wajib dipilih.',
            'fuel.string' => 'Bahan bakar harus berupa teks.',

            'colour.required' => 'Warna wajib dipilih.',
            'colour.string' => 'Warna harus berupa angka.',

            'kilometer.required' => 'Kilometer wajib diisi.',

            'body_type.required' => 'Tipe mobil wajib dipilih.',
        ]);

        return $request;
    }
}
