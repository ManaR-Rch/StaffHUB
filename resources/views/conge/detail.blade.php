@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Détails du congé</h2>

    <p><strong>Employé :</strong> {{ $conge->employe->nom }} {{ $conge->employe->prenom }}</p>
    <p><strong>Type :</strong> {{ $conge->type }}</p>
    <p><strong>Période :</strong> {{ $conge->date_debut }} → {{ $conge->date_fin }}</p>
    <p><strong>Statut :</strong> {{ $conge->statut }}</p>
    <p><strong>Commentaire :</strong> {{ $conge->commentaire ?? 'Aucun' }}</p>

    <a href="{{ route('conges.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
