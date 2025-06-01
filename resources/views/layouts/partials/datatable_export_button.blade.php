dom: '<"row margin-bottom-20 text-center"<"col-sm-12"B><"col-sm-7 text-align-start"l><"col-sm-5"f> r>tip',
buttons: [
    {
        extend: 'colvis',
        className: 'btn btn-sm btn-default',
        
    },{
        extend: 'csv',
        footer: true,
        text: '<i class="fa fa-file"></i> Export to CSV',
        className: 'btn btn-sm btn-default',
        exportOptions: {
            columns: function ( idx, data, node ) {
                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                    true : false;
            } 
        }
    },{
        extend: 'excel',
        footer: true,
        text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
        className: 'btn btn-sm btn-default',
        exportOptions: {
            columns: function ( idx, data, node ) {
                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                    true : false;
            } 
        }
    },{
        extend: 'pdf',
        footer: true,
        text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
        className: 'btn btn-sm btn-default',
        exportOptions: {
            columns: function ( idx, data, node ) {
                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                    true : false;
            } 
        }
    },{
        extend: 'print',
        footer: true,
        text: '<i class="fa fa-print"></i> Print',
        className: 'btn btn-sm btn-default',
        exportOptions: {
            columns: function ( idx, data, node ) {
                return $(node).is(":visible") && !$(node).hasClass('notexport') ?
                    true : false;
            } 
        },
        customize: function (win) {
            $(win.document.body).find('h1').css('text-align', 'center');
            $(win.document.body).find('h1').css('font-size', '25px');
        },
    }
],