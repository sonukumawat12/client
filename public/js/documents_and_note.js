function getDocAndNoteIndexPage() {
    var notable_type = $('#notable_type').val();
    var notable_id = $('#notable_id').val();
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: '/get-document-note-page',
        async: false,
        data: { notable_type: notable_type, notable_id: notable_id },
        success: function (result) {
            $('.document_note_body').html(result);
        },
    });
}
$(document).on('click', '.docs_and_notes_btn', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (result) {
            $('.docus_note_modal').html(result).modal('show');
        },
    });
});
function initialize_dropzone_for_docus_n_notes() {
    var file_names = [];
    if (dropzoneForDocsAndNotes.length > 0) {
        Dropzone.forElement('div#docusUpload').destroy();
    }
    dropzoneForDocsAndNotes = $('div#docusUpload').dropzone({
        url: '/post-document-upload',
        paramName: 'file',
        uploadMultiple: true,
        autoProcessQueue: true,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (file, response) {
            if (response.success) {
                toastr.success(response.msg);
                file_names.push(response.file_name);
                $('input#docus_notes_media').val(file_names);
            } else {
                toastr.error(response.msg);
            }
        },
    });
}
// Commented since it's already implemented on app.js, and causes duplicated entries on Member Module / List member/Action/Document & Note
// $(document).on('submit', 'form#docus_notes_form', function (e) {
//     e.preventDefault();
//     var url = $('form#docus_notes_form').attr('action');
//     var method = $('form#docus_notes_form').attr('method');
//     var data = $('form#docus_notes_form').serialize();
//     $.ajax({
//         method: method,
//         dataType: 'json',
//         url: url,
//         data: data,
//         success: function (result) {
//             if (result.success) {
//                 $('.docus_note_modal').modal('hide');
//                 toastr.success(result.msg);
//                 documents_and_notes_data_table.ajax.reload();
//             } else {
//                 toastr.error(result.msg);
//             }
//         },
//     });
// });
$(document).on('click', '#delete_docus_note', function (e) {
    e.preventDefault();
    var url = $(this).data('href');
    swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(
        (confirmed) => {
            if (confirmed) {
                $.ajax({
                    method: 'DELETE',
                    dataType: 'json',
                    url: url,
                    success: function (result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            documents_and_notes_data_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        }
    );
});
$(document).on('click', '.view_a_docs_note', function () {
    var url = $(this).data('href');
    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (result) {
            $('.view_modal').html(result).modal('show');
        },
    });
});
function initializeDocumentAndNoteDataTable() {
    documents_and_notes_data_table = $('#documents_and_notes_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/note-documents',
            data: function (d) {
                d.notable_id = $('#notable_id').val();
                d.notable_type = $('#notable_type').val();
            },
        },
        columnDefs: [{ targets: [0, 2, 4], orderable: false, searchable: false }],
        aaSorting: [[3, 'asc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'heading', name: 'heading' },
            { data: 'createdBy' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
        ],
    });
}
