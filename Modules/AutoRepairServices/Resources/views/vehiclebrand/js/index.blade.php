@section('javascript')
<script type="text/javascript">
	$(document).ready(function(){
	    listContent = new Vue({
        el: "#content",
        data: {
            error: {
                hasError: false,
                message: "",
            },
        },
        methods: {
            /**
             * Datatable
             */
            tableInit() {
                dataTableInit();
            },
            onAddSubmit(e) {
                saveData();
                e.preventDefault();
            },
            populateOnEdit(id) {
                $.ajax({
                    type: "GET",
                    url: "/vehicle-brand/populateData",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    data: {
                        id: id
                    },
                    success: function (msg) {
                        if (msg) {
                            $("input[name='vehicle_brand']").val(msg.vehicle_brand);
                            $("input[name='vehicle_type']").val(msg.vehicle_type);
                            $("input[name='vehicle_model']").val(msg.vehicle_model);
                            $("input[name='chassis_no']").val(msg.chassis_no);
                            $("input[name='mod-id']").val(msg.id);
                        }
                    },
                });
            },
            deleteRowData(id) {
                $.ajax({
                    type: "GET",
                    url: "/vehicle-brand/deleteData",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    data: {
                        id: id
                    },
                    success: function (msg) {
                        swal(
                            msg.title,
                            msg.msg,
                            msg.swalState
                        );
                        listContent.tableInit();
                    },
                });
            }
        },
        mounted() {
            // Table Initialize
            this.tableInit();
        },
        });
	});
	
// 	Functions
function dataTableInit(args) {
    console.log("table initialize");
    $(".dt").DataTable().destroy();
    return $(".dt").DataTable({
        processing: true,
        serverSide: false,
        stateSave: true,
        // searching: false,
        pageLength: 10,
        order: [],
        ajax: {
            url: "/vehicle-brand/table",
            type: "GET",
        },
        columns: [
            { data: "vehicle_brand", orderable: false, className: "text-center" },
            { data: "vehicle_type" },
            { data: "vehicle_model" },
            { data: "chassis_no" },
            { data: "action", orderable: false, className: "text-center" },
        ],
        initComplete: function (settings, json) {

        },
    });
}
function saveData(id = 0) {
    return $.ajax({
        type: "GET",
        url: "/vehicle-brand/saveData",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        data: {
            id: id,
            args: $("#modalFrm").serialize()
        },
        success: function (msg) {
            swal(
                msg.title,
                msg.msg,
                msg.swalState
            );
            $("#closeModal").trigger('click');
            listContent.tableInit();
        },
    });
}
function swalConfirm(id) {
    swal({
        title: LANG.sure,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function (confirmed) {
        if (confirmed) {
            listContent.deleteRowData(id);
        }
    })
}

// JQUERY
$("#addBrandService").on("show.bs.modal", function (e) {
    if (typeof $(e.relatedTarget).data("id") !== "undefined") {
        listContent.populateOnEdit($(e.relatedTarget).data("id"));
        $(".modal-title").text("Edit");
    } else {
        $(".modal-title").text("Add");
    }
});

$("#addBrandService").on("hide.bs.modal", function (e) {
    $("#modalFrm")[0].reset();
});
</script>
@endsection