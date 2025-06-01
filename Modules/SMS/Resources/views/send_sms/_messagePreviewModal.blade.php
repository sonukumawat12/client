<div class="modal fade " id="messagePreview" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('sms::lang.overview') }}</h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('sms::lang.parts') }}</th>
                                <th scope="col">{{ __('sms::lang.length') }}</th>
                                <th scope="col">{{ __('sms::lang.recipients') }}</th>
                                <th scope="col">{{ __('sms::lang.message') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th id="msgCost"></th>
                                <td id="msgLength"></td>
                                <td id="msgRecepients" data-loading-text="<i data-feather='loader'></i>"></td>
                                <td id="msg"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button id="closeMessagePreview" type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('sms::lang.close') }}</button>
                <button type="button" class="btn btn-primary" id="finalSend" data-loading-text="<i data-feather='loader'></i> {{ __('sms::lang.please_wait') }}">{{ __('sms::lang.proceed') }}</button>
            </div>
             {!! Form::close() !!}
        </div>
    </div>
</div>
