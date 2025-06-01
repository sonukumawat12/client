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
                        {{ __('Ad') }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-lg-6  mt-10">
                    {{-- <a type="button" href="#" onclick="addAd()" class="btn btn-primary pull-right  mt-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        {{ __('Add Ad') }}
                    </a> --}}
                    <button type="button" onclick="addAd()" class="btn btn-primary pull-right mt-10" aria-label="Left Align">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ __('Add Ad') }}
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
                            <table class="table table-vcenter card-table" id="table-ad">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Ad Page') }}</th>
                                        <th>{{ __('Ad Slot No') }}</th>
                                        <th>{{ __('Client Name') }}</th>
                                        <th>{{ __('Image Code') }}</th>
                                        <th>{{ __('Start Date') }}</th>
                                        <th>{{ __('End Date') }}</th>
                                        <th>{{ __('Amount') }}</th>
                                        <th class="w-1">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($ads) && $ads->count())
                                    @foreach ($ads as $ad)
                                    <tr>
                                        <td class="text-muted">
                                            {{ date('d-m-Y', strtotime($ad->created_at)) }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->ad_page_name }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->ad_page_slot_name}}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->client_name }}
                                        </td>
                                        <td class="text-muted">
                                            {{ $ad->code }}
                                        </td>
                                        <td class="text-muted">
                                            {{ date('d-m-Y', strtotime($ad->start_date)) }}
                                        </td>
                                        <td class="text-muted">
                                            {{ date('d-m-Y', strtotime($ad->end_date)) }}
                                        </td>

                                        <td class="text-muted">
                                            {{-- $currencies->currency --}}{{ $ad->amount }}
                                        </td>

                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a class="btn btn-primary btn-sm" href="{{ route('edit.ad', $ad->ad_id)}}">{{ __('Edit') }}</a>

                                                <a class="btn btn-primary btn-sm" href="{{route('delete.ad', ['ad_id'=>$ad->ad_id]) }}">{{ __('Delete') }}</a>
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


    <div class="modal fade " id="add-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row row-deck row-cards">
                        <div class="col-sm-12 col-lg-12">
                            <form method="post" class="card" id="add-ad-form" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header text-center">
                                    <h4 class="page-title">{{ __('New Ad') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-xl-10">
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('Date') }}</label>
                                                            <input type="date" class="form-control" name="create_date" placeholder="{{ __('Date') }}..." value="" required>
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
                                                            <label class="form-label" for="ad_page_slot_id">{{ __('Ad Slot') }}</label>
                                                            <select name="ad_page_slot_id" id="ad_page_slot_id" class="form-control" required>
                                                                <option value='' disabled selected>{{ __('Ad Slot') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('Image') }} <span id="recommend-size" class="text-danger"></span></label>
                                                            <input type="file" class="form-control" name="content" placeholder="{{ __('Image') }}..." accept=".jpeg,.jpg,.png,.gif,.svg" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required" for="storage_disk">{{ __('storage_disk') }}</label>
                                                            <select class="form-control" name="storage_disk" id="storage_disk">
                                                                <option value="s3">S3</option>
                                                                <option value="local_server">Local Server</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('Start Date') }}</label>
                                                            <input type="date" class="form-control" name="start_date" placeholder="{{ __('Start Date') }}..." value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('End Date') }}</label>
                                                            <input type="date" class="form-control" name="end_date" placeholder="{{ __('End Date') }}..." value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('Client Name') }} </label>
                                                            <input type="text" class="form-control" name="client_name" placeholder="{{ __('Client Name') }}..." value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label required">{{ __('Amount') }}</label>
                                                            <input type="number" class="form-control" name="amount" min="0" required placeholder="{{ __('Amount') }}...">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('Link') }}</label>
                                                            <input type="url" class="form-control" name="link" min="0" placeholder="{{ __('Link') }}...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-6">
                                                        <div class="mb-3 checkbox">
                                                            <input class="form-check-input input-icheck" type="checkbox" id="status" name="status">
                                                            <label class="inline" for="status">{{ __('Active') }}</label>
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


    @section('javascript')
    <script type="text/javascript">
        function addAd(parameter) {
            "use strict";
            $("#add-modal").modal("show");
        }

        function deleteAd(parameter) {
            "use strict";
            $("#delete-modal").modal("show");
            var link = document.getElementById("deleted_ad_id");
            link.getAttribute("href");
            link.setAttribute("href", parameter);
        }

        $(document).ready(function() {
            $('#table-ad').DataTable({
                //dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $("#ad_page_id").change(function() {
                var ad_page_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('ad.get-ad-slots-data') }}",
                    dataType: "json",
                    data: {
                        ad_page_id: ad_page_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(result) {

                        if (result.success == 1) {
                            $('#recommend-size').text('');
                            $('#ad_page_slot_id').empty();
                            $('#ad_page_slot_id').append(`
                            <option value=""></option>
                        `);
                            result.data.map(function(v, i) {
                                $('#ad_page_slot_id').append(`
                            <option data-width="${v.width}" data-height="${v.height}" value="${v.id}">${v.slot}</option>
                        `);
                            });
                        }
                    }
                });
            });

            $("#ad_page_slot_id").change(function() {
                var ad_page_slot_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "{{ route('ad.get-ad-slots-data') }}",
                    dataType: "json",
                    data: {
                        ad_page_slot_id: ad_page_slot_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(result) {

                        if (result.success == 1 && result.data != null) {
                            $('#recommend-size').text('(' + result.data.width + ' x ' + result.data.height + ')');
                        }
                    }
                });
            });

            $('form#add-ad-form').submit(function(event) {
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '{{ route("add.ad") }}',
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
    @endsection

</div>