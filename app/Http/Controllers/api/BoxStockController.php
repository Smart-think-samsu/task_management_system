<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BoxStock;
use Illuminate\Http\Request;

class BoxStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $dlStocks = BoxStock::orderBy('id', 'desc')->paginate(10);

            if ($dlStocks->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 404,
                    'message' => 'No data found',
                ])->setStatusCode(404);
            }

            return response()->json([
               'status' => 'success',
               'status_code' => 200,
               'message' => 'Box Stock data retrieved successfully',
               'data' => $dlStocks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }

    }

    public function boxStockSearch(Request $request)
    {
        try {
            $dlStocks = BoxStock::where('box_no', $request->box_no)->get();

            if ($dlStocks->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'status_code' => 404,
                    'message' => 'No data found',
                ])->setStatusCode(404);
            }
            return response()->json([
                'status' => 'success',
                'status_code' => 200,
                'message' => 'Box Stock data retrieved successfully',
                'data' => $dlStocks
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'status_code' => 500,
                'message' => $e->getMessage(),
            ])->setStatusCode(500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
