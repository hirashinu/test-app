<?php
namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionRepository
{
    public $request, $transaction;

    function __construct(Request $request, Transaction $transaction) {
        $this->request = $request;
        $this->transaction = $transaction;
    }

    public function getAll()
    {
        return $this->transaction::all();
    }

    public function find($id)
    {
        return $this->transaction::find($id);
    }

    public function get($request)
    {
        $query = $this->transaction::query();
        if (isset($request->search)) {
            $query = $query->where(function($q) use ($request) {
                $q->where('item_code', 'LIKE', '%'.$request->search.'%')
                ->orWhere('quantity', 'LIKE', '%'.$request->search.'%')
                ;
            });
        }
        if ($request->orderColumn && $request->orderDir) {
            if (in_array($request->orderColumn, ['id', 'item_code', 'quantity'])) {
                $query = $query->orderBy($request->orderColumn, $request->orderDir);
            }
        }
        if ($request->count) return $query->count();
        if (array_key_exists('length', $request->all()) && array_key_exists('page', $request->all())) {
            return $query->paginate($request->length, [
                '*'
            ], 'page', $request->page);
        }

        return $query->get();
    }

    public function store($request, $userId)
    {
    	$transaction = new $this->transaction;
        $transaction->item_code = $request->item_code;
        $transaction->quantity = (int) $request->quantity;
        $transaction->save();

        return $transaction;
    }

    public function delete($id, $userId)
    {
        $transaction = $this->find($id);

        if ($transaction) $transaction->delete();
    }
}
?>
