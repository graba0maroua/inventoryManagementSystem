<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infrastructure Localités PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Infrastructure Localités PDF </h1>

    <table>
        <thead>
            <tr>
                <th>N° localité</th>
                <th>Localité</th>
                <th>Nombre total d'inventaire</th>
                <th>Nombre d'inventaire scanné</th>
                <th>Nombre d'inventaire non scanné</th>
                <th>Pourcentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->locality_id }}</td>
                    <td>{{ $result->locality_name }}</td>
                    <td>{{ $result->total_count }}</td>
                    <td>{{ $result->scanned_count }}</td>
                    <td>{{ $result->not_scanned_count }}</td>
                    <td>{{ $result->percentage }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

