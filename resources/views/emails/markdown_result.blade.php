<x-mail::message>
# Compte accepté

Monsieur/Madame {{$user->name}}
votre demande de compte a été acceptée le {{$user->demandeCompte->updated_at}}
Veuillez vous se rendre sur votre compte en cliquant sur le lien ci-dessous :

<x-mail::button :url="$url" color="success">
Connectez vous
</x-mail::button>

<br>
{{ config('app.name') }}
</x-mail::message>
