<style>
    .form-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        width: 48%; /* Membagi menjadi dua kolom */
    }

    .form-group label {
        font-weight: bold;
    }

    .form-group input {
        width: 100%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form-group .full-width {
        width: 100%; /* Untuk input yang harus penuh di satu kolom */
    }
</style>

<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content"> 
{!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\F15FormController@store15FormSetting'), 'method' => 'post', 'id' => 'add_15_form_settings', 'accept-charset' => 'UTF-8']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang( 'mpcs::lang.add_15_form_settings' )</h4>
        </div>
        <div class="modal-body">
            <div class="col-md-12">
                <br />
@if(auth()->user()->can('superadmin'))
<div class="form-group full-width">
    <label for="form_note">@lang('Note Details')</label>
    <textarea name="form_note" id="form_note" class="form-control"></textarea>
</div>
@endif

                <!-- Form Grid -->
                <div class="form-container">
                    <!-- Date Field -->
<!-- Date Field -->
<div class="form-group full-width">
    <label for="form_date">@lang('Date & Time')</label>
    <input type="date" name="dated_at" id="form_date" class="form-control" value="{{ date('Y-m-d') }}" required>
</div>

                    <!-- F 15 Form Starting Number -->
                    <div class="form-group">
                        <label for="f15_start_number">@lang('F 15 Form Starting Number')</label>
                        <input type="hidden" name="form15_label_id[1]" value="1">
                        <input type="text" name="rupees[1]" class="form-control" required >
                    </div>
                    
                    <!-- Ref Previous Form Number -->
                    <div class="form-group">
                        <label for="ref_previous_form">@lang('Ref Previous Form Number')</label>
                        <input type="hidden" name="form15_label_id[2]" value="2">
                        <input type="text" name="rupees[2]" class="form-control" required >
                    </div>
                    
                    <!-- Store Purchases Amount Up to Previous Day -->
                    <div class="form-group">
                        <label for="store_purchase">@lang('Store Purchases Amount Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[3]" value="3">
                        <input type="text" name="rupees[3]" class="form-control" required >
                    </div>
                    
                    <!-- Total (No 17) Up to Previous Day -->
                    <div class="form-group">
                        <label for="total_up_to_previous_day">@lang('Total (No 17) Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[4]" value="4">
                        <input type="text" name="rupees[4]" class="form-control" required >
                    </div>
                    
                    <!-- Opening Stock Up to Previous Day -->
                    <div class="form-group">
                        <label for="opening_stock">@lang('Opening Stock Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[5]" value="5">
                        <input type="text" name="rupees[5]" class="form-control" required >
                    </div>
                    
                    <!-- Grand Total Up to Previous Day -->
                    <div class="form-group">
                        <label for="grand_total">@lang('Grand Total Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[6]" value="6">
                        <input type="text" name="rupees[6]" class="form-control" required >
                    </div>
                    
                    <!-- Cash Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="cash_sales">@lang('Cash Sales Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[7]" value="7">
                        <input type="text" name="rupees[7]" class="form-control" required >
                    </div>
                    
                    <!-- Card Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="card_sales">@lang('Card Sales Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[8]" value="8">
                        <input type="text" name="rupees[8]" class="form-control" required >
                    </div>
                    
                    <!-- Credit Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="credit_sales">@lang('Credit Sales Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[9]" value="9">
                        <input type="text" name="rupees[9]" class="form-control" required >
                    </div>
                    
                    <!-- Total (No 31) Up to Previous Day -->
                    <div class="form-group">
                        <label for="total_up_to_previous_day_31">@lang('Total (No 31) Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[10]" value="10">
                        <input type="text" name="rupees[10]" class="form-control" required >
                    </div>
                    
                    <!-- Balance Stock in Sale Price Up to Previous Day -->
                    <div class="form-group">
                        <label for="balance_stock">@lang('Balance Stock in Sale Price Up to Previous Day')</label>
                        <input type="hidden" name="form15_label_id[11]" value="11">
                        <input type="text" name="rupees[11]" class="form-control" required >
                    </div>
                    
                    <!-- Grand Total Again -->
                    <div class="form-group">
                        <label for="grand_total_again">@lang('Grand Total Again')</label>
                        <input type="hidden" name="form15_label_id[12]" value="12">
                        <input type="text" name="rupees[12]" class="form-control" required >
                    </div>


                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div> 
        {!! Form::close() !!}
    </div>
</div>
