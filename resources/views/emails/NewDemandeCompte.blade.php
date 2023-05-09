<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande de Compte créée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        h1 {
            color: #0b2c4e;
            font-size: 24px;
            margin-top: 0;
        }

        p {
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
        }

        .button {
            margin-top: 15px;
            display: inline-block;
            background-color: #0b2c4e;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/LogoNAFTAL.png') }}" alt="Naftal Logo">
        </div>
        <h1>Naftal Inventory Management</h1>
        <p>Monsieur/Madame {{ $user->name }},</p>
        <p>Une nouvelle demande de compte a été créée.</p>
        <p>
            Veuillez vous se rendre sur la page de connexion en cliquant sur le lien ci-dessous :
            <a href="{{ url('/api/login') }}" class="button">Connectez-vous</a>
        </p>
    </div>
</body>
</html>


