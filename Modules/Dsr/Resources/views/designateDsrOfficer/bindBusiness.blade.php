
<style>
    .select2-container {
        display: inherit !Important;
    }
</style>
<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title ">@lang('dsr::lang.bind_to_business') <b>{{ $dsr_officer->officer_name }}</h4>
        </div>
        <div class="modal-body">
            <div class="container">
                <form method="POST" action="{{ action('\Modules\Dsr\Http\Controllers\DesignatedDsrController@bindBusiness', ['id' => $dsr_officer->id]) }}">
                    @csrf
                    <div class="form-group">
                        <label for="businesses">Businesses:</label>
                        @foreach($allBusinesses as $id => $name)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $id }}" id="business_{{ $id }}" name="businesses[]" {{ in_array($id, $dsr_officer->businesses ?? []) ? 'checked' : '' }}>                                <label class="form-check-label" for="business_{{ $id }}">
                                    {{ $name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary" id="update">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

    });
</script>
