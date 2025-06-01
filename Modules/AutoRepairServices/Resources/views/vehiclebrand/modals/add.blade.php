<!-- Small Modal -->
<div id="addBrandService" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="modalFrm" onsubmit="listContent.onAddSubmit(event)">
                <input type="hidden" class="modfld" name="mod-id" id="mod-id" />
                <div class="modal-header">
                    <h4 class="modal-title users"><strong>@lang( 'autorepairservices::lang.add_new' )</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="col-form-label">@lang( 'autorepairservices::lang.vehicle_brand' ):</label>
                        <input type="text" class="form-control" name="vehicle_brand" required />
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">@lang( 'autorepairservices::lang.vehicle_type' ):</label>
                        <input type="text" class="form-control" name="vehicle_type" required />
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">@lang( 'autorepairservices::lang.vehicle_model' ):</label>
                        <input type="text" class="form-control" name="vehicle_model" required />
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-form-label">@lang( 'autorepairservices::lang.chassis_no' ):</label>
                        <input type="number" class="form-control" name="chassis_no" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary m-1">
                        <i class="i-Data-Save"></i>
                        Save
                    </button>
                    <button type="button" class="btn btn-outline-secondary m-1" data-dismiss="modal" id="closeModal">
                        <i class="i-Close-Window"></i>
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
