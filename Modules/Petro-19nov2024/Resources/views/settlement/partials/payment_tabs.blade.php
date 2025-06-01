<style>
    .notice_card{
        color: {{$font_color}} !important;
        font-family:  {!! $font_family !!} !important;
        background-color: {{$background_color}} !important;
        font-size:  {{$font_size}}px !important;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="settlement_tabs">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#cash_tab" class="tabs cash_tab" data-toggle="tab">
                        <i class="fa fa-money"></i> 
                        @if(empty($package_details['rename_cash_tab']))
                            <strong>@lang('petro::lang.cash')</strong>
                        @else
                            <strong>@lang('petro::lang.sale_amount')</strong>
                        @endif
                    </a>
                </li>
                
                <li>
                    <a href="#cash_deposit_tab" class="tabs cards_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card"></i> <strong>
                            @lang('petro::lang.cash_deposit') </strong>
                    </a>
                </li>
           
                <li>
                    <a href="#cards_tab" class="tabs cards_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card"></i> <strong>
                            @lang('petro::lang.cards') </strong>
                    </a>
                </li>

                <li>
                    <a href="#cheques_tab" class="tabs cheques_tab" style="" data-toggle="tab">
                        <i class="fa fa-pencil"></i> <strong>
                            @lang('petro::lang.cheques') </strong>
                    </a>
                </li>

                <li>
                    <a href="#expense_tab" class="tabs expense_tab" style="" data-toggle="tab">
                        <i class="fa fa-bell-o"></i> <strong>
                            @lang('petro::lang.expneses') </strong>
                    </a>
                </li>

                <li>
                    <a href="#shortage_tab" class="tabs shortage_tab" style="" data-toggle="tab">
                        <i class="fa fa-thermometer-O"></i> <strong>
                            @lang('petro::lang.shortage') </strong>
                    </a>
                </li>
            
                <li>
                    <a href="#excess_tab" class="tabs excess_tab" style="" data-toggle="tab">
                        <i class="fa fa-thermometer-full"></i> <strong>
                            @lang('petro::lang.excess') </strong>
                    </a>
                </li>

                <li>
                    <a href="#credit_sales_tab" class="tabs credit_sales_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.credit_sales') </strong>
                    </a>
                </li>
            
                <li>
                    <a href="#loan_payments_tab" class="tabs loan_payments_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.loan_payments') </strong>
                    </a>
                </li>
                
                <li>
                    <a href="#drawing_payments_tab" class="tabs drawing_payments_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.drawing_payments') </strong>
                    </a>
                </li>
                <li>
                    <a href="#settlement_customer_loans" class="tabs settlement_customer_loans_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.customer_loans') </strong>
                    </a>
                </li>
                
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="cash_tab">
                    
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_cash']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.cash')
                    </div>
                    
                </div>
                
                <div class="tab-pane" id="cash_deposit_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_cash_deposit']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.cash_deposit')
                    </div>
                    
                </div>

                <div class="tab-pane" id="cards_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_cards']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.cards')
                    </div>
                   
                </div>

                <div class="tab-pane" id="cheques_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_cheques']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.cheques')
                    </div>
                   
                </div>

                <div class="tab-pane" id="expense_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_expenses']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.expense')
                    </div>
                   
                </div>

                <div class="tab-pane" id="shortage_tab">
                     @php $class = ""; @endphp
                    @if(!empty($package_details['ns_shortage']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.shortage')
                    </div>
                   
                </div>

                <div class="tab-pane" id="excess_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_excess']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.excess')
                    </div>
                   
                </div>

                <div class="tab-pane" id="credit_sales_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_credit_sales']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.credit_sales')
                    </div>
                   
                </div>
                
                <div class="tab-pane" id="loan_payments_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_loan_payments']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.loan_payments')
                    </div>
                    
                   
                </div>
                
                <div class="tab-pane" id="drawing_payments_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_drawing_payments']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.owners_drawings')
                    </div>
                    
                   
                </div>
                
                <div class="tab-pane" id="settlement_customer_loans">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_customer_loans']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('petro::settlement.partials.payment_tabs.customer_loans')
                    </div>
                    
                   
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
      $('ul.nav li').click(function() {
        $('ul.nav li').removeClass('active'); // remove the active class from all li elements
        $(this).addClass('active'); // add the active class to the clicked li element
      });
    });
</script>