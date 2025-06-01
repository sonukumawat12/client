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
        {!! Form::open(['url' => action('\Modules\MPCS\Http\Controllers\F15FormController@mpcs15Update', [$settings->id]), 
            'method' => 'post', 
            'id' => 'update_15_form_settings' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">@lang( 'mpcs::lang.edit_15_form_settings' )</h4>
        </div>

        <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-container"> 
                        @if($settings->form15_label_id == 2)
                        <!-- Ubah semua name attribute sesuai dengan field di database -->
                        <div class="form-group">
                            <label for="ref_previous_form">@lang('Ref Previous Form Number')</label>
                            <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                        </div>
                   @endif
                        @if($settings->form15_label_id == 3)
                    <!-- Total (No 17) Up to Previous Day -->
                    <div class="form-group">
                        <label for="total_up_to_previous_day">@lang('Total (No 17) Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 4)
                    <!-- Opening Stock Up to Previous Day -->
                    <div class="form-group">
                        <label for="opening_stock">@lang('Opening Stock Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 5)
                    <!-- Grand Total Up to Previous Day -->
                    <div class="form-group">
                        <label for="grand_total">@lang('Grand Total Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 7)
                    <!-- Cash Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="cash_sales">@lang('Cash Sales Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 8)
                    <!-- Card Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="card_sales">@lang('Card Sales Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 9)
                    <!-- Credit Sales Up to Previous Day -->
                    <div class="form-group">
                        <label for="credit_sales">@lang('Credit Sales Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 10)
                    <!-- Total (No 31) Up to Previous Day -->
                    <div class="form-group">
                        <label for="total_up_to_previous_day_31">@lang('Total (No 31) Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 11)
                    <!-- Balance Stock in Sale Price Up to Previous Day -->
                    <div class="form-group">
                        <label for="balance_stock">@lang('Balance Stock in Sale Price Up to Previous Day')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
                    @endif
                        @if($settings->form15_label_id == 12)
                    <!-- Grand Total Again -->
                    <div class="form-group">
                        <label for="grand_total_again">@lang('Grand Total Again')</label>
                        <input type="text" name="rupees" value="{{$settings->rupees}}" class="form-control" required>
                    </div>
@endif

                </div>
            </div> 
        </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form#update_15_form_settings').validate();
    });
</script>