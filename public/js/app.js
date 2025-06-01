$(document).ready(function() {
  $.fn.modal.Constructor.prototype.enforceFocus = function() {};
  $('body').on('click', 'label', function(e) {

      var field_id = $(this).attr('for');

      if (field_id) {

          if ($('#' + field_id).hasClass('select2')) {

              $('#' + field_id).select2('open');

              return false;

          }

      }

  });

  fileinput_setting = {

      showUpload: false,

      showPreview: false,

      browseLabel: LANG.file_browse_label,

      removeLabel: LANG.remove,

  };
function get_cheques_list(payment_type = null){
    //if($('#transaction_date_range_cheque_deposit').val()){
      
       
        start_date =  $('input#transaction_date_range_cheque_deposit').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date =  $('input#transaction_date_range_cheque_deposit').data('daterangepicker').endDate.format('YYYY-MM-DD');
      let contact_id = $('#supplier_id').val(); // Get selected contact/supplier
      let expense_category_id =$('#expense_category_id').val();
      console.log("contact",contact_id);
        $.ajax({
            method: 'get',
            url: '/accounting-module/cheque-list',
            data: { start_date, end_date,payment_type,contact_id,expense_category_id },
            contentType: 'html',
            success: function(result) {
              
                $('#cheque_list_table tbody').empty().append(result);
            },
        });
   // }
   
}
  $(document).ajaxStart(function() {

      Pace.restart();

  });

  __select2($('.select2'));

  $('body').on('click', '[data-toggle="popover"]', function() {

      if ($(this).hasClass('popover-default')) {

          return false;

      }

      $(this).popover('show');

  });

  $('body').on('click', '.details_popover', function() {

      if ($(this).hasClass('popover-default')) {

          return false;

      }

      $(this).popover('show');

  });

  $('button#btnKeyboard').hover(function() {

      $(this).tooltip('show');

  });

  $(document).ready(function() {

      $('#btnKeyboard').popover();

  });

  $('.start-date-picker').datepicker({ autoclose: true, endDate: 'today' });

  $(document).on('click', '.btn-modal', function(e) {

      e.preventDefault();

      var container = $(this).data('container');
      
      $(container).empty();

      $.ajax({

          url: $(this).data('href'),

          dataType: 'html',

          success: function(result) {
              // var contact = $('#default_contact_id').val();
              $(container).html(result).modal('show');
              // $(container).find('input#contact_id').val(contact);
          },
          error: function(xhr) {
            var errorMessage = "";
            if (xhr.responseJSON && xhr.responseJSON.msg) {
                errorMessage = xhr.responseJSON.msg;
            } else if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    errorMessage = response.msg || "Something went wrong.";
                } catch (e) {
                    errorMessage = "Something went wrong.";
                }
            } else {
                errorMessage = "Something went wrong.";
            }
            toastr.error(errorMessage);
          }

      });

  });


  // brands

  $(document).on('submit', 'form#brand_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.brands_modal').modal('hide');

                  toastr.success(result.msg);

                  brands_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  var brands_table = $('#brands_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/brands',

      columns: [
          { data: 'name', name: 'name' },
          { data: 'description', name: 'description' },
          { data: 'action', name: 'action' },
      ],

  });

  $(document).on('click', 'button.edit_brand_button', function() {

      $('div.brands_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#brand_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.brands_modal').modal('hide');

                          toastr.success(result.msg);

                          brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_brand_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_brand,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
  
  $(document).on('click', '.regenerate-vat', function() {

      swal({

          title: LANG.sure,

          text: LANG.sure,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              $.ajax({

                  method: 'GET',

                  url: href,

                  dataType: 'json',

                  data: {},

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
  
  
  
  // auto repair brands
  
  $(document).on('submit', 'form#autorepair_brand_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.autorepair_brands_modal').modal('hide');

                  toastr.success(result.msg);

                  autorepair_brands_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  var autorepair_brands_table = $('#autorepair_brands_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/autorepairservices/brands',

      columns: [
          { data: 'name', name: 'name' },
          { data: 'description', name: 'description' },
          { data: 'action', name: 'action' },
      ],

  });

  $(document).on('click', 'button.autorepair_edit_brand_button', function() {

      $('div.autorepair_brands_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#autorepair_brand_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.autorepair_brands_modal').modal('hide');

                          toastr.success(result.msg);

                          autorepair_brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.autorepair_delete_brand_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_brand,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          autorepair_brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
  
  
  
  // Repair brands
  $(document).on('submit', 'form#repair_brand_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.repair_brands_modal').modal('hide');

                  toastr.success(result.msg);

                  repair_brands_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  var repair_brands_table = $('#repair_brands_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/repair/brands',

      columns: [
          { data: 'name', name: 'name' },
          { data: 'description', name: 'description' },
          { data: 'action', name: 'action' },
      ],

  });

  $(document).on('click', 'button.repair_edit_brand_button', function() {

      $('div.repair_brands_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#repair_brand_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.repair_brands_modal').modal('hide');

                          toastr.success(result.msg);

                          repair_brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.repair_delete_brand_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_brand,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          repair_brands_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
  
  
  

  var tax_rates_table = $('#tax_rates_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/tax-rates',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });
  
   var tax_rates_table = $('#default_tax_rates_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/superadmin/tax-rate',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });
  

  $(document).on('submit', 'form#tax_rate_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.tax_rate_modal').modal('hide');
                  
                  toastr.success(result.msg);
                  
                  if($('#tax_rates_table')){
                      tax_rates_table.ajax.reload();   
                  }
                  if($('#default_tax_rates_table')){
                      default_tax_rates_table.ajax.reload();   
                  }


              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.edit_tax_rate_button', function(e) {
      e.preventDefault();

      $('div.tax_rate_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#tax_rate_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.tax_rate_modal').modal('hide');

                          toastr.success(result.msg);

                          if($('#tax_rates_table')){
                              tax_rates_table.ajax.reload();   
                          }
                          
                          if($('#default_tax_rates_table')){
                              default_tax_rates_table.ajax.reload();   
                          }

                          if($('#tax_groups_table')){
                              tax_groups_table.ajax.reload();   
                          }
                          
                           if($('#default_tax_groups_table')){
                              default_tax_groups_table.ajax.reload();   
                          }


                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_tax_rate_button', function(e) {
      e.preventDefault();
      
      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_tax_rate,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          if($('#tax_rates_table')){
                              tax_rates_table.ajax.reload();   
                          }
                          
                          if($('#default_tax_rates_table')){
                              default_tax_rates_table.ajax.reload();   
                          }

                          if($('#tax_groups_table')){
                              tax_groups_table.ajax.reload();   
                          }
                          
                           if($('#default_tax_groups_table')){
                              default_tax_groups_table.ajax.reload();   
                          }


                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });



  var is_property = $('#is_property').val();

  var units_table = $('#unit_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/units?is_property=' + is_property,

      columnDefs: [{ targets: 3, orderable: false, searchable: false }],

      columns: [

          { data: 'actual_name', name: 'actual_name' },

          { data: 'short_name', name: 'short_name' },

          { data: 'allow_decimal', name: 'allow_decimal' },

          { data: 'multiple_units', name: 'multiple_units' },

          { data: 'connected_units', name: 'connected_units' },

          { data: 'action', name: 'action' },

      ],

  });
  var vat_units_table = $('#vat_unit_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/vat-module/vat-units',

      columns: [

          { data: 'actual_name', name: 'actual_name' },

          { data: 'allow_decimal', name: 'allow_decimal' },

          { data: 'action', name: 'action' },

      ],

  });

  $(document).on('submit', 'form#unit_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.unit_modal').modal('hide');

                  toastr.success(result.msg);

                  units_table.ajax.reload();
                  
                  if($('#vat_unit_table')){
                      vat_units_table.ajax.reload();
                  }

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.edit_unit_button', function(e) {

      e.preventDefault();

      $('div.unit_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#unit_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.unit_modal').modal('hide');

                          toastr.success(result.msg);

                          units_table.ajax.reload();
                          
                          if($('#vat_unit_table')){
                              vat_units_table.ajax.reload();
                          }

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_unit_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_unit,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          units_table.ajax.reload();
                          
                          if($('#vat_unit_table')){
                              vat_units_table.ajax.reload();
                          }

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });



  var contact_table_type = $('#contact_type').val();

  if (contact_table_type == 'supplier') {

      var columns = [

          { data: 'mass_delete', searchable: false, orderable: false },

          { data: 'action', searchable: false, orderable: false },

          { data: 'contact_id', name: 'contact_id' },

          { data: 'supplier_business_name', name: 'supplier_business_name' },

          { data: 'name', name: 'name' },

          { data: 'mobile', name: 'mobile' },

          { data: 'supplier_group', name: 'supplier_group' },

          { data: 'assigned_to', searchable: false,name: 'assigned_to'},

          { data: 'pay_term', searchable: false,name: 'pay_term' },

          { data: 'due',   name:'due'},

          { data: 'return_due',  name:'return_due'},

          { data: 'opening_balance', name: 'opening_balance' },

          { data: 'email', name: 'email' },

          { data: 'tax_number', name: 'tax_number' },

          { data: 'created_at', name: 'created_at' },

      ];

      if (!$('.contact_custom_field1').hasClass('hide')) {

          columns.push({

              data: 'custom_field1',

              name: 'custom_field1',

              searchable: false,

              orderable: false,

          });

      }

      if (!$('.contact_custom_field1').hasClass('hide')) {

          columns.push({

              data: 'custom_field2',

              name: 'custom_field2',

              searchable: false,

              orderable: false,

          });

      }

      if (!$('.contact_custom_field1').hasClass('hide')) {

          columns.push({

              data: 'custom_field3',

              name: 'custom_field3',

              searchable: false,

              orderable: false,

          });

      }

      if (!$('.contact_custom_field1').hasClass('hide')) {

          columns.push({

              data: 'custom_field4',

              name: 'custom_field4',

              searchable: false,

              orderable: false,

          });

      }

  } else if (contact_table_type == 'customer') {
      var columns = [
          { data: 'mass_delete', searchable: false, orderable: false },
          { data: 'action', searchable: false, orderable: false },
          { data: 'contact_id', name: 'contact_id' },
          { data: 'name', name: 'name' },
          { data: 'mobile', name: 'mobile' },
          { data: 'customer_group', name: 'customer_group' },
          { data: 'assigned_to', searchable: true,name: 'assigned_to'},
          { data: 'credit_limit', name: 'credit_limit' },
          { data: 'due',  name:'due'},
          { data: 'return_due',  name:'return_due' },
          { data: 'pay_term', searchable: false, name: 'pay_term' },
          { data: 'image', name:'image'},
          { data: 'signature', name:'signature'},
          { data: 'created_at', name: 'created_at' },
      ];
  }

  var contact_table = $('#contact_table').DataTable({

      processing: true,
      serverSide: true,
      searching: true,

      ajax: {

          url: '/contacts',

          data: function(d) {

              d.type = $('#contact_type').val();
              d.user_id = $("#assigned_to").val();
              d.module = $('#module').val();
           

          },

      },

      aaSorting: [
          [1, 'desc']
      ],

      columns: columns,


      buttons: [

          {

              extend: 'csv',
              footer: true,

              text: '<i class="fa fa-file"></i> Export to CSV',

              className: 'btn btn-xs btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'excel',
              footer: true,

              text: '<i class="fa fa-file-excel-o"></i> Export to Excel',

              className: 'btn btn-xs btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {

                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'colvis',
              footer: true,

              text: '<i class="fa fa-columns"></i> Column Visibility',

              className: 'btn btn-xs btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {

                     var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'pdf',
              footer: true,

              text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',

              className: 'btn btn-xs btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'print',
              footer: true,

              text: '<i class="fa fa-print"></i> Print',

              className: 'btn btn-xs btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;
                  },
                  rows: ":not('.notexport-row')"

              },

          },

      ],
      
      createdRow: function (row, data, dataIndex) {
          var $dueElement = $('<div>').html(data.due);
          var origValue = $dueElement.find('.due').data('orig-value');
          if (parseFloat(origValue).toFixed(2) == 0) {
              $(row).addClass('notexport-row');
          }
      },

      fnDrawCallback: function(oSettings) {
          var total_pay_term = sum_table_col($('#contact_table'), 'pay_term');
          $('#footer_pay_term').text(total_pay_term);

          var tot_due = sum_table_col($('#contact_table'), 'due');
          $('#footer_tot_due').text(tot_due);
          
          var over_out = sum_outstanding($('#contact_table'), 'due');
          $('#total_outstanding').text(over_out[0]);
          $('#total_overpayment').text(over_out[1]);

         var tot_credit_limit = sum_table_col($('#contact_table'), 'credit_limit');

          $('#footer_tot_credit_limit').text(tot_credit_limit);

          var total_return_due = sum_table_col($('#contact_table'), 'return_due');

          $('#footer_contact_return_due').text(total_return_due);

          var total_opening_balance = sum_table_col($('#contact_table'), 'ob');


          $('#footer_contact_opening_balance').text(total_opening_balance);

          __currency_convert_recursively($('#contact_table'));

      },

  });
  
  var vat_contact_table = $('#vat_contact_table').DataTable({

      processing: true,
      serverSide: true,
      searching: true,

      ajax: {

          url: '/vat-module/vat-contacts',

          data: function(d) {
              d.type = $('#contact_type').val();
          },

      },

      aaSorting: [
          [1, 'desc']
      ],

      columns: [
          { data: 'mass_delete', searchable: false, orderable: false },

          { data: 'action', searchable: false, orderable: false },

          { data: 'contact_id', name: 'contact_id' },

          { data: 'name', name: 'name' },

          { data: 'mobile', name: 'mobile' },
          
          { data: 'created_at', name: 'created_at' },    
      ],


      buttons: [

          {

              extend: 'csv',
              footer: true,

              text: '<i class="fa fa-file"></i> Export to CSV',

              className: 'btn btn-sm btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'excel',
              footer: true,

              text: '<i class="fa fa-file-excel-o"></i> Export to Excel',

              className: 'btn btn-sm btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {

                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'colvis',
              footer: true,

              text: '<i class="fa fa-columns"></i> Column Visibility',

              className: 'btn btn-sm btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {

                     var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'pdf',
              footer: true,

              text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',

              className: 'btn btn-sm btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;

                  },

              },

          },

          {

              extend: 'print',
              footer: true,

              text: '<i class="fa fa-print"></i> Print',

              className: 'btn btn-sm btn-default',

              exportOptions: {

                  columns: function(idx, data, node) {
                      var isColumnExportable = $(node).is(':visible') && !$(node).hasClass('notexport');
                      return isColumnExportable;
                  },
                  rows: ":not('.notexport-row')"

              },

          },

      ],

  });

  var business_users_table = $('#business_users_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: {

          url: '/business-users',

          data: function(d) {

              d.business_id = $('#business_id').val();

          },

      },

      columnDefs: [{ targets: [6], orderable: false, searchable: false }],

      columns: [

          { data: 'username' },

          { data: 'full_name' },

          { data: 'role' },

          { data: 'business_name' },

          { data: 'email' },

          { data: 'contact_number' },

          { data: 'action' },

      ],

  });

  $('.contact_modal').on('shown.bs.modal', function(e) {  

      if ($('select#contact_type').val() == 'customer') {
          
          // required
          $(".nic_number").attr('required',true);
          

          $('div.supplier_fields').addClass('hide');

          $('div.customer_fields').each(function() {

              if (!$(this).hasClass('backend_hide')) {

                  $(this).removeClass('hide');

              }

          });

      } else if ($('select#contact_type').val() == 'supplier') {
          
          $(".nic_number").attr('required',false);

          $('div.supplier_fields').each(function() {

              if (!$(this).hasClass('backend_hide')) {

                  $(this).removeClass('hide');

              }

          });

          $('div.customer_fields').addClass('hide');

      }
      

      $('select#contact_type').change(function() {

          var t = $(this).val();

          if (t == 'supplier') {
              
              $(".nic_number").attr('required',false);

              $('div.supplier_fields').each(function() {

                  if (!$(this).hasClass('backend_hide')) {

                      $(this).fadeIn();
                       $(this).removeClass('hide');
                      

                  }

              });

              $('div.customer_fields').fadeOut();

          } else if (t == 'both') {
              
              $(".nic_number").attr('required',true);

              $('div.supplier_fields').each(function() {
                  
                  if (!$(this).hasClass('backend_hide')) {

                      $(this).fadeIn();
                       $(this).removeClass('hide');

                  }

              });

              $('div.customer_fields').each(function() {

                  if (!$(this).hasClass('backend_hide')) {

                      $(this).fadeIn();

                  }

              });

          } else if (t == 'customer') {
              
              $(".nic_number").attr('required',true);

              $('div.customer_fields').each(function() {

                  if (!$(this).hasClass('backend_hide')) {

                      $(this).fadeIn();
                       $(this).removeClass('hide');

                  }

              });

              $('div.supplier_fields').fadeOut();

          }

      });
      
      
      
      

      $('form#contact_add_form, form#contact_edit_form')

      .submit(function(e) {

          e.preventDefault();

      })

      .validate({

          rules: {

              contact_id: {

                  remote: {

                      url: '/contacts/check-contact-id',

                      type: 'post',

                      data: {

                          contact_id: function() {

                              return $('#contact_id').val();

                          },

                          hidden_id: function() {

                              if ($('#hidden_id').length) {

                                  return $('#hidden_id').val();

                              } else {

                                  return '';

                              }

                          },

                      },

                  },

              },

          },

          messages: { contact_id: { remote: LANG.contact_id_already_exists } },

          submitHandler: function(form) {
              $.ajax({
                  method: 'POST',
                  url: base_path + '/check-mobile',
                  dataType: 'json',
                  data: {
                      contact_id: function() {
                          return $('#hidden_id').val();
                      },
                      mobile_number: function() {
                          return $('#mobile').val();
                      },
                  },
                  beforeSend: function(xhr) {
                      __disable_submit_button($(form).find('button[type="submit"]'));
                  },
                  success: function(result) {
                      if (result.is_mobile_exists == true) {
                          swal({
                              title: LANG.sure,
                              text: result.msg,
                              icon: 'warning',
                              buttons: true,
                              dangerMode: true,
                          }).then(willContinue => {
                              if (willContinue) {
                                  submitAddContactForm(form);
                              } else {
                                  $('#mobile').select();
                              }
                          });

                      } else {
                          submitAddContactForm(form);
                      }
                  },
              });
          },

      });
      $('form#airline_contact_add_form')

          .submit(function(e) {
    
              e.preventDefault();
    
          })
    
          .validate({
    
              rules: {
    
                  contact_id: {
    
                      remote: {
    
                          url: '/contacts/check-contact-id',
    
                          type: 'post',
    
                          data: {
    
                              contact_id: function() {
    
                                  return $('#contact_id').val();
    
                              },
    
                              hidden_id: function() {
    
                                  if ($('#hidden_id').length) {
    
                                      return $('#hidden_id').val();
    
                                  } else {
    
                                      return '';
    
                                  }
    
                              },
    
                          },
    
                      },
    
                  },
    
              },
    
              messages: { contact_id: { remote: LANG.contact_id_already_exists } },
    
                submitHandler: function(form) {
                var data = $(form).serialize();
             //   console.log($(form).attr('action'));
              // $(form).find('button[type="submit"]').attr('disabled', true);

              $.ajax({
                  method: 'POST',
                  url: $(form).attr('action'),
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  cache:false,
                  encode: true,
                  data:  new FormData(form),
                  success: function(result) {

                      if (result.success == true) {

                          $('div.contact_modal').modal('hide');

                          toastr.success(result.msg);
                          if(result.data){
                            var data = {
                                id: result.data.id,
                                text: result.data.name
                            };
                            var newOption = new Option(data.text, data.id, false, false);
                            $('#customer_select').append(newOption).trigger('change');
                        }
                          

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });
              },
    
          });
          
        
       $('form#vat_contact_add_form, form#vat_contact_edit_form')

      .submit(function(e) {

          e.preventDefault();

      })

      .validate({

          submitHandler: function(form) {
              var data = $(form).serialize();
              
              $.ajax({
                  method: 'POST',
                  url: $(form).attr('action'),
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  cache:false,
                  encode: true,
                  data:  new FormData(form),
                  success: function(result) {

                      if (result.success == true) {

                          $('div.contact_modal').modal('hide');

                          toastr.success(result.msg);
                          vat_contact_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });  
          },

      });

  });
  function submitAddContactForm(form) {
      var data = $(form).serialize();
              console.log($(form).attr('action'));
              // $(form).find('button[type="submit"]').attr('disabled', true);

              $.ajax({
                  method: 'POST',
                  url: $(form).attr('action'),
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  cache:false,
                  encode: true,
                  data:  new FormData(form),
                  success: function(result) {

                      if (result.success == true) {

                          $('div.contact_modal').modal('hide');

                          toastr.success(result.msg);
                         

                          if ($("#contact_table").length > 0) {
                              contact_table.ajax.reload();
                          }

                          if ($("#property_contact_table").length > 0) {
                              property_contact_table.ajax.reload();
                          }
                          
                          $.ajax({
                              method: 'get',
                              url: '/contacts/get_outstanding',
                              success: function(result) {
                                  if (result && Object.keys(result).length > 0) {
                                      $('#total_outstanding').text(result.total_outstanding);
                                      $('#total_overpayment').text(result.total_overpayment);
                                  // $('#total_os').html(result);
                                  __currency_convert_recursively($('#contact_table'));
                                  }
                              },
                          });
                          
                          const select = document.getElementById('customer_id');

                          if(result.data){
                              //point 4c done updated by dushyant
                              // Create a new option element
                              const newOption = document.createElement('option');
                              newOption.value = result.data.id; // Set the value attribute
                              newOption.text = result.data.name; // Set the text content
    
                              // Append the new option to the select element
                              select.appendChild(newOption);
                          }
                          

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });
  }
  $(document).on('click', '.edit_contact_button', function(e) {

      e.preventDefault();

      $('div.contact_modal').load($(this).attr('href'), function() {

          $(this).modal('show');

      });

  });

  $(document).on('click', '.delete_contact_button', function(e) {

      e.preventDefault();

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_contact,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).attr('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          contact_table.ajax.reload();
                          
                          if($("#vat_contact_table")){
                              vat_contact_table.ajax.reload();
                          }

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  category_table = $('#category_table').DataTable({

      processing: true,

      serverSide: true,

      aaSorting: [
          [1, 'desc']
      ],

      ajax: {

          url: '/categories',

      },

      columns: [

          { data: 'name', name: 'name' },

          { data: 'short_code', name: 'short_code' },

          { data: 'action', name: 'action' },

      ],

  });

  $(document).on('submit', 'form#category_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success === true) {

                  $('div.category_modal').modal('hide');

                  toastr.success(result.msg);

                  category_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.edit_category_button', function() {

      $('div.category_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#category_edit_form').submit(function(e) {

              e.preventDefault();

              $(this).find('button[type="submit"]').attr('disabled', true);

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          $('div.category_modal').modal('hide');

                          toastr.success(result.msg);

                          category_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_category_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_category,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          toastr.success(result.msg);

                          category_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  var variation_table = $('#variation_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/variation-templates',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });

  $(document).on('click', '#add_variation_values', function() {

      var html =

          '<div class="form-group"><div class="col-sm-7 col-sm-offset-3"><input type="text" name="variation_values[]" class="form-control" required></div><div class="col-sm-2"><button type="button" class="btn btn-danger delete_variation_value">-</button></div></div>';

      $('#variation_values').append(html);

  });

  $(document).on('click', '.delete_variation_value', function() {

      $(this).closest('.form-group').remove();

  });

  $(document).on('submit', 'form#variation_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success === true) {

                  $('div.variation_modal').modal('hide');

                  toastr.success(result.msg);

                  variation_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.edit_variation_button', function() {

      $('div.variation_modal').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#variation_edit_form').submit(function(e) {

              $(this).find('button[type="submit"]').attr('disabled', true);

              e.preventDefault();

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          $('div.variation_modal').modal('hide');

                          toastr.success(result.msg);

                          variation_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_variation_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_variation,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          toastr.success(result.msg);

                          variation_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  var active = false;

  $(document).on('mousedown', '.drag-select', function(ev) {

      active = true;

      $('.active-cell').removeClass('active-cell');

      $(this).addClass('active-cell');

      cell_value = $(this).find('input').val();

  });

  $(document).on('mousemove', '.drag-select', function(ev) {

      if (active) {

          $(this).addClass('active-cell');

          $(this).find('input').val(cell_value);

      }

  });

  $(document).mouseup(function(ev) {

      active = false;

      if (

          !$(ev.target).hasClass('drag-select') &&

          !$(ev.target).hasClass('dpp') &&

          !$(ev.target).hasClass('dsp')

      ) {

          $('.active-cell').each(function() {

              $(this).removeClass('active-cell');

          });

      }

  });

  $(document).on('change', '.toggler', function() {

      var parent_id = $(this).attr('data-toggle_id');
      var parent_class = $(this).attr('data-toggle_class');

      if ($(this).is(':checked')) {

          $('#' + parent_id).removeClass('hide');
          $('.' + parent_class).removeClass('hide');

      } else {

          $('#' + parent_id).addClass('hide');
          $('.' + parent_class).addClass('hide');

      }

  });

  $('#category_id').change(function() {

      get_sub_categories();

  });

  $(document).on('change', '#unit_id', function() {

      get_sub_units();

  });

  if ($('.product_form').length && !$('.product_form').hasClass('create')) {

      show_product_type_form();

  }
  
   if ($('.vat_product_form').length) {

      show_vat_product_type_form();

  }

  $('#type').change(function() {

      show_product_type_form();

  });

  $(document).on('click', '#add_variation', function() {

      var row_index = $('#variation_counter').val();

      var type = $('#type').val();

      var action = $(this).attr('data-action');

      $.ajax({

          method: 'POST',

          url: '/products/get_product_variation_row',

          data: { row_index: row_index, action: action, type: type },

          dataType: 'html',

          success: function(result) {

              if (result) {

                  $('#product_variation_form_part  > tbody').append(result);

                  $('#variation_counter').val(parseInt(row_index) + 1);

                    if (typeof toggle_dsp_input === 'function') {
                        toggle_dsp_input();
                    }

              }

          },

      });

  });

  if ($('form#bussiness_edit_form').length > 0) {

      $('form#bussiness_edit_form').validate({ ignore: [] });

      $('#business_logo').fileinput(fileinput_setting);

      $('input#purchase_in_diff_currency').on('ifChecked', function(event) {

          $('div#settings_purchase_currency_div, div#settings_currency_exchange_div').removeClass(

              'hide'

          );

      });

      $('input#purchase_in_diff_currency').on('ifUnchecked', function(event) {

          $('div#settings_purchase_currency_div, div#settings_currency_exchange_div').addClass(

              'hide'

          );

      });

      $('input#enable_product_expiry').change(function() {

          if ($(this).is(':checked')) {

              $('select#expiry_type').attr('disabled', false);

              $('div#on_expiry_div').removeClass('hide');

          } else {

              $('select#expiry_type').attr('disabled', true);

              $('div#on_expiry_div').addClass('hide');

          }

      });

      $('select#on_product_expiry').change(function() {

          if ($(this).val() == 'stop_selling') {

              $('input#stop_selling_before').attr('disabled', false);

              $('input#stop_selling_before').focus().select();

          } else {

              $('input#stop_selling_before').attr('disabled', true);

          }

      });

      $('input#enable_category').on('ifChecked', function(event) {

          $('div.enable_sub_category').removeClass('hide');

      });

      $('input#enable_category').on('ifUnchecked', function(event) {

          $('div.enable_sub_category').addClass('hide');

      });

  }

  $('#upload_document').fileinput(fileinput_setting);

  $('form#edit_user_profile_form').validate();

  $('form#edit_password_form').validate({

      rules: {

          current_password: { required: true, minlength: 5 },

          new_password: { required: true, minlength: 5 },

          confirm_password: { equalTo: '#new_password' },

      },

  });

  var tax_groups_table = $('#tax_groups_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/group-taxes',

      columnDefs: [{ targets: [2, 3], orderable: false, searchable: false }],

      columns: [

          { data: 'name', name: 'name' },

          { data: 'amount', name: 'amount' },

          { data: 'sub_taxes', name: 'sub_taxes' },

          { data: 'action', name: 'action' },

      ],

  });
  
  var default_tax_groups_table = $('#default_tax_groups_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/superadmin/group-tax',

      columnDefs: [{ targets: [2, 3], orderable: false, searchable: false }],

      columns: [

          { data: 'name', name: 'name' },

          { data: 'amount', name: 'amount' },

          { data: 'sub_taxes', name: 'sub_taxes' },

          { data: 'action', name: 'action' },

      ],

  });

  $('.tax_group_modal').on('shown.bs.modal', function() {

      $('.tax_group_modal')

      .find('.select2')

      .each(function() {

          __select2($(this));

      });

  });

  $(document).on('submit', 'form#tax_group_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.tax_group_modal').modal('hide');

                  toastr.success(result.msg);

                  if($('#tax_groups_table')){
                      tax_groups_table.ajax.reload();   
                  }
                   if($('#default_tax_groups_table')){
                      default_tax_groups_table.ajax.reload();   
                  }


              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('submit', 'form#tax_group_edit_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.tax_group_modal').modal('hide');

                  toastr.success(result.msg);

                  if($('#tax_groups_table')){
                      tax_groups_table.ajax.reload();   
                  }
                  
                   if($('#default_tax_groups_table')){
                      default_tax_groups_table.ajax.reload();   
                  }


              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.delete_tax_group_button', function(e) {
      e.preventDefault();
      swal({

          title: LANG.sure,

          text: LANG.confirm_tax_group,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);
                          
                          if($('#tax_groups_table')){
                              tax_groups_table.ajax.reload();   
                          }
                          
                           if($('#default_tax_groups_table')){
                              default_tax_groups_table.ajax.reload();   
                          }


                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  $(document).on('click', '.option-div-group .option-div', function() {

      $(this)

      .closest('.option-div-group')

      .find('.option-div')

      .each(function() {

          $(this).removeClass('active');

      });

      $(this).addClass('active');

      $(this).find('input:radio').prop('checked', true).change();

  });

  $(document).on('change', 'input[type=radio][name=scheme_type]', function() {

      $('#invoice_format_settings').removeClass('hide');

      var scheme_type = $(this).val();

      if (scheme_type == 'blank') {

          $('#prefix').val('').attr('placeholder', 'XXXX').prop('disabled', false);

      } else if (scheme_type == 'year') {

          var d = new Date();

          var this_year = d.getFullYear();

          $('#prefix')

          .val(this_year + '-')

          .attr('placeholder', '')

          .prop('disabled', true);

      }

      show_invoice_preview();

  });

  $(document).on('change', '#prefix', function() {

      show_invoice_preview();

  });

  $(document).on('keyup', '#prefix', function() {

      show_invoice_preview();

  });

  $(document).on('keyup', '#start_number', function() {

      show_invoice_preview();

  });

  $(document).on('change', '#total_digits', function() {

      show_invoice_preview();

  });

  var invoice_table = $('#invoice_table').DataTable({

      processing: true,

      serverSide: true,

      bPaginate: false,

      buttons: [],

      ajax: '/invoice-schemes',

      columnDefs: [{ targets: 4, orderable: false, searchable: false }],

  });

  $(document).on('submit', 'form#invoice_scheme_add_form', function(e) {

      e.preventDefault();

      $(this).find('button[type="submit"]').attr('disabled', true);

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.invoice_modal').modal('hide');

                  $('div.invoice_edit_modal').modal('hide');

                  toastr.success(result.msg);

                  invoice_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.set_default_invoice', function() {

      var href = $(this).data('href');

      var data = $(this).serialize();

      $.ajax({

          method: 'get',

          url: href,

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success === true) {

                  toastr.success(result.msg);

                  invoice_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $('.invoice_edit_modal').on('shown.bs.modal', function() {

      show_invoice_preview();

  });

  $(document).on('click', 'button.delete_invoice_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.delete_invoice_confirm,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          toastr.success(result.msg);

                          invoice_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  $('#add_barcode_settings_form').validate();

  $(document).on('change', '#is_continuous', function() {

      if ($(this).is(':checked')) {

          $('.stickers_per_sheet_div').addClass('hide');

          $('.paper_height_div').addClass('hide');

      } else {

          $('.stickers_per_sheet_div').removeClass('hide');

          $('.paper_height_div').removeClass('hide');

      }

  });

  $('input[type="checkbox"].input-icheck, input[type="radio"].input-icheck').iCheck({

      checkboxClass: 'icheckbox_square-blue',

      radioClass: 'iradio_square-blue',

  });

  $(document).on('ifChecked', '.check_all', function() {

      $(this)

      .closest('.check_group')

      .find('.input-icheck')

      .each(function() {

          $(this).iCheck('check');

      });

  });

  $(document).on('ifUnchecked', '.check_all', function() {

      $(this)

      .closest('.check_group')

      .find('.input-icheck')

      .each(function() {

          $(this).iCheck('uncheck');

      });

  });

  $('.check_all').each(function() {

      var length = 0;

      var checked_length = 0;

      $(this)

      .closest('.check_group')

      .find('.input-icheck')

      .each(function() {

          length += 1;

          if ($(this).iCheck('update')[0].checked) {

              checked_length += 1;

          }

      });

      length = length - 1;

      if (checked_length != 0 && length == checked_length) {

          $(this).iCheck('check');

      }

  });

  business_locations = $('#business_location_table').DataTable({

      processing: true,

      serverSide: true,

      bPaginate: false,

      buttons: [],

      ajax: '/business-location',

      columnDefs: [{ targets: 10, orderable: false, searchable: false }],

  });

  $('.location_add_modal, .location_edit_modal').on('shown.bs.modal', function(e) {

      $('form#business_location_add_form')

      .submit(function(e) {

          e.preventDefault();

      })

      .validate({

          rules: {

              location_id: {

                  remote: {

                      url: '/business-location/check-location-id',

                      type: 'post',

                      data: {

                          location_id: function() {

                              return $('#location_id').val();

                          },

                          hidden_id: function() {

                              if ($('#hidden_id').length) {

                                  return $('#hidden_id').val();

                              } else {

                                  return '';

                              }

                          },

                      },

                  },

              },

          },

          messages: { location_id: { remote: LANG.location_id_already_exists } },

          submitHandler: function(form) {

              e.preventDefault();

              $(form).find('button[type="submit"]').attr('disabled', true);

              var data = $(form).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(form).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.location_add_modal').modal('hide');

                          $('div.location_edit_modal').modal('hide');

                          toastr.success(result.msg);

                          business_locations.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          },

      });

  });

  if ($('#header_text').length) {

      CKEDITOR.replace('header_text', { customConfig: '/AdminLTE/plugins/ckeditor/config.js' });

  }

  if ($('#footer_text').length) {

      CKEDITOR.replace('footer_text', { customConfig: '/AdminLTE/plugins/ckeditor/config.js' });

  }

  var expense_cat_table = $('#expense_category_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/expense-categories',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });
  
  var vat_expense_cat_table = $('#vat_expense_category_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/vat-module/vat-expense-categories',
     

  });
  
  var expense_cat_table = $('#expense_category_code_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/expense-categories-code',

      columns: [

          { data: 'date', name: 'date' },

          { data: 'prefix', name: 'prefix' },

          { data: 'starting_no', name: 'starting_no' },
          
          { data: 'username', name: 'users.name' },
          
          { data: 'action', name: 'action', orderable: false, searchable: false },

      ],

  });

  var expense_cat_table = $("#expense_category_code_table1").DataTable({
      processing: true,
  
      serverSide: true,
      aaSorting: [[0, "desc"]],
      ajax: "/expense-categories-number",
  
      columns: [
        { data: "date", name: "date" },
  
        { data: "prefix", name: "prefix" },
  
        { data: "starting_no", name: "starting_no" },
  
        { data: "username", name: "users.name" },
  
        { data: "action", name: "action", orderable: false, searchable: false },
      ],
    });
  
  

  $(document).on('submit', 'form#expense_category_add_form', function(e) {

      e.preventDefault();

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success === true) {

                  $('div.expense_category_modal').modal('hide');

                  toastr.success(result.msg);
                  
                  if($("#expense_category_table")){
                      expense_cat_table.ajax.reload();
                  }
                  
                  if($("#vat_expense_category_table")){
                      vat_expense_cat_table.ajax.reload();
                  }

                  

              } else {

                  toastr.error(result.msg);

              }

              var expense_category_id = result.expense_category_id;

              if ($('#expense_quick_add').val()) {

                  get_expense_categories_drop_down(expense_category_id);

                  $('.view_modal').modal('hide');

              }

          },

      });

  });



  function get_expense_categories_drop_down(expense_category_id) {

      $.ajax({

          method: 'get',

          url: '/expense-categories/get-drop-down',

          contentType: 'html',

          data: {},

          success: function(result) {

              $('#expense_category').empty().append(result);

              $('#expense_category').val(expense_category_id).trigger('change');

          },

      });

  }

  $(document).on('click', 'button.delete_expense_category', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_expense_category,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          toastr.success(result.msg);

                          if($("#expense_category_table")){
                              expense_cat_table.ajax.reload();
                          }
                          
                          if($("#vat_expense_category_table")){
                              vat_expense_cat_table.ajax.reload();
                          }

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });

  if ($('#expense_date_range').length == 1) {

      $('#expense_date_range').daterangepicker(dateRangeSettings, function(start, end) {

          $('#expense_date_range').val(

              start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)

          );
          
          if($("#expense_table")){
              expense_table.ajax.reload();
          }
          
          
          if($("#vat_expense_table")){
              vat_expense_table.ajax.reload();
          }

          

      });

        $('#custom_date_apply_button').on('click', function() {
            if($('#target_custom_date_input').val() == "expense_date_range"){
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#expense_date_range').val(
                        formattedStartDate + ' ~ ' + formattedEndDate
                    );

                    $('#expense_date_range').data('daterangepicker').setStartDate(moment(startDate));
                    $('#expense_date_range').data('daterangepicker').setEndDate(moment(endDate));
                    
                    if($("#expense_table")){
                        expense_table.ajax.reload();
                    }
                    if($("#vat_expense_table")){
                        vat_expense_table.ajax.reload();
                    }

                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            }
        });
        $('#expense_date_range').on('apply.daterangepicker', function(ev, picker) {
            if (picker.chosenLabel === 'Custom Date Range') {
                $('#target_custom_date_input').val('expense_date_range');
                $('.custom_date_typing_modal').modal('show');
            }
        });

      $('#expense_date_range').on('cancel.daterangepicker', function(ev, picker) {

          $('#product_sr_date_filter').val('');

          if($("#expense_table")){
              expense_table.ajax.reload();
          }
          
          
          if($("#vat_expense_table")){
              vat_expense_table.ajax.reload();
          }

      });

      $('#expense_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));

      $('#expense_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));

  }

  expense_table = $('#expense_table').DataTable({

      processing: true,

      serverSide: true,

      aaSorting: [
          [1, 'desc']
      ],

      ajax: {

          url: '/expenses',

          data: function(d) {

              d.expense_for = $('select#expense_for').val();
              
              d.payee_name = $('select#payee_name').val();

              d.location_id = $('select#location_id').val();

              d.expense_category_id = $('select#expense_category_id').val();

              d.payment_status = $('select#expense_payment_status').val();

              d.start_date = $('input#expense_date_range')

              .data('daterangepicker')

              .startDate.format('YYYY-MM-DD');

              d.end_date = $('input#expense_date_range')

              .data('daterangepicker')

              .endDate.format('YYYY-MM-DD');

          },

      },

      columns: [

          { data: 'action', name: 'action', orderable: false, searchable: false },

          { data: 'transaction_date', name: 'transaction_date' },

          { data: 'ref_no', name: 'ref_no' },
          
          { data: 'payee_name', name: 'contacts.name' },

          { data: 'category', name: 'ec.name' },

          { data: 'location_name', name: 'bl.name' },

          { data: 'payment_status', name: 'payment_status', orderable: false },

          { data: 'tax', name: 'tr.name' },

          { data: 'final_total', name: 'final_total' },

          { data: 'payment_due', name: 'payment_due' },

          { data: 'payment_method', name: 'payment_method' },

          { data: 'expense_for', name: 'expense_for' },

          { data: 'additional_notes', name: 'additional_notes' },

          { data: 'created_by', name: 'created_by' },

      ],

      fnDrawCallback: function(oSettings) {

          var expense_total = sum_table_col($('#expense_table'), 'final-total');

          $('#footer_expense_total').text(expense_total);

          var total_due = sum_table_col($('#expense_table'), 'payment_due');

          $('#footer_total_due').text(total_due);

          $('#footer_payment_status_count').html(

              __sum_status_html($('#expense_table'), 'payment-status')

          );

          __currency_convert_recursively($('#expense_table'));

      },

      createdRow: function(row, data, dataIndex) {

          $(row).find('td:eq(4)').attr('class', 'clickable_td');

      },

  });
  
  vat_expense_table = $('#vat_expense_table').DataTable({

      processing: true,

      serverSide: true,

      aaSorting: [
          [1, 'desc']
      ],

      ajax: {

          url: '/vat-module/vat-expense',

          data: function(d) {

              d.expense_category_id = $('select#expense_category_id').val();

              d.payment_status = $('select#expense_payment_status').val();

              d.start_date = $('input#expense_date_range')

              .data('daterangepicker')

              .startDate.format('YYYY-MM-DD');

              d.end_date = $('input#expense_date_range')

              .data('daterangepicker')

              .endDate.format('YYYY-MM-DD');

          },

      },

      columns: [

          { data: 'action', name: 'action', orderable: false, searchable: false },

          { data: 'transaction_date', name: 'transaction_date' },

          { data: 'ref_no', name: 'ref_no' },
          
          { data: 'category', name: 'ec.name' },

          { data: 'payment_status', name: 'payment_status', orderable: false },

          { data: 'final_total', name: 'final_total' },

          { data: 'payment_method', name: 'payment_method' },

          { data: 'created_by', name: 'created_by' },

      ],

      fnDrawCallback: function(oSettings) {

          var expense_total = sum_table_col($('#vat_expense_table'), 'final-total');

          $('#footer_expense_total').text(expense_total);

          var total_due = sum_table_col($('#vat_expense_table'), 'payment_due');

          $('#footer_total_due').text(total_due);

          $('#footer_payment_status_count').html(

              __sum_status_html($('#vat_expense_table'), 'payment-status')

          );

          __currency_convert_recursively($('#vat_expense_table'));

      },

      createdRow: function(row, data, dataIndex) {

          $(row).find('td:eq(4)').attr('class', 'clickable_td');

      },

  });

  $(

      'select#location_id, select#expense_for,select#payee_name, select#expense_category_id, select#expense_payment_status'

  ).on('change', function() {

      if($("#expense_table")){
              expense_table.ajax.reload();
          }
          
          
          if($("#vat_expense_table")){
              vat_expense_table.ajax.reload();
          }

  });

  $('#expense_transaction_date').datetimepicker({

      format: moment_date_format + ' ' + moment_time_format,

      ignoreReadonly: true,

  });

  $(document).on('click', 'a.delete_expense', function(e) {

      e.preventDefault();

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_expense,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success === true) {

                          toastr.success(result.msg);

                          if($("#expense_table")){
                              expense_table.ajax.reload();
                          }
                          
                          
                          if($("#vat_expense_table")){
                              vat_expense_table.ajax.reload();
                          }

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });
  
  $(document).on('click', 'button.reconcile_status_btn', function(e) {

      e.preventDefault();

      swal({

          title: LANG.sure,

          text: "Pls check and reconfirm if all marked Reconciliations are correct. This operation cannot be changed once click 'Yes' button.",

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willReconcile) => {

          if (willReconcile) {

              var href = $(this).data('href');
              $.ajax({
                  method: 'get',
                  url: href,
                  data: {  },
                  success: function(result) {
                    if(result.success){
                      toastr.success('Success');
                      account_book.ajax.reload();
                    } else {

                          toastr.error(result.msg);

                      }
                  },
                });

          }

      });

  });

  $(document).on('change', '.payment_types_dropdown', function() {
      
      var payment_type = $(this).val();
      
      var to_show = null;

      var cheque_field = null;

      var location_id = $('#location_id').val();
      
      if(location_id == ""){
          var location_id = $('#pmt_location_id').val();
          if(location_id == ""){
              toastr.error("Select location first!");
              return false;
          }
      }

      var row_id = parseInt($(this).closest('.payment_row').find('.payment_row_index').val());
      
      var accounting_module = $(this).closest('.payment_row').find('.account_id');
      
      var previous_acc_id = parseInt($(this).closest('.payment_row').find('.previous_account').val());
      
      
      
      // var row_id = parseInt($(this).closest('.payment_row').data('row_id'));
      
      accounting_module.attr('required', true);
      accounting_module.empty();
      
      $(this).closest('.payment_row').find('.payment_details_div').each(function() {
          $(this).closest('.payment_row').find('.account_id').attr('required', true);
          $(this).closest('.payment_row').find('.account_module').removeClass('hide');
          
          if ($(this).attr('data-type') == 'cheque') {
              cheque_field = $(this);
          }

          if ($(this).attr('data-type') == payment_type) {
              to_show = $(this);
          } else {
              if (!$(this).hasClass('hide')) {
                  $(this).addClass('hide');
              }
          }
      });
      

      $('.card_type_div').addClass('hide');

      if (to_show && to_show.hasClass('hide')) {
          to_show.removeClass('hide');
          to_show.find('input').filter(':visible:first').focus();
      }
      
      
      if (payment_type.toLowerCase() == 'bank_transfer' || payment_type.toLowerCase() == 'direct_bank_deposit' || payment_type.toLowerCase() == 'bank'|| payment_type.toLowerCase() == 'cheque') {
          $(this).closest('.payment_row').find('.bank_transfer_fields').removeClass('hide');
          $(this).closest('.payment_row').find('.add_payment_bank_details').removeClass('hide');
          
          $(this).closest('.payment_row').find('.post_dated_cheque').removeClass('hide');
          
          if(payment_type == 'cheque'){
              $(this).closest('.payment_row').find('.cheque_payment_details').removeClass('hide');
          }else{
              $(this).closest('.payment_row').find('.cheque_payment_details').addClass('hide');
          }
      }else{
          $(this).closest('.payment_row').find('.post_dated_cheque').addClass('hide'); 
          $(this).closest('.payment_row').find('.icheckbox_square-blue').prop('checked', false);
          $(this).closest('.payment_row').find('.bank_transfer_fields').addClass('hide');
          $(this).closest('.payment_row').find('.add_payment_bank_details').addClass('hide');
          
          
      }
      let paymentType = payment_type.toLowerCase();
      if (payment_type.toLowerCase() == 'cheque' || paymentType === 'pre_payments') {
          $(this).closest('.payment_row').find('.cheque_payment_details_only').removeClass('hide');
          if($('#transaction_date_range_cheque_deposit').val()){
            console.log(paymentType);
              get_cheques_list(paymentType);
              $(this).closest('.payment_row').find('.cheque_payment_details').removeClass('show');
              $(this).closest('.payment_row').find('.post_dated_cheque').addClass('hide'); 
              $(this).closest('.payment_row').find('.icheckbox_square-blue').prop('checked', false);
              $(this).closest('.payment_row').find('.bank_transfer_fields').addClass('hide');
              $(this).closest('.payment_row').find('.add_payment_bank_details').addClass('hide');
          }
          
      }else{
          $(this).closest('.payment_row').find('.cheque_payment_details_only').removeClass('show');
      }
      // Specific logic for showing/hiding the #pre_payment_hide div
if (paymentType === 'pre_payments') {
    
    $('#pre_payment_hide').addClass('hide');
} else if (paymentType === 'cheque') {
    $('#pre_payment_hide').removeClass('hide');
}
      
      $.ajax({

              method: 'get',

              url: '/accounting-module/get-account-group-name-dp',

              data: { group_name: payment_type, location_id: location_id },

              contentType: 'html',

              success: function(result) {
                  console.log(result);
                  
                 accounting_module.empty().append(result);
                  accounting_module.attr('required', true);
                  accounting_module.val(accounting_module.find('option:first').val());
                  if(previous_acc_id){
                      accounting_module.val(previous_acc_id).change();
                  }
                  

              },

          });
      
      

      // if (payment_type == 'credit_purchase') {

      //     $(this).closest('.payment_row').find('.account_id').attr('required', false);

      //     $(this).closest('.payment_row').find('.account_module').addClass('hide');

      // }

      // if (payment_type == 'bank_transfer' || payment_type == 'direct_bank_deposit') {

      //     $(this).closest('.payment_row').find('.account_module').removeClass('hide');

      //     $.ajax({

      //         method: 'get',

      //         url: '/accounting-module/get-account-group-name-dp',

      //         data: { group_name: 'Bank Account', location_id: location_id },

      //         contentType: 'html',

      //         success: function(result) {

      //             if (row_id >= 0) {

      //                 $('#account_' + row_id)

      //                 .empty()

      //                 .append(result);

      //                 $('#account_' + row_id).attr('required', true);

      //             } else {

      //                 $('#account_id').attr('required', true);

      //                 $('#account_id').empty().append(result);

      //             }

      //         },

      //     });

      // }
      
      // if (payment_type == 'cpc') {

      //     $(this).closest('.payment_row').find('.cpc_module').removeClass('hide');

      //     $.ajax({

      //         method: 'get',

      //         url: '/accounting-module/get-account-group-name-dp',

      //         data: { group_name: 'CPC', location_id: location_id },

      //         contentType: 'html',

      //         success: function(result) {

      //             if (row_id >= 0) {

      //                 $('#account_' + row_id)

      //                 .empty()

      //                 .append(result);

      //                 $('#account_' + row_id).attr('required', true);

      //             } else {

      //                 $('#account_id').attr('required', true);

      //                 $('#account_id').empty().append(result);

      //             }

      //         },

      //     });

      // }
      

      // if (payment_type == 'cheque') {

      //     $.ajax({

      //         method: 'get',

      //         url: '/accounting-module/get-account-group-name-dp',

      //         data: { group_name: "Cheques in Hand (Customer's)", location_id: location_id },

      //         contentType: 'html',

      //         success: function(result) {
                  

      //             if (row_id >= 0) {

      //                 $('#account_' + row_id)

      //                 .empty()

      //                 .append(result);

      //                 check_insufficient_balance_row(row_id);

      //             } else {
                      
      //                 $('#account_id').empty().append(result);

      //                 $('#account_id')

      //                 .val($('#account_id option:contains("Cheques in Hand")').val())

      //                 .trigger('change');

      //             }

      //         },

      //     });

      // }

      // if (payment_type == 'company_cards') {
      //     $.ajax({
      //         method: 'get',
      //         url: '/accounting-module/get-account-group-name-dp',
      //         data: { group_name: "Company Cards", location_id: location_id },
      //         contentType: 'html',
      //         success: function(result) {
      //             if (row_id >= 0) {
      //                 $('#account_' + row_id)
      //                     .empty()
      //                     .append(result);
      //                 check_insufficient_balance_row(row_id);
      //             } else {
      //                 $('#account_id').empty().append(result);
      //                 $('#account_id')
      //                     .val($('#account_id option:contains("Company Cards")').val())
      //                     .trigger('change');
      //             }
      //         },
      //     });
      // }
      
      // if (payment_type == 'credit_expense' || payment_type == 'credit_purchase') {
      //     $.ajax({
      //         method: 'get',
      //         url: '/accounting-module/get-account-group-name-dp',
      //         data: { group_name: "accounts_payable", location_id: location_id },
      //         contentType: 'html',
      //         success: function(result) {
      //             if (row_id >= 0) {
      //                 $('#account_' + row_id)
      //                     .empty()
      //                     .append(result);
      //                 $('#account_' + row_id).attr('required', true);
      //             } else {
      //                 $('#account_id').empty().append(result);
      //                 $('#account_id').attr('required', true);
      //             }
                  
                  
      //         },
      //     });
      // }
          
      // if (payment_type == 'credit_sale') {
      //     $.ajax({
      //         method: 'get',
      //         url: '/accounting-module/get-account-group-name-dp',
      //         data: { group_name: "accounts_receivable", location_id: location_id },
      //         contentType: 'html',
      //         success: function(result) {
      //             if (row_id >= 0) {
      //                 $('#account_' + row_id)
      //                     .empty()
      //                     .append(result);
      //                 $('#account_' + row_id).attr('required', true);
      //             } else {
      //                 $('#account_id').empty().append(result);
      //                 $('#account_id').attr('required', true);
      //             }
                  
                  
      //         },
      //     });
          
      // }

      // if ($.isNumeric(payment_type) || payment_type == 'cash') {

      //     $.ajax({

      //         method: 'get',

      //         url: '/accounting-module/get-account-group-name-dp',

      //         data: { group_name: 'Cash Account', location_id: location_id },

      //         contentType: 'html',

      //         success: function(result) {
                  
                  
      //             if (row_id >= 0) {

      //                 $('#account_' + row_id)

      //                 .empty()

      //                 .append(result);

      //                 check_insufficient_balance_row(row_id);

      //             } else {

      //                 $('#account_id').empty().append(result);

      //                 $('#account_id')

      //                 .val($('#account_id option:contains("Cash")').val())

      //                 .trigger('change');

      //             }

      //         },

      //     });

      // }

      // if (payment_type == 'card') {

      //     $('.card_type_div').removeClass('hide');

      //     $.ajax({

      //         method: 'get',

      //         url: '/accounting-module/get-account-group-name-dp',

      //         data: { group_name: 'Card', location_id: location_id },

      //         contentType: 'html',

      //         success: function(result) {

      //             if (row_id >= 0) {

      //                 $('#account_' + row_id)

      //                 .empty()

      //                 .append(result);

      //                 check_insufficient_balance_row(row_id);

      //             } else {

      //                 $('#account_id').empty().append(result);

      //                 $('#account_id')

      //                 .val(

      //                     $(

      //                         '#account_id option:contains("Cards (Credit Debit)  Account")'

      //                     ).val()

      //                 )

      //                 .trigger('change');

      //             }

      //         },

      //     });

      // }



      edit_cheque_date = $(this).closest('.payment_row').find('.payment_details_div').find('#payment_edit_cheque').val();

      if (edit_cheque_date) {

          $(this).closest('.payment_row').find('.payment_details_div').find('.cheque_date').datepicker('setDate', edit_cheque_date);

      } else {
          $(this).closest('.payment_row').find('.payment_details_div').find('.cheque_date').datepicker('setDate', new Date());

      }

  });
  

  if ($('form#add_printer_form').length == 1) {

      printer_connection_type_field($('select#connection_type').val());

      $('select#connection_type').change(function() {

          var ctype = $(this).val();

          printer_connection_type_field(ctype);

      });

      $('form#add_printer_form').validate();

  }

  if ($('form#bl_receipt_setting_form').length == 1) {

      if ($('select#receipt_printer_type').val() == 'printer') {

          $('div#location_printer_div').removeClass('hide');

      } else {

          $('div#location_printer_div').addClass('hide');

      }

      $('select#receipt_printer_type').change(function() {

          var printer_type = $(this).val();

          if (printer_type == 'printer') {

              $('div#location_printer_div').removeClass('hide');

          } else {

              $('div#location_printer_div').addClass('hide');

          }

      });

      $('form#bl_receipt_setting_form').validate();

  }

  $(document).on('click', 'a.pay_purchase_due, a.pay_sale_due', function(e) {

      e.preventDefault();

      $.ajax({

          url: $(this).attr('href'),

          dataType: 'html',

          success: function(result) {

              $('.pay_contact_due_modal').html(result).modal('show');

              __currency_convert_recursively($('.pay_contact_due_modal'));

              $('#paid_on').datetimepicker({

                  format: moment_date_format + ' ' + moment_time_format,

                  ignoreReadonly: true,

              });

              $('.pay_contact_due_modal').find('form#pay_contact_due_form').validate();

          },

      });

  });

  $('#view_todays_profit').click(function() {

      $('#todays_profit_modal').modal('show');

  });

  $('#todays_profit_modal').on('shown.bs.modal', function() {

      var start = $('#modal_today').val();

      var end = start;

      var location_id = '';

      updateProfitLoss(start, end, location_id);

  });

  $(document).on('click', 'a.print-invoice', function(e) {

      e.preventDefault();

      var href = $(this).data('href');

      $.ajax({

          method: 'GET',

          url: href,

          dataType: 'json',

          success: function(result) {

              if (result.success == 1 ) {
                  const {printer_type} = result.receipt
                  
                  if(printer_type == 'browser' || printer_type == "" || printer_type == undefined)
                  {
                      // $('#receipt_section').html(result.receipt.html_content);

                      // __currency_convert_recursively($('#receipt_section'));
                      
                      _print_invoice(result.receipt);    
                  }
                  else {
                      
                      const {html_content, printer_config} = result.receipt
                      console.log(result.receipt);
                      $('#receipt_section').html("");
                      $('#receipt_section').html(html_content);
                      __currency_convert_recursively($('#receipt_section'));
                      var w = window.open('', '_self');
                      var html = document.getElementById("receipt_section").innerHTML;
                      $(w.document.body).html(html);

                      var temp = printer_config.design.split('-');

                      var options = {
                          filename: 'output.pdf',
                          html2canvas: { scale: 2 },
                          disableCss: true,

                          jsPDF: { unit: 'mm', format: temp[0], orientation: temp[1] },
                          margin: 0
                      };
                      html2pdf().set(options).from(document.body).save();
                      
                      //==============jsPDF====================
                      // const {jsPDF} = window.jspdf;
                      // const doc = new jsPDF({
                      //     margins: 0,
                      //     orientation: 'l',
                      //     unit: 'in',
                      //     format: 'a5',
                      // });
                      // html2canvas(document.body).then(function(canvas) {
                      //     var imgData = canvas.toDataURL('image/png');
                      //     var imgProps = doc.getImageProperties(imgData);
                      //     var pdfWidth = doc.internal.pageSize.getWidth();
                      //     var pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                      //     doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                      //     doc.autoPrint();
                      //     doc.save('output.pdf');
                      //   });
                      //==============jsPDF====================
                      location.reload();
                  }
              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  var sales_commission_agent_table = $('#sales_commission_agent_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/sales-commission-agents',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

      columns: [

          { data: 'full_name' },

          { data: 'email' },

          { data: 'contact_no' },

          { data: 'address' },

          { data: 'commission_type' },

          { data: 'cmmsn_percent' },

          { data: 'cmmsn_application' },

          { data: 'action' },

      ],

  });

  $('div.commission_agent_modal').on('shown.bs.modal', function(e) {

      $('form#sale_commission_agent_form')

      .submit(function(e) {

          e.preventDefault();

      })

      .validate({

          submitHandler: function(form) {

              e.preventDefault();

              var data = $(form).serialize();

              $.ajax({

                  method: $(form).attr('method'),

                  url: $(form).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.commission_agent_modal').modal('hide');

                          toastr.success(result.msg);

                          sales_commission_agent_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          },

      });

  });

  $(document).on('click', 'button.delete_commsn_agnt_button', function() {

      swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

          (willDelete) => {

              if (willDelete) {

                  var href = $(this).data('href');

                  var data = $(this).serialize();

                  $.ajax({

                      method: 'DELETE',

                      url: href,

                      dataType: 'json',

                      data: data,

                      success: function(result) {

                          if (result.success == true) {

                              toastr.success(result.msg);

                              sales_commission_agent_table.ajax.reload();

                          } else {

                              toastr.error(result.msg);

                          }

                      },

                  });

              }

          }

      );

  });

  $('button#full_screen').click(function(e) {

      element = document.documentElement;

      if (screenfull.enabled) {

          screenfull.toggle(element);

      }

  });

  var customer_groups_table = $('#customer_groups_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/contact-group?type=customer',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });

  $(document).on('submit', 'form#contact_group_add_form', function(e) {

      e.preventDefault();

      var data = $(this).serialize();

      $.ajax({

          method: 'POST',

          url: $(this).attr('action'),

          dataType: 'json',

          data: data,

          success: function(result) {

              if (result.success == true) {

                  $('div.contact_groups_modal ').modal('hide');

                  toastr.success(result.msg);

                  customer_groups_table.ajax.reload();

                  supplier_groups_table.ajax.reload();

              } else {

                  toastr.error(result.msg);

              }

          },

      });

  });

  $(document).on('click', 'button.edit_contact_group_button', function() {

      $('div.contact_groups_modal ').load($(this).data('href'), function() {

          $(this).modal('show');

          $('form#contact_group_edit_form').submit(function(e) {

              e.preventDefault();

              var data = $(this).serialize();

              $.ajax({

                  method: 'POST',

                  url: $(this).attr('action'),

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          $('div.contact_groups_modal ').modal('hide');

                          toastr.success(result.msg);

                          customer_groups_table.ajax.reload();

                          supplier_groups_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          });

      });

  });

  $(document).on('click', 'button.delete_contact_group_button', function() {

      swal({

          title: LANG.sure,

          text: LANG.confirm_delete_customer_group,

          icon: 'warning',

          buttons: true,

          dangerMode: true,

      }).then((willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          customer_groups_table.ajax.reload();

                          supplier_groups_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      });

  });



  var supplier_groups_table = $('#supplier_groups_table').DataTable({

      processing: true,

      serverSide: true,

      ajax: '/contact-group?type=supplier',

      columnDefs: [{ targets: 2, orderable: false, searchable: false }],

  });



  $(document).on('click', '.delete-sale', function(e) {

      e.preventDefault();

      swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

          (willDelete) => {

              if (willDelete) {

                  var href = $(this).attr('href');

                  $.ajax({

                      method: 'DELETE',

                      url: href,

                      dataType: 'json',

                      success: function(result) {

                          if (result.success == true) {

                              toastr.success(result.msg);

                              if (typeof sell_table !== 'undefined') {

                                  sell_table.ajax.reload();

                              }

                              if (typeof get_recent_transactions !== 'undefined') {

                                  get_recent_transactions('final', $('div#tab_final'));

                                  get_recent_transactions('draft', $('div#tab_draft'));

                              }

                          } else {

                              toastr.error(result.msg);

                          }

                      },

                  });

              }

          }

      );

  });

  if ($('form#add_invoice_layout_form').length > 0) {

      $('select#design').change(function() {

          if ($(this).val() == 'columnize-taxes') {

              $('div#columnize-taxes').removeClass('hide');

              $('div#columnize-taxes').find('input').removeAttr('disabled', 'false');

          } else {

              $('div#columnize-taxes').addClass('hide');

              $('div#columnize-taxes').find('input').attr('disabled', 'true');

          }

      });

  }

  $(document).on('keyup', 'form#unit_add_form input#actual_name', function() {

      $('form#unit_add_form span#unit_name').text($(this).val());

  });

  $(document).on('keyup', 'form#unit_edit_form input#actual_name', function() {

      $('form#unit_edit_form span#unit_name').text($(this).val());

  });

  $('#user_dob').datepicker({ autoclose: true });

});

$('.quick_add_product_modal').on('shown.bs.modal', function() {

  $('.quick_add_product_modal')

  .find('.select2')

  .each(function() {

      var $p = $(this).parent();

      $(this).select2({ dropdownParent: $p });

  });

  $('.quick_add_product_modal')

  .find('input[type="checkbox"].input-icheck')

  .each(function() {

      $(this).iCheck({

          checkboxClass: 'icheckbox_square-blue',

          radioClass: 'iradio_square-blue',

      });

  });

});

discounts_table = $('#discounts_table').DataTable({

  processing: true,

  serverSide: true,

  ajax: base_path + '/discount',

  columnDefs: [{ targets: [0, 8], orderable: false, searchable: false }],

  aaSorting: [1, 'asc'],

  columns: [

      { data: 'row_select' },

      { data: 'name', name: 'discounts.name' },

      { data: 'starts_at', name: 'starts_at' },

      { data: 'ends_at', name: 'ends_at' },

      { data: 'priority', name: 'priority' },

      { data: 'brand', name: 'b.name' },

      { data: 'category', name: 'c.name' },

      { data: 'location', name: 'l.name' },

      { data: 'action', name: 'action' },

  ],

});

$('.discount_modal').on('shown.bs.modal', function() {

  $('.discount_modal')

  .find('.select2')

  .each(function() {

      var $p = $(this).parent();

      $(this).select2({ dropdownParent: $p });

  });

  $('.discount_modal')

  .find('input[type="checkbox"].input-icheck')

  .each(function() {

      $(this).iCheck({

          checkboxClass: 'icheckbox_square-blue',

          radioClass: 'iradio_square-blue',

      });

  });

  $('.discount_modal .discount_date').datetimepicker({

      format: moment_date_format + ' ' + moment_time_format,

      ignoreReadonly: true,

  });

  $('form#discount_form').validate();

});

$(document).on('submit', 'form#discount_form', function(e) {

  e.preventDefault();

  var data = $(this).serialize();

  $.ajax({

      method: $(this).attr('method'),

      url: $(this).attr('action'),

      dataType: 'json',

      data: data,

      success: function(result) {

          if (result.success == true) {

              $('div.discount_modal').modal('hide');

              toastr.success(result.msg);

              discounts_table.ajax.reload();

          } else {

              toastr.error(result.msg);

          }

      },

  });

});

$(document).on('click', 'button.delete_discount_button', function() {

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          discounts_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      }

  );

});

function printer_connection_type_field(ctype) {

  if (ctype == 'network') {

      $('div#path_div').addClass('hide');

      $('div#ip_address_div, div#port_div').removeClass('hide');

  } else if (ctype == 'windows' || ctype == 'linux') {

      $('div#path_div').removeClass('hide');

      $('div#ip_address_div, div#port_div').addClass('hide');

  }

}

function show_invoice_preview() {

  var prefix = $('#prefix').val();

  var start_number = $('#start_number').val();

  var total_digits = $('#total_digits').val();

  var preview = prefix + pad_zero(start_number, total_digits);

  $('#preview_format').text('#' + preview);

}

function pad_zero(str, max) {

  str = str.toString();

  return str.length < max ? pad_zero('0' + str, max) : str;

}

function get_sub_categories() {

  var cat = $('#category_id').val();

  $.ajax({

      method: 'POST',

      url: '/products/get_sub_categories',

      dataType: 'html',

      data: { cat_id: cat },

      success: function(result) {

          if (result) {

              $('#sub_category_id').html(result);

          }

      },

  });

}

function get_sub_units() {

  if ($('#sub_unit_ids').is(':visible')) {

      var unit_id = $('#unit_id').val();

      $.ajax({

          method: 'GET',

          url: '/products/get_sub_units',

          dataType: 'html',

          data: { unit_id: unit_id },

          success: function(result) {

              if (result) {

                  $('#sub_unit_ids').html(result);

              }

          },

      });

  }

}

function show_product_type_form() {

  if ($('#type').val() == 'combo') {

      $('#enable_stock').iCheck('uncheck');

      $('input[name="woocommerce_disable_sync"]').iCheck('check');

  }

  var action = $('#type').attr('data-action');

  var product_id = $('#type').attr('data-product_id');

  $.ajax({

      method: 'POST',

      url: '/products/product_form_part',

      dataType: 'html',

      data: { type: $('#type').val(), product_id: product_id, action: action },

      success: function(result) {

          if (result) {

              $('#product_form_part').html(result);

                if (typeof toggle_dsp_input === 'function') {
                    toggle_dsp_input();
                }

          }

      },

  });

}

function show_vat_product_type_form() {
  

  var action = $('#type').attr('data-action');

  var product_id = $('#type').attr('data-product_id');

  $.ajax({

      method: 'POST',

      url: '/vat-module/vat-products/product_form_part',

      dataType: 'html',

      data: { type: $('#type').val(), product_id: product_id, action: action },

      success: function(result) {

          if (result) {

              $('#product_form_part').html(result);

                if (typeof toggle_dsp_input === 'function') {
                    toggle_dsp_input();
                }

          }

      },

  });

}

$(document).on('click', 'table.ajax_view tbody tr', function(e) {

  if (

      !$(e.target).is('td.selectable_td input[type=checkbox]') &&

      !$(e.target).is('td.selectable_td') &&

      !$(e.target).is('td.clickable_td') &&

      !$(e.target).is('a') &&

      !$(e.target).is('button') &&

      !$(e.target).hasClass('label') &&

      !$(e.target).is('li') &&

      $(this).data('href') &&

      !$(e.target).is('i')

  ) {

      $.ajax({

          url: $(this).data('href'),

          dataType: 'html',

          success: function(result) {
              $('.view_modal').html(result).modal('show');

          },

      });

  }

});

$(document).on('click', 'td.clickable_td', function(e) {

  e.preventDefault();

  e.stopPropagation();

  if (e.target.tagName == 'SPAN') {

      return false;

  }

  var link = $(this).find('a');

  if (link.length) {

      if (!link.hasClass('no-ajax')) {

          var href = link.attr('href');

          var container = $('.payment_modal');

          $.ajax({

              url: href,

              dataType: 'html',

              success: function(result) {

                  $(container).html(result).modal('show');

                  __currency_convert_recursively(container);

              },

          });

      }

  }

});

$(document).on('click', 'button.select-all', function() {

  var this_select = $(this).closest('.form-group').find('select');

  this_select.find('option').each(function() {

      $(this).prop('selected', 'selected');

  });

  this_select.trigger('change');

});

$(document).on('click', 'button.deselect-all', function() {

  var this_select = $(this).closest('.form-group').find('select');

  this_select.find('option').each(function() {

      $(this).prop('selected', '');

  });

  this_select.trigger('change');

});

$(document).on('change', 'input.row-select', function() {

  if (this.checked) {

      $(this).closest('tr').addClass('selected');

  } else {

      $(this).closest('tr').removeClass('selected');

  }

});

$(document).on('click', '#select-all-row', function(e) {

  if (this.checked) {

      $(this)

      .closest('table')

      .find('tbody')

      .find('input.row-select')

      .each(function() {

          if (!this.checked) {

              $(this).prop('checked', true).change();

          }

      });

  } else {

      $(this)

      .closest('table')

      .find('tbody')

      .find('input.row-select')

      .each(function() {

          if (this.checked) {

              $(this).prop('checked', false).change();

          }

      });

  }

});

$(document).on('click', 'a.view_purchase_return_payment_modal', function(e) {

  e.preventDefault();

  e.stopPropagation();

  var href = $(this).attr('href');

  var container = $('.payment_modal');

  $.ajax({

      url: href,

      dataType: 'html',

      success: function(result) {

          $(container).html(result).modal('show');

          __currency_convert_recursively(container);

      },

  });

});

$(document).on('click', 'a.view_invoice_url', function(e) {

  e.preventDefault();

  $('div.view_modal').load($(this).attr('href'), function() {

      $(this).modal('show');

  });

  return false;

});

$(document).on('click', '.load_more_notifications', function(e) {

  e.preventDefault();

  var this_link = $(this);

  this_link.text(LANG.loading + '...');

  this_link.attr('disabled', true);

  var page = parseInt($('input#notification_page').val()) + 1;

  var href = '/load-more-notifications?page=' + page;

  $.ajax({

      url: href,

      dataType: 'html',

      success: function(result) {

          if ($('li.no-notification').length == 0) {

              $(result).insertBefore(this_link.closest('li'));

          }

          this_link.text(LANG.load_more);

          this_link.removeAttr('disabled');

          $('input#notification_page').val(page);

      },

  });

  return false;

});

$(document).on('click', 'a.load_notifications', function(e) {

  if (!$(this).data('loaded')) {

      e.preventDefault();

      $('li.load_more_li').addClass('hide');

      var this_link = $(this);

      var href = '/load-more-notifications?page=1';

      $('span.notifications_count').html(__fa_awesome());

      $.ajax({

          url: href,

          dataType: 'html',

          success: function(result) {

              $('ul#notifications_list').prepend(result);

              $('span.notifications_count').text('');

              this_link.data('loaded', true);

              $('li.load_more_li').removeClass('hide');

          },

      });

  }

});

$(document).on('click', 'a.delete_purchase_return', function(e) {

  e.preventDefault();

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (willDelete) => {

          if (willDelete) {

              var href = $(this).attr('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          purchase_return_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      }

  );

});

$(document).on('submit', 'form#types_of_service_form', function(e) {

  e.preventDefault();

  var data = $(this).serialize();

  $(this).find('button[type="submit"]').attr('disabled', true);

  $.ajax({

      method: $(this).attr('method'),

      url: $(this).attr('action'),

      dataType: 'json',

      data: data,

      success: function(result) {

          if (result.success == true) {

              $('div.type_of_service_modal').modal('hide');

              toastr.success(result.msg);

              types_of_service_table.ajax.reload();

          } else {

              toastr.error(result.msg);

          }

      },

  });

});

types_of_service_table = $('#types_of_service_table').DataTable({

  processing: true,

  serverSide: true,

  ajax: base_path + '/types-of-service',

  columnDefs: [{ targets: [3], orderable: false, searchable: false }],

  aaSorting: [1, 'asc'],

  columns: [

      { data: 'name', name: 'name' },

      { data: 'description', name: 'description' },

      { data: 'packing_charge', name: 'packing_charge' },

      { data: 'action', name: 'action' },

  ],

  fnDrawCallback: function(oSettings) {

      __currency_convert_recursively($('#types_of_service_table'));

  },

});

$(document).on('click', 'button.delete_type_of_service', function(e) {

  e.preventDefault();

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (willDelete) => {

          if (willDelete) {

              var href = $(this).data('href');

              var data = $(this).serialize();

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  data: data,

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          types_of_service_table.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      }

  );

});

$(document).on('submit', 'form#edit_shipping_form', function(e) {

  e.preventDefault();

  var data = $(this).serialize();

  $(this).find('button[type="submit"]').attr('disabled', true);

  $.ajax({

      method: $(this).attr('method'),

      url: $(this).attr('action'),

      dataType: 'json',

      data: data,

      success: function(result) {

          if (result.success == true) {

              $('div.view_modal').modal('hide');

              toastr.success(result.msg);

              sell_table.ajax.reload();

          } else {

              toastr.error(result.msg);

          }

      },

  });

});

$(document).on('show.bs.modal', '.register_details_modal, .close_register_modal', function() {

  __currency_convert_recursively($(this));

});

$('#close_register').click(function() {

  $('.close_register_modal').modal({ backdrop: 'static', keyboard: false });

});

function updateProfitLoss(start = null, end = null, location_id = null) {

  if (start == null) {

      var start = $('#profit_tabs_filter')

      .data('daterangepicker')

      .startDate.format('YYYY-MM-DD');

  }

  if (end == null) {

      var end = $('#profit_tabs_filter')

      .data('daterangepicker')

      .endDate.format('YYYY-MM-DD');

  }

  if (location_id == null) {

      var location_id = $('#profit_loss_location_filter').val();

  }

  var data = { start_date: start, end_date: end, location_id: location_id };

  var loader = __fa_awesome();

  var pl_span = $('span#pl_span');

  // @eng 9/2 2359 START
  pl_span.find('.opening_stock, .total_transfer_shipping_charges, .closing_stock, .total_sell, .total_purchase, .total_expense, .net_profit, .total_adjustment, .decrease_stock_adjustment, .increase_stock_adjustment, .total_recovered, .total_sell_discount, \.total_purchase_discount, .total_purchase_return, .total_sell_return, .gross_profit, \.total_reward_amount, .total_payroll, .profit_without_expense, .total_sales_on_cost ').html(loader);
  // @eng 9/2 2359 END

  $.ajax({

      method: 'GET',

      url: '/reports/profit-loss',

      dataType: 'json',

      data: data,

      success: function(data) {
          // var netProfit = (data.total_sell - parseFloat(data.total_sale_cost) - data.direct_expense_balance) - data.total_expense; // @eng 9/2 2359

          pl_span.find('.opening_stock').html(__currency_trans_from_en(data.opening_stock, true));

          pl_span.find('.closing_stock').html(__currency_trans_from_en(data.closing_stock, true));

          pl_span.find('.total_sell').html(__currency_trans_from_en(data.total_sell, true));

          pl_span

              .find('.total_purchase')

          .html(__currency_trans_from_en(data.total_purchase, true));

          pl_span.find('.total_expense').html(__currency_trans_from_en(data.total_expense, true));

          if ($('.total_payroll').length > 0) {

              pl_span

                  .find('.total_payroll')

              .html(__currency_trans_from_en(data.total_payroll, true));

          }

          if ($('.total_production_cost').length > 0) {

              pl_span

                  .find('.total_production_cost')

              .html(__currency_trans_from_en(data.total_production_cost, true));

          }

          pl_span.find('.net_profit').html(__currency_trans_from_en(data.gross_profit_3, true));

          // @eng START 9/2 2359
          pl_span

              .find('.profit_without_expense')

          // .html(__currency_trans_from_en((data.total_sell - data.total_sale_cost), true));
          .html(__currency_trans_from_en(data.gross_profit, true));

          pl_span.find('.gross_profit').html(__currency_trans_from_en(data.net_profit, true));
          // @eng END 9/2 2359

          pl_span

              .find('.total_adjustment')

          .html(__currency_trans_from_en(data.total_adjustment, true));

          pl_span

              .find('.decrease_stock_adjustment')

          .html(__currency_trans_from_en(data.decrease_stock_adjustment, true));

          pl_span

              .find('.increase_stock_adjustment')

          .html(__currency_trans_from_en(data.increase_stock_adjustment, true));

          pl_span

              .find('.total_sales_on_cost')

          // .html(__currency_trans_from_en(parseFloat(data.total_sale_cost), true));  // @eng 12/2
          .html(__currency_trans_from_en(parseFloat(data.total_sales_on_cost), true));  // @eng 12/2

          pl_span

              .find('.total_recovered')

          .html(__currency_trans_from_en(data.total_recovered, true));

          pl_span

              .find('.total_purchase_return')

          .html(__currency_trans_from_en(data.total_purchase_return, true));

          pl_span

              .find('.total_transfer_shipping_charges')

          .html(__currency_trans_from_en(data.total_transfer_shipping_charges, true));

          pl_span

              .find('.total_purchase_discount')

          .html(__currency_trans_from_en(data.total_purchase_discount, true));

          pl_span

              .find('.total_sell_discount')

          .html(__currency_trans_from_en(data.total_sell_discount, true));

          pl_span

              .find('.total_reward_amount')

          .html(__currency_trans_from_en(data.total_reward_amount, true));

          pl_span

              .find('.total_sell_return')

          .html(__currency_trans_from_en(data.total_sell_return, true));

          __highlight(data.net_profit, pl_span.find('.net_profit'));

          __highlight(data.net_profit, pl_span.find('.gross_profit'));

          __highlight(data.total_profit_by_product, pl_span.find('.profit_without_expense'));

      },

  });

}

$(document).on('click', 'button.activate-deactivate-location', function() {

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (willDelete) => {

          if (willDelete) {

              $.ajax({

                  url: $(this).data('href'),

                  dataType: 'json',

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          business_locations.ajax.reload();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      }

  );

});

$(document).on('click', '.delete-sale-suspend', function(e) {

  e.preventDefault();

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (willDelete) => {

          if (willDelete) {

              var href = $(this).attr('href');

              $.ajax({

                  method: 'DELETE',

                  url: href,

                  dataType: 'json',

                  success: function(result) {

                      if (result.success == true) {

                          toastr.success(result.msg);

                          (parts = href.split('/')), (last_part = parts[parts.length - 1]);

                          var parent_class = $('.sale-' + last_part)

                          .parent()

                          .prop('className');

                          number = $('.' + parent_class + ' .sale').length;

                          if (number == 1) {

                              $('.sale-' + last_part)

                              .parent()

                              .append('<p class="text-center">No records found</p>');

                          }

                          $('.sale-' + last_part).remove();

                      } else {

                          toastr.error(result.msg);

                      }

                  },

              });

          }

      }

  );

});

$('#p_date_of_birth').datepicker({ autoclose: true, endDate: 'today' });

$('#prescription_date').datepicker({ autoclose: true, endDate: 'today' });

$('#pharmacy_date').datepicker({ autoclose: true, endDate: 'today' });

$('#laboratory_date').datepicker({ autoclose: true, endDate: 'today' });

function getDocAndNoteIndexPage() {

  var notable_type = $('#notable_type').val();

  var notable_id = $('#notable_id').val();

  $.ajax({

      method: 'GET',

      dataType: 'html',

      url: '/get-document-note-page',

      async: false,

      data: { notable_type: notable_type, notable_id: notable_id },

      success: function(result) {

          $('.document_note_body').html(result);

      },

  });

}

$(document).on('click', '.docs_and_notes_btn', function() {

  var url = $(this).data('href');

  $.ajax({

      method: 'GET',

      dataType: 'html',

      url: url,

      success: function(result) {

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

      success: function(file, response) {

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

$(document).on('submit', 'form#docus_notes_form', function(e) {

  e.preventDefault();

  var url = $('form#docus_notes_form').attr('action');

  var method = $('form#docus_notes_form').attr('method');

  var data = $('form#docus_notes_form').serialize();

  $.ajax({

      method: method,

      dataType: 'json',

      url: url,

      data: data,

      success: function(result) {

          if (result.success) {

              $('.docus_note_modal').modal('hide');

              toastr.success(result.msg);

              documents_and_notes_data_table.ajax.reload();

          } else {

              toastr.error(result.msg);

          }

      },

  });

});

$(document).on('click', '#delete_docus_note', function(e) {

  e.preventDefault();

  var url = $(this).data('href');

  swal({ title: LANG.sure, icon: 'warning', buttons: true, dangerMode: true }).then(

      (confirmed) => {

          if (confirmed) {

              $.ajax({

                  method: 'DELETE',

                  dataType: 'json',

                  url: url,

                  success: function(result) {

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

$(document).on('click', '.view_a_docs_note', function() {

  var url = $(this).data('href');

  $.ajax({

      method: 'GET',

      dataType: 'html',

      url: url,

      success: function(result) {

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

          data: function(d) {

              d.notable_id = $('#notable_id').val();

              d.notable_type = $('#notable_type').val();

          },

      },

      columnDefs: [{ targets: [0, 2, 4], orderable: false, searchable: false }],

      aaSorting: [
          [3, 'asc']
      ],

      columns: [

          { data: 'action', name: 'action' },

          { data: 'heading', name: 'heading' },

          { data: 'createdBy' },

          { data: 'created_at', name: 'created_at' },

          { data: 'updated_at', name: 'updated_at' },

      ],

  });

}



function Insufficient_balance_swal() {

  swal({

      title: 'Transaction Not Allowed. Insufficient balance Amount',

      icon: 'error',

      buttons: true,

      dangerMode: true,

  });

}

$('#transaction_date_range_bill').daterangepicker({
                    singleDatePicker: false, // For selecting a single date
                    showDropdowns: true, // To show the dropdown for predefined date ranges
                    locale: {
                        format: 'YYYY-MM-DD', // Adjust the date format according to your needs
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Custom Date Range': [moment().startOf('month'), moment().endOf(
                            'month')], // Default custom date range (this can be modified)
                    }
                }, function(start, end, label) {
                    if (label === 'Custom Date Range') {
                        // Show the modal for manual input
                        $('.custom_date_typing_modal').modal('show');
                        // $('.custom_date_typing_modal').modal('show'); // Uncomment if needed
                    }else if (!start.isSame(end, 'day')) {
                        // If it's a range, show as YYYY-MM-DD ~ YYYY-MM-DD
                        $('#transaction_date_range_bill').val(start.format('YYYY-MM-DD') + ' ~ ' + end.format('YYYY-MM-DD'));
                    } else {
                        // If it's a single date
                        $('#transaction_date_range_bill').val(start.format('YYYY-MM-DD'));
                    }
                });

                $('#custom_date_apply_button').on('click', function () {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);
                    let fullRange = formattedStartDate + ' ~ ' + formattedEndDate;

                    // === Update #9c_date_range if it exists ===
                    if ($('#transaction_date_range_bill').length) {
                        $('#transaction_date_range_bill').val(fullRange);
                        $('#transaction_date_range_bill').data('daterangepicker').setStartDate(moment(startDate));
                        $('#transaction_date_range_bill').data('daterangepicker').setEndDate(moment(endDate));
                    }
                    // Hide the modal
                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });

 $('#manual_bill_date_range').on('change', function() {
    getManualBillTableData();   
 });

$('#manual_bill_date_range').daterangepicker({
    ...dateRangeSettings,  // include your existing settings
    startDate: moment().startOf('month'),
    endDate: moment().endOf('month')
}, function(start, end) {
    $('#manual_bill_date_range').val(
        start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
    );

    if($("#expense_table")){
        expense_table.ajax.reload();
    }

    if($("#vat_expense_table")){
        vat_expense_table.ajax.reload();
    }
});

    $('#custom_date_apply_button').on('click', function() {
        if($('#target_custom_date_input').val() == "manual_bill_date_range"){
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#manual_bill_date_range').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#manual_bill_date_range').data('daterangepicker').setStartDate(moment(startDate));
                $('#manual_bill_date_range').data('daterangepicker').setEndDate(moment(endDate));
                
                if($("#expense_table")){
                    expense_table.ajax.reload();
                }
                if($("#vat_expense_table")){
                    vat_expense_table.ajax.reload();
                }

                $('.custom_date_typing_modal').modal('hide');
            } else {
                alert("Please select both start and end dates.");
            }
        }
    });
    $('#manual_bill_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('#target_custom_date_input').val('manual_bill_date_range');
            $('.custom_date_typing_modal').modal('show');
        }
    });

    $('#transaction_date_range').on('apply.daterangepicker', function(ev, picker) {
        if (picker.chosenLabel === 'Custom Date Range') {
            $('.custom_date_typing_modal').modal('show');
        }
    });
    $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#transaction_date_range').html(
            '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
        );
    });

  $('#manual_bill_date_range').on('cancel.daterangepicker', function(ev, picker) {

      $('#product_sr_date_filter').val('');

      if($("#expense_table")){
          expense_table.ajax.reload();
      }
      
      
      if($("#vat_expense_table")){
          vat_expense_table.ajax.reload();
      }

  });


  $('#customer_dropdown').on('change', function () {
    getManualBillTableData();
  });


function getManualBillTableData(){
    var customerId = $('#customer_dropdown').val(); // Get the selected customer ID
    var dateRange = $('#manual_bill_date_range').val(); // Assuming this is the input field
    var dates = dateRange.split(' - ');
    var startDate = dates[0];
    var endDate = dates[1];

    if (customerId && startDate && endDate) {
        $.ajax({
            method: 'POST',
            url: '/manual-bill/get-table-data',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                customer_id: customerId,
                start_date: startDate,
                end_date: endDate
            },
            success: function (result) {
                if (result.customer_name) {
                    $('#customer_name_display').val(result.customer_name);
                }

                // 🟢 Dynamically populate Settlement No dropdown
                if (result.settlement_numbers && result.settlement_numbers.length > 0) {
                    let options = '<option value="">Select Settlement No</option>';
                    result.settlement_numbers.forEach(function(settlementNo) {
                        options += `<option value="${settlementNo}">${settlementNo}</option>`;
                    });

                    $('#settlement_dropdown').html(options).prop('disabled', false).trigger('change');
                } else {
                    $('#settlement_dropdown').html('<option value="">No Settlement Found</option>').prop('disabled', false).trigger('change');
                }

                // 🟢 Dynamically populate order No dropdown
                if (result.order_numbers && result.order_numbers.length > 0) {
                    let options = '<option value="">Select Order No</option>';
                    result.order_numbers.forEach(function(orderNo) {
                        options += `<option value="${orderNo}">${orderNo}</option>`;
                    });

                    $('#order_dropdown').html(options).prop('disabled', false).trigger('change');
                } else {
                    $('#order_dropdown').html('<option value="">No Order Found</option>').prop('disabled', false).trigger('change');
                }

                // 🟢 Dynamically populate vehicle No dropdown
                if (result.customer_references && result.customer_references.length > 0) {
                    let options = '<option value="">Select Vehicle No</option>';
                    result.customer_references.forEach(function(vehicleNo) {
                        options += `<option value="${vehicleNo}">${vehicleNo}</option>`;
                    });

                    $('#vehicle_dropdown').html(options).prop('disabled', false).trigger('change');
                } else {
                    $('#vehicle_dropdown').html('<option value="">No vehicle Found</option>').prop('disabled', false).trigger('change');
                }

                // 🟢 Dynamically populate product dropdown with id => name
                if (result.products_name && result.products_name.length > 0) {
                    let options = '<option value="">Select product</option>';
                    result.products_name.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name}</option>`;
                    });

                    $('#product_dropdown').html(options).prop('disabled', false).trigger('change');
                } else {
                    $('#product_dropdown').html('<option value="">No product Found</option>').prop('disabled', false).trigger('change');
                }

                let rows = '';
                result.payment_data.forEach(function (item, index) {
                    if (item.transaction_date && item.settlement_no) {
                        const orderDate = item.created_at ? item.created_at.split('T')[0].split(' ')[0] : '';
                        rows += `
                            <tr>
                                <td>${item.transaction_date}</td>
                                <td>${item.settlement_no}</td>
                                <td>${item.order_number ?? ''}</td>
                                <td>
                                    <input type="checkbox" class="form-check-input single-check" 
                                        data-index="${index}" 
                                        data-transaction-date="${item.transaction_date ?? ''}" 
                                        data-settlement-no="${item.settlement_no ?? ''}" 
                                        data-order-no="${item.order_number ?? ''}"
                                        data-order-date="${orderDate}"
                                        data-vehicle-no="${item.customer_reference}"
                                        data-product="${item.name}"
                                        data-product-id="${item.id}"
                                        data-product-unit-price="${item.unit_price}"
                                        data-settlement-amount="${item.settlement_amount}">
                                </td>
                            </tr>
                        `;
                    }
                });

                $('#payment_data_table tbody').html(rows);

                $('input[name="transaction_date_range"]').val('').prop('disabled', false);
                $('#settlement_dropdown').val('').prop('disabled', false);
                $('input[name="order_no"]').val('').prop('disabled', false);
                $('input[name="order_date"]').val('').prop('disabled', false);
                $('input[name="vehicle_no"]').val('').prop('disabled', false);
                const settlementNumbers = $(this).data('settlement-numbers');
                const orderNumbers = $(this).data('order_numbers');
                const customerReferences = $(this).data('customer_reference');
                const products = $(this).data('product');



                $('#settlement_dropdown').val(settlementNumbers).trigger('change').prop('disabled', false);
                $('#order_dropdown').val(orderNumbers).trigger('change').prop('disabled', false);
                $('#vehicle_dropdown').val(customerReferences).trigger('change').prop('disabled', false);
                $('#product_dropdown').val(products).trigger('change').prop('disabled', false);


                // ✅ Only allow one checkbox to be selected & populate fields
                $('.single-check').on('change', function () {
                    $('.single-check').not(this).prop('checked', false);

                    if ($(this).is(':checked')) {
                        // Get the data attributes from the checkbox
                        const transactionDate = $(this).data('transaction-date');
                        const settlementNo = $(this).data('settlement-no');
                        const orderNo = $(this).data('order-no');
                        const orderDate = $(this).data('order-date');
                        const vehicleNo = $(this).data('vehicle-no');
                        const product = $(this).data('product');
                        const productId = $(this).data('product-id');
                        const productUnitPrice = $(this).data('product-unit-price');
                        const settlementAmount = $(this).data('settlement-amount');

                        // Get customer references and convert to comma-separated string
                        // const referenceString = customerReferences.map(ref => ref.reference).join(',');

                        // Populate form fields
                              // Populate and disable form fields
                        $('input[name="transaction_date_range"]').val(transactionDate).prop('disabled', true);
                        // Ensure the option exists in the dropdown
                        if ($('#settlement_dropdown option[value="' + settlementNo + '"]').length === 0) {
                            // Add the missing option dynamically
                            const newOption = new Option(settlementNo, settlementNo, true, true);
                            $('#settlement_dropdown').append(newOption).trigger('change');
                        } else {
                            // If option exists, just set it
                            $('#settlement_dropdown').val(settlementNo).trigger('change');
                        }

                        // Disable after selection
                        $('#settlement_dropdown').prop('disabled', true);
                        
                        if ($('#order_dropdown option[value="' + orderNo + '"]').length === 0) {
                            // Add the missing option dynamically
                            const newOption = new Option(orderNo, orderNo, true, true);
                            $('#order_dropdown').append(newOption).trigger('change');
                        } else {
                            // If option exists, just set it
                            $('#order_dropdown').val(orderNo).trigger('change');
                        }

                        // Disable after selection
                        $('#order_dropdown').prop('disabled', true);

                        $('input[name="order_date"]').val(orderDate).prop('disabled', true);
                        // $('input[name="vehicle_no"]').val(referenceString).prop('disabled', true);
                        if ($('#vehicle_dropdown option[value="' + vehicleNo + '"]').length === 0) {
                            // Add the missing option dynamically
                            const newOption = new Option(vehicleNo, vehicleNo, true, true);
                            $('#vehicle_dropdown').append(newOption).trigger('change');
                        } else {
                            // If option exists, just set it
                            $('#vehicle_dropdown').val(vehicleNo).trigger('change');
                        }

                        // Disable after selection
                        $('#vehicle_dropdown').prop('disabled', true);

                        if ($('#product_dropdown option[value="' + productId + '"]').length === 0) {
                            // Add the missing option dynamically
                            const newOption = new Option(product, productId, true, true);
                            $('#product_dropdown').append(newOption).trigger('change');
                        } else {
                            // If option exists, just set it
                            $('#product_dropdown').val(productId).trigger('change');
                        }

                        $('input[name="unit_price"]').val(parseFloat(productUnitPrice).toFixed(2));
                        $('input[name="settlement_amount"]').val(parseFloat(settlementAmount).toFixed(2));
                        $('input[name="qty"]').val(1);
                        $('input[name="total"]').val(parseFloat(productUnitPrice).toFixed(2));


                    } else {
                        // Clear form fields if unchecked
                          // Clear and enable form fields
                        $('input[name="transaction_date_range"]').val('').prop('disabled', false);
                        $('#settlement_dropdown').val('').prop('disabled', false);
                        $('input[name="order_no"]').val('').prop('disabled', false);
                        $('input[name="order_date"]').val('').prop('disabled', false);
                        $('#settlement_dropdown').val('').trigger('change').prop('disabled', false);
                        $('#vehicle_dropdown').val('').trigger('change').prop('disabled', false);
                        $('#order_dropdown').val('').trigger('change').prop('disabled', false);
                        $('input[name="unit_price"]').val('');
                        $('input[name="settlement_amount"]').val('');
                        $('input[name="total"]').val('');
                        $('input[name="qty"]').val('');


                    }
                });
            },
            error: function () {
                console.error('Failed to fetch customer details.');
            }
        });
    }
}


$('#manual_bill_form').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const actionUrl = form.attr('action');

    $.ajax({
        url: actionUrl,
        method: 'POST',
        data: form.serialize(),
        success: function (response) {
            toastr.success("Form saved successfully.");
            
            // ✅ Clear all fields
            form.trigger('reset');

            // ✅ Clear Select2 fields (if used)
            form.find('select.select2').val(null).trigger('change');
        },
        error: function (xhr) {
            toastr.error("Error saving data");
        }
    });
});



$(document).on('change', '.sub_unit', function() {

  multiplier = $(this).data('multiplier');

  table = $(this).closest('table');

  table_id = table.attr('id');

  tr = $(this).closest('tr');

  var multiplier = 1;

  var unit_name = '';

  var unit_price = 0;

  var purchase_price = 0;

  var current_stock = 0;

  var total_sold = 0;

  var current_stock_price = 0;

  var total_sold_value = 0;

  var quantity = tr.find('select.quantity').length; // in items report

  var purchase_qty = tr.find('select.purchase_qty').length; // in product purchase report

  var sell_qty = tr.find('select.sell_qty').length; // in product sell report

  var sub_unit_length = tr.find('select.sub_unit').length;

  if (sub_unit_length > 0) {

      var select = tr.find('select.sub_unit');

      multiplier = parseFloat(select.find(':selected').data('multiplier'));

      unit_name = select.find(':selected').data('unit_name');

      unit_price = parseFloat(select.find(':selected').data('unit_price'));

      purchase_price = tr.find('span.purchase_price').data('orig-value');

      current_stock = tr.find('span.current_stock').data('orig-value');

      total_sold = tr.find('span.total_sold').data('orig-value');

      total_sold_value = tr.find('span.total_sold_value').data('orig-value');

      current_stock_price = tr.find('span.current_stock_price').data('orig-value');

      quantity = tr.find('span.quantity').data('orig-value');

      purchase_qty = tr.find('span.purchase_qty').data('orig-value');

      sell_qty = tr.find('span.sell_qty').data('orig-value');

  }

  if (unit_price > 0) {

      tr.find('span.selling_price').text(__currency_trans_from_en(unit_price, true));

  } else {

      selling_price = tr.find('span.selling_price').data('orig-value');

      new_selling_price = selling_price * multiplier;

      tr.find('span.selling_price').text(__currency_trans_from_en(new_selling_price, true));

  }

  if (purchase_price > 0) {

      new_purchase_price = purchase_price * multiplier;

      tr.find('span.purchase_price').text(__currency_trans_from_en(new_purchase_price, true));

  }

  if (current_stock > 0) {

      new_current_stock = current_stock / multiplier;

      tr.find('span.current_stock').text(__currency_trans_from_en(new_current_stock, false));

  }

  if (total_sold > 0) {

      new_total_sold = total_sold / multiplier;

      tr.find('span.total_sold').text(__currency_trans_from_en(new_total_sold, false));

  }

  if (current_stock_price > 0) {

      new_current_stock_price = current_stock_price / multiplier;

      tr.find('span.current_stock_price').text(__currency_trans_from_en(new_current_stock_price, true));

  }

  if (total_sold_value > 0) {

      new_total_sold_value = total_sold_value / multiplier;

      tr.find('span.total_sold_value').text(__currency_trans_from_en(new_total_sold_value, true));

  }

  if (quantity > 0) {

      new_quantity = quantity / multiplier;

      tr.find('span.quantity').text(__currency_trans_from_en(new_quantity, false));

  }

  if (purchase_qty > 0) {

      new_purchase_qty = purchase_qty / multiplier;

      tr.find('span.purchase_qty').text(__currency_trans_from_en(new_purchase_qty, false));

  }

  if (sell_qty > 0) {

      new_sell_qty = sell_qty / multiplier;

      tr.find('span.sell_qty').text(__currency_trans_from_en(new_sell_qty, false));

  }

  tr.find('span.unit_name').text(unit_name);



})