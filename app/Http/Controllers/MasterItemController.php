<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterItemRequest;
use App\Http\Requests\UpdateMasterItemRequest;
use App\Repositories\MasterItem\MasterItemRepository as MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MasterItemController extends Controller
{
    private $masterItem;

    public function __construct(MasterItem $masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('master-item');
    }

    public function dataMasterItem(Request $request)
    {
        try {
            $draw = $request->input('draw');
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = $request->input('search.value');
            $page = ($start / $length) + 1;
            
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');
            $orderColumn = $request->input("columns.$orderColumnIndex.name");
            
            // Total records
            $totalRecords = $this->masterItem->get(new Request([
                'count' => true
            ]));

            // Total records with filter
            $totalRecordswithFilter = $this->masterItem->get(new Request([
                'search' => $search, 
                'count' => true
            ]));

            // Fetch records
            $records = $this->masterItem->get(new Request([
                'search' => $search,
                'orderColumn' => $orderColumn,
                'orderDir' => $orderDir,
                'start' => $start,
                'length' => $length,
                'page' => $page
            ]));

            $no = $start + 1;

            $records->map(function($item, $key) use ($no) {
                $item->DT_RowId = "master_item_{$item->id}";
            });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecordswithFilter,
                'data' => $records->items(),
                'orderColumnIndex' => $orderColumnIndex,
                'orderDir' => $orderDir,
                'dataLength' => intval($length)
            ]);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->dataMasterItem: '.$ex);
        }

        exit;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterItemRequest $request)
    {
        try {
            $masterItem = $this->masterItem->store($request, Auth::id());
            
            return response()->json([
                'status' => 200,
                'message' => 'Store master item success'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->store: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Store master item failed'
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $masterItem = $this->masterItem->find($id);

            return response()->json([
                'status' => 200,
                'message' => 'Get master item success',
                'data' => $masterItem
            ], 200);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->show: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Get master item failed'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $masterItem = $this->masterItem->find($id);

            return response()->json([
                'status' => 200,
                'message' => 'Get master item success',
                'data' => $masterItem
            ], 200);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->edit: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Get master item failed'
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterItemRequest $request)
    {
        try {
            $masterItem = $this->masterItem->update($request, Auth::id());
            
            return response()->json([
                'status' => 200,
                'message' => 'Update master item success',
                'data' => $masterItem
            ], 200);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->update: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Update master item failed'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $this->masterItem->delete($id, Auth::id());

            return response()->json([
                'status' => 200,
                'message' => 'Delete master item success'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('MasterItemController->delete: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Delete master item failed'
            ], 400);
        }
    }
}
