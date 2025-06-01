@extends('layouts.app')
@section('title', 'Form Settings')

@section('css')

@endsection

@section('content')

@php
$business_id = request()->session()->get('user.business_id');
$type = !empty($type) ? $type : 'customer';
$customer_groups = App\ContactGroup::where('business_id',$business_id)
->where(function ($query) use ($type) {
$query->where('contact_groups.type', 'customer')
->orWhere('contact_groups.type', 'both');
})->pluck('name','id');
@endphp


<?php



$tab = isset($_GET['tab'])?$_GET['tab']:'customers';



switch ($tab) {

    case "suppliers":

        $activeTab =  "suppliers";

        break;

    case "passengers":

        $activeTab =  "passengers";

        
      
    break;   
    default:

       $activeTab =  "customers";

}
?>



<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h5 class="page-title pull-left">Form Settings</h5>
           
            </div>
        </div>
    </div>
</div>

<section class="content main-content-inner">



       
<div class="settlement_tabs">

        <ul class="nav nav-tabs">

            <li class="<?php echo $activeTab === 'customers' ? 'active' : ''; ?>">

                <a href="#customers" data-toggle="tab">

                     <strong>Customers</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'suppliers' ? 'active' : ''; ?>">

                <a href="#suppliers" data-toggle="tab">

                    <strong>Suppliers</strong>

                </a>

            </li>

          
            <li class="<?php echo $activeTab === 'passengers' ? 'active' : ''; ?>">

                <a href="#passengers" data-toggle="tab">

                    <strong>Passengers</strong>

                </a>

            </li>

        </ul>



        <div class="tab-content">

            <div class="tab-pane <?php echo $activeTab === 'customers' ? 'active' : ''; ?>" id="customers">

                <div class="row">

                    <div class="col-md-12 ">


                    @include('airline::form_settings.customers')
                        

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-12" style="padding-top: 50px;">


                    @include('airline::form_settings.invoice')
                        

                    </div>

                </div>

            </div>

            

             <div class="tab-pane <?php echo $activeTab === 'suppliers' ? 'active' : ''; ?>" id="suppliers">

                <div class="row ">

                    <div class="col-md-12 mt-4">

                    @include('airline::form_settings.suppliers')

                    </div>

                </div>

            </div>
             <div class="tab-pane <?php echo $activeTab === 'passengers' ? 'active' : ''; ?>" id="passengers">

                <div class="row">

                    <div class="col-md-12">

                    @include('airline::form_settings.passengers')

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-danger pull-right settingForm_button"
                type="submit">@lang('business.update_settings')</button>
        </div>
    </div> --}}


</section>


@endsection

@section('javascript')
<script>

    $(document).ready(function() {
      
    });
    
      
      
</script>
@endsection