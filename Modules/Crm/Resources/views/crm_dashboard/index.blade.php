@php
    $settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
    $file = base_path($settings->uploadFileFicon);

    $settings_icon = DB::table('settings')->where('id', 1)->select('*')->first();
    $file_url = '';
    $site_settings = \App\SiteSettings::where('id', 1)->first();
    if (!empty($settings_icon) && file_exists(public_path('public' . $settings_icon->favicon))) {
        $file_url = asset('public/' . $settings_icon->favicon);
    } elseif (!empty($site_settings) && !empty($site_settings->uploadFileFicon)) {
        if (file_exists(public_path($site_settings->uploadFileFicon))) {
            $file_url = url($site_settings->uploadFileFicon);
        } else {
            $file_url = asset('img/setting/icon-1730547010.png');
        }
    }
@endphp
@if (!empty($file_url))
<link rel="shortcut icon" type="image/png" href="{{ $file_url }}" />
@endif
@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<section class="no-print">
    <style>
    .export-buttons .btn {
    margin: 0 5px;  /* Add some space between the buttons */
    font-size: 14px;
    padding: 8px 16px;  /* Adjust the button size for better visibility */
    text-transform: uppercase; /* Optional: makes the text all caps */
    border-radius: 4px;
}

.export-buttons .btn:hover {
    opacity: 0.9;  /* Hover effect for better interactivity */
}

.export-buttons .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.export-buttons .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.export-buttons .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Add some margin around the buttons */
.export-buttons {
    display: flex;
    justify-content: flex-end;  /* Align buttons to the right */
    gap: 10px;
    padding-top: 10px;
}

        /* Customizing dropdown icon */
        #contacts_login_dropdown::after {
            display: inline-block;
            width: 0;
            height: 0;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }

        /* Horizontal navbar layout */
        .navbar-nav_up {
            display: flex;
            flex-direction: row;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .nav-item_up {
            list-style: none;
        }

        .nav-link_up {
            text-decoration: none;
        }

        /* Ensure navbar collapse works for small screens */
        .navbar-collapse {
            flex-grow: 1;
        }

        /* Table styling */
        .table-responsive {
            margin-top: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: #fff;
        }

        /* Hover effect for rows */
        tr:hover {
            background-color: #f9f9f9;
        }

        /* Alternate row colors */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Add spacing between columns */
        .table th, .table td {
            padding: 16px;
        }

        /* Table container styling */
        .table-container {
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Table footer */
        .table-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .info-box {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-box h5 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        .info-box p {
            margin: 0;
            font-size: 20px;
            color: #007bff;
            font-weight: bold;
        }

        .info-box i {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .info-box {
    position: relative;
    padding-left: 40px; /* Add space for the icon */
    background-color: #f4f4f4; /* Optional background color */
    padding: 20px;
    border-radius: 8px;
}

.info-box:before {
    content: "";
    position: absolute;
    left: 10px; /* Position the image on the left side */
    top: 50%;
    transform: translateY(-50%);
    width: 30px; /* Set the size of the icon */
    height: 30px;
    background-image: url('path/to/your-image.png'); /* Replace with your image path */
    background-size: contain;
    background-repeat: no-repeat;
}

    </style>
    
<div class="container">
<nav class="navbar navbar-expand-lg navbar-light bg-light tw-transition-all tw-duration-5000 tw-shrink-0 tw-rounded-2xl tw-m-2 tw-border-2 !tw-bg-white">
        <div class="container-fluid">
            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="{{ url('crm/dashboard') }}">
                    <i class="fas fa-broadcast-tower"></i> CRM
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav navbar-nav_up">
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/leads?lead_view=list_view') }}">Leads</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/follow-ups') }}">Follow ups</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/campaigns') }}">Campaigns</a></li>
                    <li class="nav-item nav-item_up dropdown">
                        <a class="nav-link nav-link_up dropdown-toggle" href="#" id="contacts_login_dropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Contacts Login
                        </a>
                        <div class="dropdown-menu" aria-labelledby="contacts_login_dropdown">
                            <a class="dropdown-item" href="{{ url('crm/all-contacts-login') }}">Contacts Login</a>
                            <a class="dropdown-item" href="{{ url('crm/commissions') }}">Commissions</a>
                        </div>
                    </li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/reports') }}">Reports</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/proposal-template') }}">Proposal template</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/proposals') }}">Proposals</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('taxonomies?type=source') }}">Sources</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('taxonomies?type=life_stage') }}">Life Stage</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('taxonomies?type=followup_category') }}">Followup Category</a></li>
                    <li class="nav-item nav-item_up"><a class="nav-link nav-link_up" href="{{ url('crm/settings') }}">Settings</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
</section>
 <hr class="my-4">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-3">
    <div class="info-box">
        <h5>Today's Follow-ups</h5>
        <p>0</p>
    </div>
