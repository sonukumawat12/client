<div class="modal fade text-left" id="discountModal" data-keyboard="false" data-backdrop="static" tabindex="-1"
                role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true" style="overflow-y: scroll;">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel33">@lang('discount::lang.add_discount')</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                onclick="closeModalWithClearValidation()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                            <div class="modal-body">
                        {!! Form::open(['url' => action('\Modules\Discount\Http\Controllers\NewdiscountController@store'), 'method' => 'post',
        'id' => 'Register', 'files' => true ]) !!}
                            <label strong style="font-size: 16px;">Date & Time:</label><label
                                    style="color: red; font-size:16px;">*</label>
                                <div class="form-group">
                              <input type="datetime-local" class="form-control" id="date_and_time" name="data_time" required>
                                    <span class="text-danger">
                                        <strong id="name-error"></strong>
                                    </span>
                                </div>
                                <label strong style="font-size: 16px;">Sub Category</label><label
                                            style="color: red; font-size:16px;">*</label>
                                        <div class="form-group">
                                            <div>
                                                <select class="custom-select browser-default select2" name="sub_category" required>
                                                    <option value="">Please select here</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                                <span class="text-danger">
                                                    <strong id="category-error"></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <label strong style="font-size: 16px;">Users</label><label
                                            style="color: red; font-size:16px;">*</label>
                                        <div class="form-group">
                                            <div>
                                                <select class="form-control custom-select browser-default select2" name="users[]"
                                                    id="user" onchange="removecategoryValidation()" id="multselect" multiple required>
                                                    <option value=""></option>
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->username }}">{{ $user->username }}</option>
                                                    @endforeach
                                                    
                                                </select>
                                                <span class="text-danger">
                                                    <strong id="category-error"></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <label strong style="font-size: 16px;">Maximum discount %</label>
                                        <label style="color: red; font-size:16px;">*</label>
                                <div class="form-group">
                              <input type="text" class="form-control" id="discount" pattern="^\d+(\.\d+)?$" name="max_discount" placeholder="Write discount without %" required>
                                    <span class="text-danger">
                                        <strong id="discount-error"></strong>
                                    </span>
                                </div>
                                        
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-info">Save & Close</button>
                                <button id="closebutton" type="button" class="btn btn-danger"
                                    onclick="closeModalWithClearValidation()" data-dismiss="modal">Close</button>
                            </div>
                    </div>
                </div>
            </div>