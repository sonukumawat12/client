<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<style>
    .swal-icon {
        width: 80px;
        height: 80px;
        border-width: 4px;
        border-style: solid;
        border-radius: 50%;
        padding: 0;
        position: relative;
        box-sizing: content-box;
        margin: 20px auto;
        
    }
    .swal-modal{
        font-family: Calibri, sans-serif !important;
    }
    .swal-text{
        color: red !important;
    }
    .swal-icon:first-child {
        margin-top: 32px;
    }

    .swal-icon--warning {
        border-color: #f8bb86;
        -webkit-animation: pulseWarning .75s infinite alternate;
        animation: pulseWarning .75s infinite alternate;
    }

    .swal-icon--info:after,
    .swal-icon--info:before {
        content: "";
        position: absolute;
        left: 50%;
        background-color: #f8bb86;
    }

    .swal-icon--info {
        border-color: #f8bb86;
    }

    .swal-button {
        color: #fff !important;
        background-color: #337ab7 !important;
        border-color: #2e6da4 !important;
    }
</style>
<script>
    function showPopup() {
        swal({
            title: "Warning!",
            text: "You have not logged into the System. Please first login to the system to view this page",
            icon: "info",
            buttons: {
                close: {
                    text: "Close",
                    value: null,
                    visible: true,
                    className: "btn btn-primary",
                    closeModal: true,
                },
            },
        });
    }

    // Trigger the popup on page load
    window.onload = showPopup;
</script>