</div>

            <div class="col-md-3">
                <div class="info-box">
                   
                    <h5>My Leads</h5>
                    <p>0</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                   
                    <h5>My Leads to Customer Conversion</h5>
                    <p>0</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <h5>My Follow-ups</h5>
                    <ul class="list-group">
                        <li class="list-group-item">Scheduled <span class="badge bg-primary float-end">0</span></li>
                        <li class="list-group-item">Open <span class="badge bg-primary float-end">0</span></li>
                        <li class="list-group-item">Cancelled <span class="badge bg-primary float-end">0</span></li>
                        <li class="list-group-item">Completed <span class="badge bg-primary float-end">0</span></li>
                    </ul>
                </div>
            </div>
        </div>
 <hr class="my-4">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box">
                   
                    <h5>Customers</h5>
                    <p>1</p>
                </div>
                 <!--<div class="col-md-6">-->
                <h5>Sources</h5>
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Total</th>
                                <th>Conversion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Telephone</td>
                                <td>0</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>Mail</td>
                                <td>0</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>Friend</td>
                                <td>0</td>
                                <td>0%</td>
                            </tr>
                            <tr>
                                <td>ABC Source</td>
                                <td>0</td>
                                <td>0%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <!--</div>-->
            </div>
            <div class="col-md-3">
                <div class="info-box">
                 
                    <h5>Leads</h5>
                    <p>0</p>
                    
                </div>
                <h5>Life Stages</h5>
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Life Stage</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2">No data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                
                    <h5>Sources</h5>
                    <p>4</p>
                </div>
                <!--<div class="col-md-6">-->
                <!--<h5>Life Stages</h5>-->
                <!--<div class="table-container">-->
                <!--    <table class="table table-striped">-->
                <!--        <thead>-->
                <!--            <tr>-->
                <!--                <th>Life Stage</th>-->
                <!--                <th>Total</th>-->
                <!--            </tr>-->
                <!--        </thead>-->
                <!--        <tbody>-->
                <!--            <tr>-->
                <!--                <td colspan="2">No data</td>-->
                <!--            </tr>-->
                <!--        </tbody>-->
                <!--    </table>-->
                <!--</div>-->
            <!--</div>-->
            </div>
             <div class="col-md-3">
    <div class="info-box p-3">
        <div class="d-flex align-items-center">
          
            <h5 class="mb-0">Birthdays</h5>
        </div>
        <hr>
        <div class="today-birthdays mb-3">
            <h6>Today's Birthdays</h6>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic Data -->
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="upcoming-birthdays mb-3">
            <h6>Upcoming Birthdays</h6>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Birthday On</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic Data -->
                    <tr>
                        <td>1</td>
                        <td>Adam Brown</td>
                        <td>27 Dec</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Emily Clark</td>
                        <td>29 Dec</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button class="btn btn-success w-100">Send Wishes</button>
    </div>
</div>

        </div>

     
    </div>
</section>

<hr class="my-4">
<section class="content no-print">
  <div class="row mb-12">
            <div class="col-md-12">
                <div class="info-box">
        	@component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.follow_ups_by_user')])
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('follow_up_user_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('follow_up_user_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="follow_ups_by_user_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('role.user')</th>
                           <!--<th>@lang('crm::lang.scheduled') ppp</th>-->
                           <!-- <th>@lang('crm::lang.scheduled')</th>-->
                           <!-- <th>@lang('crm::lang.cancelled')</th>-->
                           <!-- <th>@lang('crm::lang.completed')</th>-->
                           <!-- <th>@lang('crm::lang.none')</th>-->
                           <!-- <th>@lang('lang_v1.others')</th>-->
                           <!-- <th>@lang('crm::lang.total_follow_ups')</th>-->
                            @foreach($statuses as $key => $value)
                                <th>
                                    {{$value}}  
                                </th>
                            @endforeach
                            <th>
                                @lang('lang_v1.others')
                            </th>
                            <th>
                                @lang('crm::lang.total_follow_ups') 
                            </th>
                        </tr>
                    </thead>
                </table>
            @endcomponent
        </div>
    </div>
    </div>
<section/>
<hr class="my-4">
<section class="content no-print">
  <div class="row mb-12">
            <div class="col-md-12">
                <div class="info-box">
        	@component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.lead_to_customer_conversion')])
              <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('follow_up_user_date_range1', __('report.date_range') . ':') !!}
                            {!! Form::text('follow_up_user_date_range1', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="lead_to_customer_conversion" style="width: 100%;">
                    <thead>
                        <tr>
                            <!--<th>@lang('role.user')</th>-->
                           <!--<th>@lang('crm::lang.scheduled')</th>-->
                            
                            @foreach($statuses as $key => $value)
                                <th>
                                    {{$value}}  
                                </th>
                            @endforeach
                            <th>
                                @lang('lang_v1.others')
                            </th>
                            <th>
                                @lang('crm::lang.total_follow_ups') 
                            </th>
                        </tr>
                    </thead>
                </table>
            @endcomponent
        </div>
    </div>
      </div>
    
    
    
    
</section>
@endsection
<hr class="my-4">

@section('javascript')
    @include('crm::reports.report_javascripts')
@endsection

