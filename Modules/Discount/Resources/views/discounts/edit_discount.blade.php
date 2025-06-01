<div class="modal fade text-left" id="examplemodal-edit" data-keyboard="false" data-backdrop="static"
                tabindex="-1" role="dialog" aria-labelledby="myModalLabel34" aria-hidden="true"
                style="overflow-y: scroll;">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel34">Update Region</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                onclick="closeModalWithClearValidation()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <ul id="save_msgList"></ul>
                        <form id="updateRegister">
                            {{ csrf_field() }}

                            <input type="hidden" id="edit_id" />
                            <div class="modal-body">
                                <label strong style="font-size: 16px;">Region Name</label><label
                                    style="color: red; font-size:16px;">*</label>
                                <div class="form-group">
                                    <input type="text" placeholder="Write Region Name" class="form-control"
                                        name="Rgn_Name" id='edit_name' onclick="removeRgnNameValidation()" autofocus />
                                    <span class="text-danger">
                                        <strong id="uname-error"></strong>
                                    </span>
                                </div>
                                <label strong style="font-size: 16px;">Region Number</label><label
                                    style="color: red; font-size:16px;">*</label>
                                <div class="form-group">
                                    <input type="number" placeholder="Write Region Number" class="form-control"
                                        name="Rgn_Number" id="edit_number" onclick="removeNumberValidation()" />
                                    <span class="text-danger">
                                        <strong id="unumber-error"></strong>
                                    </span>
                                </div>
                                <label strong style="font-size: 16px;">Description</label><label
                                    style="color: red; font-size:16px;"></label>
                                <div class="form-group">
                                    <textarea name="description" placeholder="Wriet descriptio here" id="edit_description" cols="20"
                                        rows="4" class="form-control" onclick="removeDescriptionValidation()"></textarea>
                                    <span class="text-danger">
                                        <strong id="udescription-error"></strong>
                                    </span>
                                </div>
                                <label strong style="font-size: 16px;">Status</label><label
                                    style="color: red; font-size:16px;">*</label>
                                <div class="form-group">
                                    <div>
                                        <select class="custom-select browser-default select2" name="status"
                                            id="edit_status" onclick="removeStatusValidation()">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                        <span class="text-danger">
                                            <strong id="ustatus-error"></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="updatebutton" type="button" class="btn btn-info">Update</button>
                                <button id="closebutton" type="button"
                                    class="btn btn-danger wave-effect waves-float waves-light" data-dismiss="modal"
                                    onclick="closeModalWithClearValidation()">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>