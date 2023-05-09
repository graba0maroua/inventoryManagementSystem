<x-mail::message>
# Nouvelle demande de compte crée
Monsieur/Madame ,
une nouvelle demande de compte a été créée le {{$user->created_at}} .
Veuillez vous se rendre sur la page de connexion en cliquant sur le lien ci-dessous :
<x-mail::button :url="$url" color="success">
Connectez vous
</x-mail::button>
<br>
{{ config('app.name') }}
</x-mail::message>
