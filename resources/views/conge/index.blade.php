@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Liste des demandes de congés</h2>
    <a href="{{ route('conges.create') }}" class="btn btn-primary mb-3">Demander un congé</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Employé</th>
                <th>Type</th>
                <th>Dates</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($conges as $conge)
                <tr>
                    <td>{{ $conge->employe->nom }} {{ $conge->employe->prenom }}</td>
                    <td>{{ ucfirst($conge->type) }}</td>
                    <td>{{ $conge->date_debut }} → {{ $conge->date_fin }}</td>
                    <td>
                        <span class="badge 
                            {{ $conge->statut == 'accepté' ? 'bg-success' : ($conge->statut == 'refusé' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($conge->statut) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('conges.show', $conge->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('conges.edit', $conge->id) }}" class="btn btn-warning btn-sm">Traiter</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
