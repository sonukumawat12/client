<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice: {{ $data['invoice_number'] }}</title>
    <style>
        /* Style your email content here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }

        /* Add more styles as needed */
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello!</h1>
        <p>The invoice number is: {{ $data['invoice_number'] }}</p>

    </div>
</body>
</html>
