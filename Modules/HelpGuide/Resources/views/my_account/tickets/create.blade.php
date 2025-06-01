@extends('helpguide::my_account.base', ['page' => 'create_ticket', 'pageTitle' => __('Create new ticket')])

@section('page-heading') 
@endsection

@section('content')   
<div class="container page-ticket-new">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-divider text-capitalize">
                    <h5>{{__('Create new ticket')}}</h5>
                </div>
                <div class="card-body">
                    <submit-ticket-form><div class="preloader"></div></submit-ticket-form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection