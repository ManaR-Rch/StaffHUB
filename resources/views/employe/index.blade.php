@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Liste des employés</h2>
    <a href="{{ route('employes.create') }}" class="btn btn-primary mb-3">Ajouter un employé</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Poste</th>
                <th>Département</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employes as $employe)
                <tr>
                    <td>{{ $employe->numero }}</td>
                    <td>{{ $employe->nom }}</td>
                    <td>{{ $employe->prenom }}</td>
                    <td>{{ $employe->poste }}</td>
                    <td>{{ $employe->departement }}</td>
                    <td>{{ $employe->statut }}</td>
                    <td>
                        <a href="{{ route('employes.show', $employe->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('employes.edit', $employe->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('employes.destroy', $employe->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
