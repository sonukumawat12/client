<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'myhealth::patient.all_your_allergie' )])
    @slot('tool')
    <div class="box-tools">
        @if (session()->get('business.is_patient')) 
        <a class="btn btn-modal btn-primary" id='allergy_add_btn' data-container=".add_modal"
            data-href="{{action('\Modules\MyHealth\Http\Controllers\AllergiesController@create',['patient_code' => Auth::user()->username])}}">

            <i class="fa fa-plus"></i> @lang( 'myhealth::patient.add' )</a>
        @endif

    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="allergie_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang( 'myhealth::patient.date' )</th>
                    <th>@lang( 'myhealth::patient.allergie_&_notes' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade add_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
