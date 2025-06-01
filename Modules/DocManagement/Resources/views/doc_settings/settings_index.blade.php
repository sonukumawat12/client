@extends('layouts.app')
@section('title', 'Doc Management Settings')

@section('css')

<?php 

$tab = isset($_GET['tab'])?$_GET['tab']:'documentcategory';

switch ($tab) {
    case "department":
        $activeTab =  "department";
        break;
    case "designation":
        $activeTab =  "designation";
        break;
    case "documenttype":
        $activeTab =  "documenttype";
        break;
    case "purpose":
        $activeTab =  "purpose";
        break;
    case "forwardedwith":
        $activeTab =  "forwardedwith";
        break;   
    case "mandatorysignatures":
        $activeTab =  "mandatorysignatures";
        break;   
    case "uploadsignature":
        $activeTab = "uploadsignature";   
        break;  
    case "uploadlogo":
        $activeTab =  "uploadlogo";   
        break;  
    default:
       $activeTab =  "documentcategory";
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
                <h5 class="page-title pull-left">Doc Management Settings</h5>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@index') }}">Doc Management </a></li>
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
            <li class="<?php echo $activeTab === 'documentcategory' ? 'active' : ''; ?>">
                <a href="#documentcategory" data-toggle="tab">
                     <strong>Document Category </strong>
                </a>
            </li>
             
            <li class="<?php echo $activeTab === 'department' ? 'active' : ''; ?>">
                <a href="#department" data-toggle="tab">
                    <strong>Department</strong>
                </a>
            </li>
            <li class="<?php echo $activeTab === 'designation' ? 'active' : ''; ?>">
                <a href="#designation" data-toggle="tab">
                    <strong>Designation </strong>
                </a>
            </li>
            <li class="<?php echo $activeTab === 'documenttype' ? 'active' : ''; ?>">
                <a href="#documenttype" data-toggle="tab">
                    <strong>Document Type </strong>
                </a>
            </li>
            <li class="<?php echo $activeTab === 'purpose' ? 'active' : ''; ?>">
                <a href="#purpose" data-toggle="tab">
                    <strong>Purpose </strong>
                </a>
            </li>
            <li class="<?php echo $activeTab === 'forwardedwith' ? 'active' : ''; ?>">
                <a href="#forwardedwith" data-toggle="tab">
                    <strong>Forwarded with </strong>
                </a>
            </li>
             <li class="<?php echo $activeTab === 'mandatorysignatures' ? 'active' : ''; ?>">
                <a href="#mandatorysignatures" data-toggle="tab">
                     <strong>Mandatory Signatures </strong>
                </a>
        </li>
         <li class="<?php echo $activeTab === 'uploadsignature' ? 'active' : ''; ?>">
                <a href="#uploadsignature" data-toggle="tab">
                    <strong>Upload Signature </strong>
                </a>
        </li>
        <li class="<?php echo $activeTab === 'uploadlogo' ? 'active' : ''; ?>">
                <a href="#uploadlogo" data-toggle="tab">
                    <strong>Upload Logo</strong>
                </a>
            </li>
        </ul>
       
        <div class="tab-content">
            <div class="tab-pane <?php echo $activeTab === 'documentcategory' ? 'active' : ''; ?>" id="documentcategory">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.documentcategory')
                    </div>
                </div>
            </div>
            <div class="tab-pane <?php echo $activeTab === 'department' ? 'active' : ''; ?>" id="department">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.department')
                    </div>
                </div>
            </div>
             
            
            <div class="tab-pane <?php echo $activeTab === 'designation' ? 'active' : ''; ?>" id="designation">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.designation')
                    </div>
                </div>
            </div>
            <div class="tab-pane <?php echo $activeTab === 'documenttype' ? 'active' : ''; ?>" id="documenttype">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.documenttype')
                    </div>
                </div>
            </div>
             <div class="tab-pane <?php echo $activeTab === 'purpose' ? 'active' : ''; ?>" id="purpose">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.purpose')
                    </div>
                </div>
            </div>
              <div class="tab-pane <?php echo $activeTab === 'forwardedwith' ? 'active' : ''; ?>" id="forwardedwith">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.forwardedwith')
                    </div>
                </div>
            </div>
              <div class="tab-pane <?php echo $activeTab === 'mandatorysignatures' ? 'active' : ''; ?>" id="mandatorysignatures">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.mandatorysignatures')
                    </div>
                </div>
            </div>
             <div class="tab-pane <?php echo $activeTab === 'uploadsignature' ? 'active' : ''; ?>" id="uploadsignature">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.uploadsignature')
                    </div>
                </div>
            </div>
              <div class="tab-pane <?php echo $activeTab === 'uploadlogo' ? 'active' : ''; ?>" id="uploadlogo">
                <div class="row">
                    <div class="col-md-12">
                        @include('docmanagement::doc_settings.partials.uploadlogo')
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