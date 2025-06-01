<style>
    #prescription_show_table {
        overflow-y: scroll;
        display: block;
        height: 450px;
        width: 100%;
    }
</style>
<div class="modal-dialog" tabindex="1" role="document" style="width: 65%;">
    <div class="modal-content print">
        <div class="modal-header">
            <h5 class="modal-title">@lang('myhealth::patient.prescriptions')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="text-align:center; height: 550px">
            <div class="col-md-12" style="text-align:center; ">
                <div class="row">
                    <table class="table  table-striped" id="prescription_show_table" style="text-align: center">
                        <tr>
                            <th style="text-align:center; width:100%">@lang('myhealth::patient.date')</th>
                            <th style="text-align:center; ">@lang('myhealth::patient.action')</th>
                        </tr>
                        @foreach ($prescriptions as $item)
                        <tr>

                            <td>
                                {{ $item->date }}
                            </td>
                            <td>
                                <a href="#"
                                    data-href="{{ action('\Modules\MyHealth\Http\Controllers\PrescriptionController@imageModal', ['title' => 'Prescription', 'url' => url($item->prescription_file)]) }}"
                                    class="btn-modal btn btn-primary"
                                    data-container=".view_modal">@lang("messages.view")</a>
                            </td>
                        </tr>

                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>