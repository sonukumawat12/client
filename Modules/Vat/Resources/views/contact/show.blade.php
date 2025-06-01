@extends('layouts.app')
@section('title', __('contact.view_contact'))
@section('content')


<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('contact.view_contact') }}</h1>
</section>
<!-- Main content -->
<section class="content no-print">
    <div class="hide print_table_part">
        <style type="text/css">
            .info_col {
                width: 25%;
                float: left;
                padding-left: 10px;
                padding-right: 10px;
            }
            .box {
                border: 0px !important;
            }
        </style>
        <div style="width: 100%;">
            
            <div class="info_col">
                @include('vat::contact.contact_more_info')
            </div>
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('contact_id', $contact_dropdown, $contact->id , ['class' => 'form-control select2', 'id' => 'contact_id']) !!}
            <input type="hidden" id="sell_list_filter_customer_id" value="{{$contact->id}}">
            <input type="hidden" id="purchase_list_filter_supplier_id" value="{{$contact->id}}">
        </div>
        <div class="col-md-12 col-xs-12" style="margin-top: 10px;">
            @if($contact->type == 'customer')
                <span class="text-red" style="font-size: 36px;">
                    @lang('contact.customer'): {{$contact->name}}
                </span>
            @endif
        </div>
        
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                        @if(!empty($view_type) &&  $view_type == 'contact_info')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#contact_info_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-user"
                                aria-hidden="true"></i> @lang( 'contact.contact_info', ['contact' =>
                            __('contact.contact') ])</a>
                    </li>
                    
                </ul>
                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                            @if(!empty($view_type) &&  $view_type == 'contact_info')
                                active
                            @else
                                ''
                            @endif" id="contact_info_tab">
                        @include('vat::contact.partials.contact_info_tab')
                    </div>
                    
                   
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="edit_ledger_discount_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

@stop
@section('javascript')

@endsection