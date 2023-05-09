<x-mail::message>
# Compte refusé

Monsieur/Madame {{$user->name}}
votre demande de compte a été malheureusement refusé le {{$user->updated_at}}

{{-- <x-mail::button >
Button Text
</x-mail::button> --}}
<br>
{{ config('app.name') }}
</x-mail::message>
