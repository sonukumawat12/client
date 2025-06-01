@extends('layouts.app')

@section('title', 'Airline Settings')



@section('css')



<?php



$tab = isset($_GET['tab'])?$_GET['tab']:'prefixnstarting';



switch ($tab) {

    case "airlines":

        $activeTab =  "airlines";

        break;

    case "airlinesagents":

        $activeTab =  "airlinesagents";

        break;

    case "linkedaccounts":

        $activeTab =  "linkedaccounts";

        break;

    case "airports":

        $activeTab =  "airports";

        break;

    case "additionalservice":

        $activeTab =  "additionalservice";

        break;   

    case "Commission Types":

        $activeTab =  "Commission Types";

        break;   
    case "Passenger Types":

        $activeTab =  "Passenger Types";

        break;   
    case "Contact Settings":

    $activeTab =  "Contact Settings";

    break; 
    case "Airline Classes":

    $activeTab =  "Airline Classes";

    break; 
    
    default:

       $activeTab =  "prefixnstarting";

}
?>

<style>

    #airport_table > tbody > tr > td{

        vertical-align: middle;

    }

</style>

@endsection



@section('content')



<div class="page-title-area">

    <div class="row align-items-center">

        <div class="col-sm-6">

            <div class="breadcrumbs-area clearfix">

                <h5 class="page-title pull-left">Airline Settings</h5>

                <ul class="breadcrumbs pull-left" style="margin-top: 15px">

                    <li><a href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@index') }}">Airline Ticketing</a></li>

                    <li><span>Settings</span></li>

                </ul>

            </div>

        </div>

    </div>

</div>



<!-- Main content -->

<section class="content main-content-inner">

    <div class="settlement_tabs">

        <ul class="nav nav-tabs">

            <li class="<?php echo $activeTab === 'prefixnstarting' ? 'active' : ''; ?>">

                <a href="#prefixnstarting" data-toggle="tab">

                     <strong>Prefix & Starting No</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'airlines' ? 'active' : ''; ?>">

                <a href="#airlines" data-toggle="tab">

                    <strong>Air Lines</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'airlinesagents' ? 'active' : ''; ?>">

                <a href="#airlinesagents" data-toggle="tab">

                    <strong>AirLines Agents</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'linkedaccounts' ? 'active' : ''; ?>">

                <a href="#linkedaccounts" data-toggle="tab">

                    <strong>Linked Accounts</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'airports' ? 'active' : ''; ?>">

                <a href="#airports" data-toggle="tab">

                    <strong>Airports</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'additionalservice' ? 'active' : ''; ?>">

                <a href="#additionalservice" data-toggle="tab">

                    <strong>Additional Service</strong>

                </a>

            </li>

            <li class="<?php echo $activeTab === 'commissiontype' ? 'active' : ''; ?>">

                <a href="#commissiontype" data-toggle="tab">

                    <strong>Commission Types</strong>

                </a>

            </li>
            <li class="<?php echo $activeTab === 'passengertypes' ? 'active' : ''; ?>">

                <a href="#passengertypes" data-toggle="tab">

                    <strong>Passenger Types</strong>

                </a>

            </li>
            <li class="<?php echo $activeTab === 'contactsettings' ? 'active' : ''; ?>">

                <a href="#contactsettings" data-toggle="tab">

                    <strong>Contact Settings</strong>

                </a>

            </li>
            <li class="<?php echo $activeTab === 'airlineclasses' ? 'active' : ''; ?>">

                <a href="#airlineclasses" data-toggle="tab">

                    <strong>Airline Classes</strong>

                </a>

            </li>

        </ul>



        <div class="tab-content">

            <div class="tab-pane <?php echo $activeTab === 'prefixnstarting' ? 'active' : ''; ?>" id="prefixnstarting">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.prefix')

                    </div>

                </div>

            </div>

            <div class="tab-pane <?php echo $activeTab === 'airlines' ? 'active' : ''; ?>" id="airlines">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.airline')

                    </div>

                </div>

            </div>

            <div class="tab-pane <?php echo $activeTab === 'airlinesagents' ? 'active' : ''; ?>" id="airlinesagents">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.agent')

                    </div>

                </div>

            </div>

            <div class="tab-pane <?php echo $activeTab === 'linkedaccounts' ? 'active' : ''; ?>" id="linkedaccounts">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.linkedaccounts')

                    </div>

                </div>

            </div>

            <div class="pt--15 tab-pane <?php echo $activeTab === 'airports' ? 'active' : ''; ?>" id="airports">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.airports')

                    </div>

                </div>

            </div>

            <div class="tab-pane <?php echo $activeTab === 'additionalservice' ? 'active' : ''; ?>" id="additionalservice">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.additional_service')

                    </div>

                </div>

            </div>

             <div class="tab-pane <?php echo $activeTab === 'commissiontype' ? 'active' : ''; ?>" id="commissiontype">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.commision_type')

                    </div>

                </div>

            </div>

             <div class="tab-pane <?php echo $activeTab === 'passengertypes' ? 'active' : ''; ?>" id="passengertypes">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.passenger_type')

                    </div>

                </div>

            </div>
             <div class="tab-pane <?php echo $activeTab === 'contactsettings' ? 'active' : ''; ?>" id="contactsettings">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.contact_settings')

                    </div>

                </div>

            </div>

            <div class="tab-pane <?php echo $activeTab === 'airlineclasses' ? 'active' : ''; ?>" id="airlineclasses">

                <div class="row">

                    <div class="col-md-12">

                        @include('airline::airline_settings.partials.airline_classes')

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- /.content -->

<style>

  .nav-tabs-custom>.nav-tabs>li.active a{

    color:#3c8dbc;

  }

  .nav-tabs-custom>.nav-tabs>li.active a:hover{

    color:#3c8dbc;

  }

</style>

@endsection



@section('javascript')

@endsection