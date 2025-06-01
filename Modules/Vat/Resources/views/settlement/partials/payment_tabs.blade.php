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
                        @if(!empty($package_details['rename_cash_tab']))
                            <strong>@lang('petro::lang.cash')</strong>
                        @else
                            <strong>@lang('petro::lang.sale_amount')</strong>
                        @endif
                    </a>
                </li>
                
               
           
                <li>
                    <a href="#cards_tab" class="tabs cards_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card"></i> <strong>
                            @lang('petro::lang.cards') </strong>
                    </a>
                </li>

            
                <li>
                    <a href="#credit_sales_tab" class="tabs credit_sales_tab" style="" data-toggle="tab">
                        <i class="fa fa-credit-card-alt"></i> <strong>
                            @lang('petro::lang.credit_sales') </strong>
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
                        @include('vat::settlement.partials.payment_tabs.cash')
                    </div>
                    
                </div>
                
                <div class="tab-pane" id="cards_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_cards']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('vat::settlement.partials.payment_tabs.cards')
                    </div>
                   
                </div>

                <div class="tab-pane" id="credit_sales_tab">
                    @php $class = ""; @endphp
                    @if(!empty($package_details['ns_credit_sales']))
                        @php $class = "hidden"; @endphp
                        <div class="notice_card card text-center"> {{ $message }} </div>
                    @endif
                    <div class="{{$class}}">
                        @include('vat::settlement.partials.payment_tabs.credit_sales')
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