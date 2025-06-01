@extends('layouts.app')
@section('title', __('lang_v1.'.$type.'s'))

@section('content')

<!-- Content Header (Page header) -->

<style>
  
.popup{
   
    cursor: pointer
}
.popupshow{
    z-index: 99999;
    display: none;
}
.popupshow .overlay{
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.66);
    position: absolute;
    top: 0;
    left: 0;
}
.popupshow .img-show{
        width: 900px;
    height: 600px;
    background: #FFF;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    overflow: hidden;
}
.img-show span{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
    cursor: pointer;
}
.img-show img{
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
/*End style*/

</style>


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Contacts</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Contacts</a></li>
                    <li><span>Manage contacts</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>



<!-- Main content -->
<section class="content main-content-inner">
 <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="">
                         <a class="collapse-item " href="{{action('\Modules\Vat\Http\Controllers\VatContactController@index',['type' => 'customer'])}}">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.customers')</strong>
                        </a>
                    </li>
                  
                    <li class="">
                         <a class="collapse-item " href="{{action('\Modules\Vat\Http\Controllers\VatContactController@index',['type' => 'supplier'])}}">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.suppliers')</strong>
                        </a>
                    </li>
                  
                </ul>
                </div>
            </div>
        </div>
  
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.all_your_contact', ['contacts' =>
    __('lang_v1.'.$type.'s') ])])
    
   <div class="box-tools pull-right">
        <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" >
        <button type="button" class="btn btn-primary btn-modal"
            data-href="{{action('\Modules\Vat\Http\Controllers\VatContactController@create', ['type' => $type])}}" data-container=".contact_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
        
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%" id="vat_contact_table">
            <thead>
                <tr>
                    <td colspan="6">
                        <div class="row">
                            <div class="col-sm-2">
                                @if(auth()->user()->can('customer.delete') || auth()->user()->can('supplier.delete'))
                                    {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatContactController@massDestroy'), 'method' => 'post', 'id'
                                    => 'mass_delete_form' ]) !!}
                                    {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                                    {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger',
                                    'id' => 'delete-selected')) !!}
                                    {!! Form::close() !!}
                                @endif
                            </div>
                            
                        </div>
                        
                    </td>
                    
                </tr>
                
                <tr>
                    <th><input type="checkbox" id="select-all-row"></th>
                    <th class="notexport">@lang('messages.action')</th>
                    <th >@lang('lang_v1.contact_id')</th>
                    <th>@lang('contact.name')</th>
                    <th>@lang('contact.mobile')</th>
                    <th>@lang('lang_v1.added_on')</th>
                </tr>
            </thead>
        </table>
    </div>
    
    @endcomponent

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>


<!-- /.content -->

@endsection

@section('javascript')

@if(session('status'))
    @if(session('status')['success'])
        <script>
            toastr.success('{{ session("status")["msg"] }}');
        </script>
    @else
        <script>
            toastr.error('{{ session("status")["msg"] }}');
        </script>
    @endif
@endif


<script>
    
    $('.contact_modal').on('shown.bs.modal', function() {
        $('.contact_modal')
        .find('.select2')
        .each(function() {
            var $p = $(this).parent();
            $(this).select2({ 
                dropdownParent: $p
            });
        });

    });
    $(document).on('click', '#delete-selected', function(e){
        e.preventDefault();
        var selected_rows = getSelectedRows();

        if(selected_rows.length > 0){
        $('input#selected_rows').val(selected_rows);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                $('form#mass_delete_form').submit();
                }
            });
        } else{
        $('input#selected_rows').val('');
            swal('@lang("lang_v1.no_row_selected")');
        }
    });
    
    
    function getSelectedRows() {
        var selected_rows = [];
        var i = 0;
        $('.row-select:checked').each(function () {
            selected_rows[i++] = $(this).val();
        });

        return selected_rows;
    }


</script>
@endsection
