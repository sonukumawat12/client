<script type="text/javascript">

    $(document).ready( function() {
  //Repair Device Model Code
        // model_datatable = $("#category_table").DataTable({
        //             processing: true,
        //             serverSide: true,
        //             ajax: {
        //                 ajax: '/taxonomies?type=' + 'auto-device',
        //                 data:function(d) {
        //                     console.log(d);
        //                     //d.brand_id = $("#brand_id").val();
        //                     //d.device_id = $("#device_id").val();
        //                 }
        //             },
        //             columnDefs: [
        //                 {
        //                     targets: [0, 2],
        //                     orderable: false,
        //                     searchable: false,
        //                 },
        //             ],
        //             aaSorting: [[1, 'desc']],
        //             columns: [
        //                 { data: 'action', name: 'action' },
        //                 { data: 'name', name: 'name' },
        //                 { data: 'repair_checklist', name: 'repair_checklist' },
        //                 { data: 'device_id', name: 'device_id' },
        //                 { data: 'brand_id', name: 'brand_id' },
        //             ]
        //     });

        $(document).on('change', "#brand_id, #device_id", function(){
            model_datatable.ajax.reload();
        });
		
        function getTaxonomiesIndexPage () {
            var data = {category_type : $('#category_type').val()};
            $.ajax({
                method: "GET",
                dataType: "html",
                url: '/taxonomies-ajax-index-page',
                data: data,
                async: false,
                success: function(result){
                    console.log(result);
                    $('.taxonomy_body').html(result);
                }
            });
        }

        function initializeTaxonomyDataTable() {
            //Category table
            if ($('#taxonomy_category_table').length) {
                var category_type = $('#category_type').val();
                
                
                category_table = $('#taxonomy_category_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/taxonomies?type=' + category_type,
                    columns: [
                        { data: 'name', name: 'name' },
                        @if($cat_code_enabled)
                            { data: 'short_code', name: 'short_code' },
                        @endif
                        
                        { data: 'description', name: 'description' },
                        { data: 'username', name: 'username' },
                        
                        { data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                });
            }
        }

        @if(empty(request()->get('type')))
            getTaxonomiesIndexPage();
        @endif

        initializeTaxonomyDataTable();
    });
  
// 	$('#category_add_form').submit(function(e){
//         e.preventDefault();
//         var form = $(this);
//         var data = form.serialize();

//         $.ajax({
//             method: 'POST',
//             url: $(this).attr('action'),
//             dataType: 'json',
//             data: data,
//             beforeSend: function(xhr) {
//                 __disable_submit_button(form.find('button[type="submit"]'));
//             },
//             success: function(result) {
//                 if (result.success === true) {
//                     $('div.category_modal').modal('hide');
//                     toastr.success(result.msg);
//                     category_table.ajax.reload();
//                 } else {
//                     toastr.error(result.msg);
//                 }
//             },
//         });
		
// 		return false;
//     });
	
	
//     $(document).on('click', 'button.edit_category_button', function() {
//         $('div.category_modal').load('https://vimi10.monster/246/taxonomies/60/edit', function() {
//             $(this).modal('show');

//             $('form#category_edit_form').submit(function(e) {
//                 e.preventDefault();
//                 var form = $(this);
//                 var data = form.serialize();

//                 $.ajax({
//                     method: 'POST',
//                     url: $(this).attr('action'),
//                     dataType: 'json',
//                     data: data,
//                     beforeSend: function(xhr) {
//                         __disable_submit_button(form.find('button[type="submit"]'));
//                     },
//                     success: function(result) {
//                         if (result.success === true) {
//                             $('div.category_modal').modal('hide');
//                             toastr.success(result.msg);
//                             category_table.ajax.reload();
//                         } else {
//                             toastr.error(result.msg);
//                         }
//                     },
//                 });
//             });
//         });
//     });

//     $(document).on('click', 'button.delete_category_button', function() {
//         swal({
//             title: LANG.sure,
//             icon: 'warning',
//             buttons: true,
//             dangerMode: true,
//         }).then(willDelete => {
//             if (willDelete) {
//                 var href = $(this).data('href');
//                 var data = $(this).serialize();

//                 $.ajax({
//                     method: 'DELETE',
//                     url: href,
//                     dataType: 'json',
//                     data: data,
//                     success: function(result) {
//                         if (result.success === true) {
//                             toastr.success(result.msg);
//                             category_table.ajax.reload();
//                         } else {
//                             toastr.error(result.msg);
//                         }
//                     },
//                 });
//             }
//         });
//     });
</script>