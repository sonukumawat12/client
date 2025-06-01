@section('css')
    <style>
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        .inline {
            display: inline;
        }
    </style>
@endsection

<div class="pos-tab-content">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="page-pretitle">
                        {{ __('Overview') }}
                    </div>
                    <h2 class="page-title">
                        {{ __('Ad Slot') }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-lg-6  mt-10">
                    <button type="button" onclick="addAdSlot()" class="btn btn-primary pull-right mt-10" aria-label="Left Align">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ __('Add Ad Slot') }}
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="table-responsive px-2 py-2">
                            <table class="table table-vcenter card-table" id="table-adslots">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Slot</th>
                                        <th>Slot No</th>
                                        <th>Page</th>
                                        <th>Width</th>
                                        <th>Height</th>
                                        <th class="w-1">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($adPageSlot) && $adPageSlot->count())
                                    @foreach ($adPageSlot as $ad)
                                    <tr>
                                        <td class="text-muted">
                                            {{ $ad->ad_page__slot_id }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->slot }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->slot_no}}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->ad_page_name }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->width }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->height }}
                                        </td>

                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                {{-- <a class="btn btn-primary btn-sm" href="{{ route('edit.ad', $ad->id)}}">{{ __('Edit') }}</a> --}}

                                                <a class="btn btn-primary btn-sm" href="{{route('delete.ad', ['ad_id'=>$ad->id]) }}">{{ __('Delete') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade " id="add-ads-slot-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row row-deck row-cards">
                        <div class="col-sm-12 col-lg-12">
                            <form method="post" class="card" id="add-ad-slot-form">
                                @csrf
                                <div class="card-header text-center">
                                    <h4 class="page-title">{{ __('New Ad Slot') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xl-10">
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="slot">Slot</label>
                                                            <input type="text" class="form-control" name="slot" placeholder="{{ __('Slot') }}" value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="slot">Slot Number</label>
                                                            <input type="text" class="form-control" id="slot_no" name="slot_no" placeholder="{{ __('Slot Number') }}" value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="ad_page_id">{{ __('Ad Page') }}</label>
                                                            <select name="ad_page_id" id="ad_page_id" class="form-control" required>
                                                                <option value='' disabled selected>{{ __('Ad Page') }}</option>
                                                                @foreach ($ad_pages as $ad_page)
                                                                    <option value="{{ $ad_page->id }}">{{ $ad_page->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="slot">Width</label>
                                                            <input type="text" class="form-control" id="width" name="width" placeholder="{{ __('Width') }}" value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="slot">Height</label>
                                                            <input type="text" class="form-control" id="height" name="height" placeholder="{{ __('Height') }}" value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xl-12 mt-10">
                                                        <div class="mb-3">
                                                            <button type="submit" class="btn btn-primary" aria-label="Left Align">
                                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ __('Add') }}
                                                            </button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('javascript')
    <script type="text/javascript">
        function addAdSlot() {
            "use strict";
            $("#add-ads-slot-modal").modal("show");
        }

        function deleteAd(parameter) {
            "use strict";
            $("#delete-modal").modal("show");
            var link = document.getElementById("deleted_ad_id");
            link.getAttribute("href");
            link.setAttribute("href", parameter);
        }

        $(document).ready(function() {
            $('#table-adslots').DataTable({
                //dom: 'Bfrtip',
                // buttons: [
                //     'copy', 'csv', 'excel', 'pdf', 'print'
                // ]
            });

        
            $('form#add-ad-slot-form').submit(function(event) {
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route("add.adslot") }}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.success == 1) {
                            swal({
                                text: response.message,
                                icon: "success",
                            });
                            location.reload();
                        } else {
                            swal({
                                text: response.message,
                                icon: "error",
                            });
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

                return false;
            });
        });
    </script>
    @endpush

</div>