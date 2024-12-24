<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BoxStock;
use App\Models\Chalan;
use App\Models\DlStock;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class DlStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $drivingLicense = DlStock::orderBy('id', 'DESC')->paginate(10);

            if($drivingLicense->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No Driving License found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Driving License data found",
                "data" => $drivingLicense
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function sampleDownload()
    {
        try {
            $filePath = 'driving_license_file/dl_sample_file.xlsx';
            $fileName = 'dl_sample_file.xlsx';

            if (!file_exists(public_path($filePath))) {
                return response()->json(['message' => 'File not found.'], 404);
            }

            $fileContents = file_get_contents($filePath);

            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\""
            ];

            return response($fileContents, 200, $headers);

        } catch (\Exception $e) {

            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Failed to download. Please try again. Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv'
            ]);

            $collection = Excel::toCollection(null, $request->file('file'))->first();

            $errors = [];

            foreach ($collection->slice(13) as $index => $row) {

                if (collect($row)->filter(function ($value) {
                    return $value !== null;
                })->isEmpty()) {
                    continue;
                }

                $cleanedRow = collect($row)->filter(function ($value) {
                    return $value !== null;
                })->values()->all();

                $validator = Validator::make([
                    'brta_ref_no' => $cleanedRow[1] ?? null,
                    'license_no' => $cleanedRow[2] ?? null,
                    'applicant_name' => $cleanedRow[3] ?? null,
                    'box_no' => $cleanedRow[4] ?? null,
                ], [
                    'brta_ref_no' => 'required|string|unique:dl_stocks,brta_ref_no|max:255',
                    'license_no' => 'required|string|unique:dl_stocks,license_no|max:255',
                    'applicant_name' => 'required|string|max:255',
                    'box_no' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    $errors[$index] = $validator->errors()->toArray();
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    "status" => "error",
                    "status_code" => 422,
                    "message" => "Validation failed in rows",
                    "errors" => $errors
                ], 422);
            }

            // If validation passes, begin transaction
            DB::beginTransaction();

            try {
                $rdnChalanNo = 'CH' . rand(10000000, 99999999);
                $boxCount = [];

                foreach ($collection->slice(13) as $index => $row) {

                    if (collect($row)->filter(function ($value) {
                        return $value !== null;
                    })->isEmpty()) {
                        continue;
                    }

                    $cleanedRowDL = collect($row)->filter(function ($value) {
                        return $value !== null;
                    })->values()->all();

                    $storeData = [
                        'user_id' => Auth::user()->id,
                        'chalan_number' => $rdnChalanNo,
                        'brta_ref_no' => $cleanedRowDL[1],
                        'license_no' => $cleanedRowDL[2],
                        'applicant_name' => $cleanedRowDL[3],
                        'box_no' => $cleanedRowDL[4],
                        'created_by' => Auth::user()->id,
                        'created_by_ip' => request()->ip(),
                    ];

                    DlStock::create($storeData);
                    $boxCount[$storeData['box_no']] = ($boxCount[$storeData['box_no']] ?? 0) + 1;
                    $chalanCount[$rdnChalanNo] = ($chalanCount[$rdnChalanNo] ?? 0) + 1;
                }

                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $extension = $file->getClientOriginalExtension();
                    $filename = $rdnChalanNo . '-' . time() . '.' . $extension;
                    $destinationPath = public_path('driving_license_file/');
                    $file->move($destinationPath, $filename);
                    $file = $filename;
                }

                foreach ($chalanCount as $chalanNo => $totalDL) {
                    $storeData = [
                        'chalan_number' => $chalanNo,
                        'sender_id' => Auth::user()->id,
                        'total_dl' => $totalDL,
                        'execl_file' => $file,
                        'created_by' => Auth::user()->id,
                        'created_by_ip' => request()->ip(),
                    ];

                    Chalan::create($storeData);
                }

                foreach ($boxCount as $boxNo => $totalDL) {
                    BoxStock::create([
                        'chalan_number' => $rdnChalanNo,
                        'box_no' => $boxNo,
                        'total_dl' => $totalDL,
                        'created_by' => Auth::user()->id,
                        'created_by_ip' => request()->ip(),
                    ]);
                }

                Log::create([
                    'user_id' => Auth::id(),
                    'action' => "Driving license Uploaded",
                    'type' => 2,
                    'file' => $file,
                    'module_type' => 2,
                    'created_by' => Auth::id(),
                    'created_by_ip' => request()->ip(),
                ]);

                DB::commit();

                return response()->json([
                    "status" => "success",
                    "status_code" => 201,
                    "message" => "DL File imported successfully."
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    "status" => "error",
                    "status_code" => 500,
                    "message" => "Error: " . $e->getMessage()
                ])->setStatusCode(500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ])->setStatusCode(422);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
