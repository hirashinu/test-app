<?php
namespace App\Repositories\MasterItem;

use App\Models\MasterItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterItemRepository
{
    public $request, $masterItem;

    function __construct(Request $request, MasterItem $masterItem) {
        $this->request = $request;
        $this->masterItem = $masterItem;
    }

    public function getAll()
    {
        return $this->masterItem::all();
    }

    public function find($id)
    {
        return $this->masterItem::find($id);
    }

    public function get($request)
    {
        $query = $this->masterItem::select(
            'master_items.*', 
            DB::raw("
                CASE
                    WHEN status = 1
                    THEN 'Active'
                    ELSE 'Inactive'
                END AS format_status
            "), 
        );
        if (array_key_exists('status', $request->all())) $query = $query->where('status', $request->status);
        if (isset($request->search)) {
            $query = $query->where(function($q) use ($request) {
                $q->where('item_name', 'LIKE', '%'.$request->search.'%')
                ->orWhere('item_code', 'LIKE', '%'.$request->search.'%')
                ->orWhereRaw("CASE status
                    WHEN 1 THEN 'Active' 
                    ELSE 'Inactive'
                END LIKE ?", ['%'.$request->search.'%'])
                ;
            });
        }
        if ($request->orderColumn && $request->orderDir) {
            if (in_array($request->orderColumn, ['id', 'item_name', 'item_code', 'quantity', 'status'])) {
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
    	$masterItem = new $this->masterItem;
        $masterItem->item_name = $request->item_name;
        $masterItem->item_code = $request->item_code;
        $masterItem->save();

        return $masterItem;
    }
    
    public function update($request, $userId)
    {
    	$masterItem = $this->find($request->id);

        if ($masterItem) {
            if (array_key_exists('item_name', $request->all())) $masterItem->item_name = $request->item_name;
            if (array_key_exists('item_code', $request->all())) $masterItem->item_code = $request->item_code;
            if (array_key_exists('status', $request->all())) $masterItem->status = $request->status;
            
            $masterItem->update();
        }

        return $masterItem;
    }

    public function delete($id, $userId)
    {
        $masterItem = $this->find($id);

        if ($masterItem) $masterItem->delete();
    }
}
?>
