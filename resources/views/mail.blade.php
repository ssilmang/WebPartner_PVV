<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apperçu des activités</title>
</head>
<body>
    <p>Bonjour {{ $request['name'] }}</p>
    <p>Voici vos coordonnées pour se connecter </p>
    <h4>Nom d'utilisateur : {{ $request['name'].'_'.$request['matricule'] }}</h4>
    <h4>Email : {{ $request['email'] }}</h4>
    <h4>mot de passe : {{ $request['password'] }}</h4>
    <h3>Si vous voulez Se connecter  click  <a href="http://localhost:4200/login">ici</a></h3>
    <h3>Si vous voulez changer votre mot de passe click  <a href="http://localhost:4200/changer/password">ici</a></h3>
</body>
</html>