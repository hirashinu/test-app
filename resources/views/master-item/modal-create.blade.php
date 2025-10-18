<div class="modal" tabindex="-1" id="formMasterItemModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Master Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formMasterItem">
                <div class="modal-body">
                    @csrf
                    <div id="dataMasterItemModal" class="form-horizontal">
                        <input type="hidden" name="master_item_id" id="master-item-id">
                        <div class="mb-3">
                            <label for="item-name" class="col-form-label">Item Name:</label>
                            <input type="text" class="form-control" id="item-name" name="item_nane"></input>
                        </div>
                        <div class="mb-3">
                            <label for="item-code" class="col-form-label">Item Code:</label>
                            <input type="text" class="form-control" id="item-code" name="item_code"></input>
                        </div>
                        <div class="mb-3" style="display: none;" id="form-status">
                            <label for="status" class="col-form-label">Status:</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="closeMasterItemModal" type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Close</button>
                    <button id="btn-save-master-item" type="submit" class="btn btn-primary">Save</button>
                    <button id="btn-update-master-item" type="submit" class="btn btn-primary" style="display: none;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>