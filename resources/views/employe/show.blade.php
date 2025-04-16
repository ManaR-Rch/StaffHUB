@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Détails de l'employé</h2>

    <p><strong>Nom :</strong> {{ $employe->nom }}</p>
    <p><strong>Prénom :</strong> {{ $employe->prenom }}</p>
    <p><strong>Date de naissance :</strong> {{ $employe->date_naissance }}</p>
    <p><strong>Poste :</strong> {{ $employe->poste }}</p>
    <p><strong>Département :</strong> {{ $employe->departement }}</p>
    <p><strong>Date d’embauche :</strong> {{ $employe->date_embauche }}</p>
    <p><strong>Statut :</strong> {{ $employe->statut }}</p>
    <p><strong>Numéro :</strong> {{ $employe->numero }}</p>

    <a href="{{ route('employes.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
