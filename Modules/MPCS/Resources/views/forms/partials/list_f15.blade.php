<!-- Main content -->
<section class="content">
    <!-- date and time Ref form number added on table -->
<div class="card">
        <div class="card-body">
            <table width="50%">
                <tbody>
                    <tr>
                    <td width="12%"><h4>Date & Time:</h4></td>
                    <td width="18%" style="text-align: left;"><h4><strong>{{ date('Y-m-d H:i:s') }}</strong></h4></td>
                    <td width="4%"></td>
                    <td width="23%"><h4>Ref Previous Form Number:</h4></td>
                    <td width="8%"><h4><strong>{{ !empty($headers->id) ? $headers->id : 'N/A' }}
                    </strong></h4></td>
                    <td width="8%"></td>
                    <td width="15%"><h4>User Added:</h4></td>
                    <td width="20%"><h4><strong>{{ auth()->user()->name }} {{ auth()->user()->username}}</strong></h4></td>
                </tr>
            </tbody></table>
        </div>
    </div>
<!-- end date and time Ref form -->
    <div class="row">
        <div class="box-tools pull-right" style="margin: 14px 20px 14px 0;">
            @php
                $header = $row->created_by ?? 0;
            @endphp
            <button type="button" class="btn btn-primary btn-modal  @if (auth()->user()->can('superadmin') || auth()->user()->id === $header) left @endif"
                data-href="{{ action('\Modules\MPCS\Http\Controllers\F15FormController@get15FormSetting') }}"
                data-container="">
                <i class="fa fa-plus"></i> @lang('mpcs::lang.add_15_form_settings')</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            
            @component('components.widget', ['class' => 'box-primary'])
                <div class="col-md-12">
                    <div class="box-body" style="margin-top: 20px;">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="msg"></div>
                                <div class="table-responsive">
                                    <table id="form_15_settings_table" class="table table-striped table-bordered"
                                        width="100%">
                                        <thead>
                                            <tr>
                                                <!--<th>No.</th>-->
                                                <th>Actions</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <!-- Modal for adding form settings -->
    <div class="modal fade form_15_settings_modal" id="form_15_settings_modal" tabindex="-1" role="dialog"
        aria-labelledby="gridSystemModalLabel"></div>
    <!-- Modal for updating form settings -->
    <div class="modal fade update_form_15_settings_modal" id="update_form_15_settings_modal" tabindex="-1"
        role="dialog" aria-labelledby="gridSystemModalLabel"></div>

</section>


<!-- JavaScript -->
<script type="text/javascript">
    $(document).ready(function() {
        const form15Labels = {
            2: 'Ref Previous Form Number',
            3: 'Store Purchases Amount Up to Previous Day',
            4: 'Total (No 17) Up to Previous Day',
            5: 'Opening Stock Up to Previous Day',
            6: 'Grand Total Up to Previous Day',
            7: 'Cash Sales Up to Previous Day',
            8: 'Card Sales Up to Previous Day',
            9: 'Credit Sales Up to Previous Day',
            10: 'Total (No 31) Up to Previous Day',
            11: 'Balance Stock in Sale Price Up to Previous Day',
            12: 'Grand Total Again'
        };

        function getForm15Label(form15_label_id) {
            return form15Labels[form15_label_id] || null; // Mengembalikan null jika label tidak ditemukan
        }

        // Ambil nomor Form Number dari f15_form_id
        const startingNumberLabel =
            @if (auth()->user()->can('user'))
                'Form Number : - ';
            @else
                'Form Number : ' + '{{ !empty($headers->id) ? $headers->id : 'N/A' }}';
            @endif

        var form_15_settings_table = $('#form_15_settings_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action('\Modules\MPCS\Http\Controllers\F15FormController@mpcs15FormSettings') }}",
                type: 'GET',
                dataSrc: function(json) {
                    // Filter data untuk menghilangkan baris yang tidak memiliki label
                    return json.data.filter(function(row) {
                        return form15Labels[row.form15_label_id] !== undefined;
                    });
                },
                error: function(xhr, error, thrown) {
                    console.log('Ajax error:', xhr.status, error, thrown);
                    alert('Error loading data: ' + thrown);
                }
            },
            columns: [
                // { 
                //     data: null, 
                //     name: 'number', 
                //     render: function(data, type, row, meta) {
                //         return meta.row + meta.settings._iDisplayStart + 1;
                //     }, 
                //     className: 'text-center', 
                //     width: '5%'
                // },
                {
                    data: 'action',
                    name: 'action',
                    render: function(data) {
                        return data;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    width: '10%'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        return new Date(data)
                    .toLocaleDateString(); // Mengubah data menjadi format tanggal
                    },
                    className: 'text-left',
                    width: '10%'
                },
                {
                    data: 'form15_label_id',
                    name: 'form15_label_id',
                    render: function(data) {
                        return form15Labels[data];
                    },
                    className: 'text-left',
                    width: '60%'
                },
                {
                    data: 'rupees',
                    name: 'rupees',
                    render: function(data) {
                        return parseFloat(data).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    },
                    className: 'text-center',
                    width: '15%'
                }
            ],
            columnDefs: [{
                targets: '_all',
                className: 'text-left'
            }],
            paging: false,
            lengthChange: false
        });
        // Menampilkan F 15 Form Starting Number di atas tabel
        $('#form_15_settings_table').before('<div class="form-group" style="display:none;"><label>' + startingNumberLabel +
            '</label></div>');


        // Submit Update Form 15 Settings
        $(document).on('submit', 'form#update_15_form_settings', function(e) {
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success == true) {
                        toastr.success(result.msg);
                        form_15_settings_table.ajax.reload();
                        $('#update_form_15_settings_modal').modal('hide');
                    } else {
                        toastr.error(result.msg);
                    }
                    $('button[type="submit"]').attr('disabled', false);
                },
                error: function(xhr, status, error) {
                    toastr.error(error);
                    $('button[type="submit"]').attr('disabled', false);
                }
            });
        });
    });

    // Function to delete Form Setting
    function deleteFormSetting(button) {
        var url = button.getAttribute('data-href');

        if (confirm("Are you sure you want to delete this setting?")) {
            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.msg); // Success message
                        button.closest('tr').remove(); // Remove the row from table after success
                    } else {
                        toastr.error(data.msg); // Error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error("Something went wrong.");
                });
        }
    }
</script>
