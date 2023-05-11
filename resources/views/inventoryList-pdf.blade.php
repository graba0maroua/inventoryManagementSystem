<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> PDF Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Inventory List</h2>
    <table>
        <thead>
            <tr>

                <th>Location ID</th>
                <th>Location Name</th>
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Value</th>
                <th>Acquisition Date</th>
                <th>Code Bar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $item)
            <tr>
                <td>{{ $item->LOC_ID_INIT }}</td>
                <td>{{ $item->LOC_LIB_INIT }}</td>
                <td>{{ $item->AST_ID }}</td>
                <td>{{ $item->AST_LIB }}</td>
                <td>{{ $item->AST_VALBASE }}</td>
                <td>{{ $item->AST_DTE_ACQ }}</td>
                <td>{{ $item->code_bar }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
