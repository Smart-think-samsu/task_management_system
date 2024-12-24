<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BoxOperation;
use App\Models\BoxStock;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoxOperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $boxOperations = BoxOperation::with('user')->with('boxStock')->orderBy('id', 'desc')->paginate(10);

            if($boxOperations->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No box found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Box operation data found",
                "data" => $boxOperations
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ], 500);
        }


    }

    /**
     * user wise box list show
     */

    public function userWiseBox()
    {
        try {
            $userWiseBox = BoxOperation::with('boxStock')->where('user_id', Auth::user()->id)->where('status')->orderBy('id', 'desc')->paginate(10);

            if($userWiseBox->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No box found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 404,
                "message" => "Box operation data found",
                "data" => $userWiseBox
            ])->setStatusCode(400);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getAllOperator()
    {
        try {
            $operators = User::select('id','name')->where('type', 3)->orderBy('id', 'desc')->get();

            if($operators->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No Operator found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Operator data found",
                "operator" => $operators
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ], 500);
        }
    }

    public function getBox()
    {
        try {

            $boxes = BoxStock::select('id', 'box_no', 'total_dl', 'status')->whereIn('status', [0,2])->orderBy('id', 'desc')->get();

            if($boxes->isEmpty()){
                return response()->json([
                    "status" => "error",
                    "status_code" => 404,
                    "message" => "No Boxes found"
                ])->setStatusCode(404);
            }

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Box data found",
                "box" => $boxes
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'box_stock_id' => "bail|required|array",
                'box_stock_id.*' => "required",
                'assign_by' => "bail|required",
                'user_id' => "bail|required",
            ]);

            $boxNo = BoxStock::whereIn('id', $request->box_stock_id)->where('status', 0)->pluck('box_no')->toArray();

            $logBoxNo = implode(",", $boxNo);

            $operator = User::where('id', $request->user_id)->value('name');

            foreach ($request->box_stock_id as $stockId) {
                BoxOperation::create([
                    'box_stock_id' => $stockId,
                    'assign_by' => $request->assign_by,
                    'user_id' => $request->user_id,
                    'created_at' => now(),
                    'created_by' => Auth::id(),
                    'created_by_ip' => request()->ip(),
                ]);
                BoxStock::where('id', $stockId)->where('status', 0)->update(['status' => 1]);
            }

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$operator Assign box no: ($logBoxNo). Assign By: " . Auth::user()->name,
                'type' => 2,
                'module_type' => 6,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);

            DB::commit();

            return response()->json([
                "status" => "success",
                "status_code" => 201,
                "message" => "Box No: $logBoxNo Assign successfully."
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();

            return response()->json([
                "status" => "error",
                "status_code" => 422,
                "message" => "Validation failed. Error: " . $e->getMessage()
            ])->setStatusCode(422);

        } catch (\Exception $e) {
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
    public function edit($id)
    {
        try {
            $data = BoxOperation::with('user')->with('boxStock')->findOrFail($id);

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Box Operation edit data found",
                "data" => $data
            ])->setStatusCode(200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 404,
                "message" => "Box Operation edit data not found."
            ])->setStatusCode(404);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'box_stock_id' => 'bail|required',
                'assign_by' => 'bail|required',
                'user_id' => 'bail|required',
            ]);

            $boxOperation = BoxOperation::whereIn('status', [0, 2])->find($id);

            if (!$boxOperation) {
                return response()->json([
                    "status" => "error",
                    "status_code" => 200,
                    "message" => "This box has already been accepted. no editable operations are allowed."
                ])->setStatusCode(200);
            }

            $assignedBox = BoxOperation::where('box_stock_id', $request->box_stock_id)
                ->where('status', '!=', 2)
                ->first();

            if ($assignedBox) {
                return response()->json([
                    "status" => "error",
                    "status_code" => 200,
                    "message" => "This box has already been assigned.",
                    "data" => $assignedBox
                ])->setStatusCode(200);
            }

            $boxNo = BoxStock::where('id', $request->box_stock_id)->whereIn('status', [0, 1])->value('box_no');
            $operatorName = User::where('id', $request->user_id)->value('name');
            $oldBoxStockId = $boxOperation->box_stock_id;
            $oldBoxNo = BoxStock::where('id', $oldBoxStockId)->value('box_no');
            $oldOperatorName = User::where('id', $boxOperation->user_id)->value('name');

            $boxOperation->update([
                'box_stock_id' => $request->box_stock_id,
                'assign_by' => $request->assign_by,
                'user_id' => $request->user_id,
                'updated_by' => Auth::id(),
                'updated_by_ip' => $request->ip(),
            ]);

            BoxStock::where('id', $oldBoxStockId)->update(['status' => 0]);
            BoxStock::where('id', $request->box_stock_id)->whereIn('status', [0, 1])->update(['status' => 1]);

            Log::create([
                'user_id' => Auth::id(),
                'action' => "$operatorName Update box no: $boxNo. Old Operator: $oldOperatorName, Old Box no: $oldBoxNo Updated By: " . Auth::user()->name,
                'type' => 2,
                'module_type' => 6,
            ]);

            DB::commit();

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => "Box No: $boxNo updated successfully."
            ])->setStatusCode(200);

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

    public function updateStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $boxOperation = BoxOperation::where('id', $id)->first();

            if ($boxOperation && in_array($boxOperation->status, [2, 3])) {
                $statusMessages = [
                    2 => "This box has already Canceled.",
                    3 => "This box has already completed."
                ];
                return response()->json([
                    "status" => "error",
                    "status_code" => 200,
                    "message" => $statusMessages[$boxOperation->status]
                ])->setStatusCode(200);
            }

            if($request->status == 2) {
                $request->validate(['reason' => 'bail|required']);
                $boxOperation->reason = $request->reason;
            }

            $boxOperation->status = $request->status;
            $boxOperation->updated_by = Auth::id();
            $boxOperation->updated_by_ip = request()->ip();
            $boxOperation->save();

            $boxStockId = $boxOperation->box_stock_id;
            $boxNo = BoxStock::where('id', $boxStockId)->value('box_no');

            if($request->status != 1) {
                BoxStock::where('id', $boxStockId)->update([
                    'status' => $request->status,
                    'updated_by' => Auth::id(),
                    'updated_by_ip' => request()->ip()
                ]);
            }

            $actionMessages = [
                1 => "$boxNo accepted. Accept by ". Auth::user()->name,
                2 => "$boxNo Rejected. Reason $request->reason Rejected by". Auth::user()->name,
                3 => "$boxNo Delivered. Delivery by ". Auth::user()->name,
            ];

            Log::create([
                'user_id' => Auth::id(),
                'action' => $actionMessages[$request->status],
                'type' => 2,
                'module_type' => 6,
                'created_by' => Auth::id(),
                'created_by_ip' => request()->ip(),
            ]);

            DB::commit();

            $statusMessages = [
                1 => "Box No: $boxNo accepted successfully.",
                2 => "Box No: $boxNo Rejected successfully.",
                3 => "Box No: $boxNo Delivered successfully.",
            ];

            return response()->json([
                "status" => "success",
                "status_code" => 200,
                "message" => $statusMessages[$request->status]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "status_code" => 500,
                "message" => "Error: " . $e->getMessage()
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
