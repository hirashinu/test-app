<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Repositories\Transaction\TransactionRepository as Transaction;
use App\Repositories\MasterItem\MasterItemRepository as MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private $transaction, $masterItem;

    public function __construct(Transaction $transaction, MasterItem $masterItem)
    {
        $this->transaction = $transaction;
        $this->masterItem = $masterItem;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $masterItems = $this->masterItem->get(new Request(['status' => 1]));

        return view('home')
        ->with('masterItems', $masterItems)
        ;
    }

    public function dataTransaction(Request $request)
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
            $totalRecords = $this->transaction->get(new Request([
                'count' => true
            ]));

            // Total records with filter
            $totalRecordswithFilter = $this->transaction->get(new Request([
                'search' => $search, 
                'count' => true
            ]));

            // Fetch records
            $records = $this->transaction->get(new Request([
                'search' => $search,
                'orderColumn' => $orderColumn,
                'orderDir' => $orderDir,
                'start' => $start,
                'length' => $length,
                'page' => $page
            ]));

            $no = $start + 1;

            $records->map(function($item, $key) use ($no) {
                $item->DT_RowId = "transaction_{$item->id}";
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
            Log::error('TransactionController->dataTransaction: '.$ex);
        }

        exit;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $transaction = $this->transaction->store($request, Auth::id());

            return response()->json([
                'status' => 200,
                'message' => 'Store transaction success'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('TransactionController->store: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Store transaction failed'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $this->transaction->delete($id, Auth::id());

            return response()->json([
                'status' => 200,
                'message' => 'Delete transaction success'
            ], 200);
        } catch (\Exception $ex) {
            Log::error('TransactionController->delete: '.$ex);

            return response()->json([
                'status' => 400,
                'message' => 'Delete transaction failed'
            ], 400);
        }
    }
}
