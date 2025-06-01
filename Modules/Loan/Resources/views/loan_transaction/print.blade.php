<html lang="en">

<head>
    <title>{{ trans_choice('loan::general.transaction', 1) }} {{ trans_choice('core.detail', 2) }}</title>
    <style>
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
            display: table;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .pull-right {
            float: right !important;
        }

        span {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @include('loan::loan_transaction.partials.loan_transaction_body')
</body>
<script>
    window.print();
</script>

</html>
