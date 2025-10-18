<div class="modal" tabindex="-1" id="formTransactionModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTransaction">
                <div class="modal-body">
                    @csrf
                    <div id="dataTransactionModal" class="form-horizontal">
                        <div class="mb-3">
                            <label for="item-code" class="col-form-label">Item Code:</label>
                            <select class="form-control" id="item-code">
                                <option value="">Choose item code</option>
                                @foreach($masterItems as $masterItem)
                                <option value="{{ $masterItem->item_code }}">{{ $masterItem->item_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity"></input>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="closeTransactionModal" type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Close</button>
                    <button id="btn-save-transaction" type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>