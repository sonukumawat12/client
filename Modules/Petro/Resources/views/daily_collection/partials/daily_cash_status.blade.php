<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.daily_cash_status')</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content" style="padding: 4px!important;">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            
            <div class="row justify-content-between align-items-end">



                <!-- Date Picker -->
                <div class="col-md-4 col-sm-12">
                    <div class="form-group d-flex align-items-center gap-2">
                        {!! Form::label('cash_transaction_date', __('petro::lang.date') . ':', ['class' => 'mb-0']) !!}
                        {!! Form::text('cash_transaction_date', \Carbon\Carbon::now()->format('Y-m-d'), [
                            'class' => 'form-control date-picker',
                            'placeholder' => __('petro::lang.select_a_date'),
                            'autocomplete' => 'off',
                            'id' => 'cash_transaction_date',
                            'style' => 'width: auto; max-width: 100%;'
                        ]) !!}
                    </div>
                </div>



                <!-- Shift -->
                <div class="col-md-3 col-sm-12">
                    <div class="form-group d-flex">
                        {!! Form::label('shift', __('petro::lang.shift') . ':') !!}

                        {!! Form::select('shift', array_combine($dailyCashShiftNumbers, $dailyCashShiftNumbers), null, [
                            'class' => 'form-control',
                            'placeholder' => "Select Shift No",
                            'id' => 'shift'
                        ]) !!}
                    </div>
                </div>


                <!-- Title -->
                <div class="col-md-4 col-sm-12">
                    <p style="text-align: end; font-size: 16px; font-weight: 600;">
                        Daily Cash Status No: 1
                    </p>
                </div>

                

                
            </div>

            @endcomponent
        </div>


    </div>
    <div @style([ 'margin-top:30px' , 'border: 1px solid #2974A6' , 'padding: 40px' , 'border-radius: 9px', 'position:relative' ])>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('cash_collection', __('petro::lang.cash_collection') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('cash_collection',null, ['class' => 'form-control status-text',
                    'readonly' => true,
                    'placeholder' => __(
                    'petro::lang.cash_collection' ),]); !!}
                </div>
            </div>

            <div class="col-md-2" style ="display:none;">


                <div class="form-group">
                    {!! Form::label('other_income_cash', __('petro::lang.other_income_cash') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('other_income_cash',null, ['class' => 'form-control status-text',
                    'readonly' => true,
                    'placeholder' => __(
                    'petro::lang.other_income_cash' ),]); !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('customer_payment_cash', __('petro::lang.customer_payment_cash') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('customer_payment_cash',null, ['class' => 'form-control status-text',
                    'readonly' => true,
                    'placeholder' => __(
                    'petro::lang.customer_payment_cash' ),]); !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('cash_expense', __('petro::lang.cash_expense') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('cash_expense',null, ['class' => 'form-control status-text',
                    'readonly' => true,
                    'placeholder' => __(
                    'petro::lang.cash_expense' ),]); !!}
                    {{-- shift_numbers --}}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('cash_deposit', __('petro::lang.cash_deposit') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('cash_deposit',null, ['class' => 'form-control status-text',
                    'placeholder' => __(
                    'petro::lang.cash_deposit' ),
                    'readonly' => true,
                    ]); !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! Form::label('balance_in_hand', __('petro::lang.balance_in_hand') . ':', ['class' => 'status-label']) !!}
                    {!! Form::text('balance_in_hand',null, ['class' => 'form-control status-text',
                    'readonly' => true,
                    'placeholder' => __(
                    'petro::lang.balance_in_hand' ),]); !!}
                </div>
            </div>

        </div>

        <div class="row" style="margin-top: 50px;">
            <!-- Left column (3 cols) -->
            <div class="col-md-4 cash-details"  style="border-right:1px solid #71ADBC;">
                <p style="" class="cash-heading">Detail Cash Collection</p>
                <div class="status-details">
                    <div>
                        <p>Pump Operator</p>
                        <div id="operator_names">
                            @if (!empty($assigned_operators))
                                @foreach($assigned_operators as $key => $operator)
                                    <p>{{ is_array($operator) ? $operator['name'] : $operator }}</p>
                                @endforeach
                            @else
                                <p>No operators assigned.</p>
                            @endif
                        </div>
                        <p>Total</p>
                        
                    </div>
                    <div>
                        <p>Amount</p>
                        <div id="per_operator_total">
                        </div>
                        <p id="pump_operators_total"></p>
                    </div>
                </div>
                
            </div>
        
            <div class="col-md-4 cash-details" style="border-right:1px solid #71ADBC;">
                <p style="" class="cash-heading">Detail Customer Payments</p>
                <div class="status-details">
                    <div>
                        <p>Customer</p>
                        <div class="mt-4" id="customers_names">
                        </div>
                        <p>Total</p>
                    </div>
                    <div>
                        <p>Amount</p>
                        <div class="mt-4" id="customer_expense">
                        </div>
                        <p class="mt-2" id="customer_total"></p>
                    </div>
                </div>
            </div>
        
            <!-- Right column (3 cols) -->
            <div class="col-md-4 cash-details">
                <p style="" class="cash-heading">Cash Expenses</p>
                <div class="status-details">
                    <div>
                        <p>Expenses</p>
                        <div class="mt-4" id="cash_expense_name">
                        </div>
                        <p>Total</p>
                    </div>
                    <div>
                        <p>Amount</p>
                        <div class="mt-4" id="cash_expense_cost">
                        </div>
                        <p class="mt-2" id="cash_expense_total"></p>
                    </div>

                </div>
            </div>
        </div>


        <div>
            <button class="close-btn" id="close-shift-btn">
                Close Shift
            </button>
        </div>
    </div>


</section>

<script>

    $(document).ready(function() {
        $('#shift').on('change', function() {
            let shift = $(this).val();

            if (shift) {
                $.ajax({
                    url: "{{ route('daily.cash.status.data') }}",
                    type: 'GET',
                    data: { shift: shift },
                    success: function(response) {
                        // Assuming the API returns an object like { cash_collection: 1234.56 }
                        if (response.cash_collection !== undefined) {
                            $('input[name="cash_collection"]').val(response.cash_collection);
                        } else {
                            $('input[name="cash_collection"]').val('');
                        }
                        if (response.operators) {
                            const operators = response.operators.operators_payment || [];
                            const totalAmount = response.operators.total_amount || 0;

                            let nameHtml = '';
                            let amountHtml = '';

                            if (operators.length > 0) {
                                operators.forEach(op => {
                                    nameHtml += `<p>${op.operator_name}</p>`;
                                    amountHtml += `<p>${op.total_payment}</p>`;
                                });
                            } else {
                                nameHtml = '<p>No operators assigned.</p>';
                            }

                            $('#operator_names').html(nameHtml);
                            $('#per_operator_total').html(amountHtml);
                            $('#pump_operators_total').html(`<strong>${totalAmount}</strong>`);
                        }
                    },
                    error: function() {
                        $('input[name="cash_collection"]').val('');
                        alert('Error fetching cash collection data.');
                    }
                });
            } else {
                $('input[name="cash_collection"]').val('');
            }
        });


         $('#close-shift-btn').on('click', function () {
        const shiftId = $('#shift').val();

        if (!shiftId) {
            alert('Please select a shift number first.');
            return;
        }

        $.ajax({
            url: "{{ route('daily_shift.close') }}",
            method: 'POST',
            data: {
                shift_id: shiftId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                  toastr.success('Shift Saved Successfully');
               
            },
            error: function (xhr) {
                alert(xhr.responseJSON.message || 'Failed to close shift.');
            }
        });
    });

    });
</script>


