@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Traiter une demande de congé</h2>

    <form action="{{ route('conges.update', $conge->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('conges.form')

        <div class="mb-3">
            <label>Statut</label>
            <select name="statut" class="form-control">
                <option value="en attente" {{ $conge->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                <option value="accepté" {{ $conge->statut == 'accepté' ? 'selected' : '' }}>Accepté</option>
                <option value="refusé" {{ $conge->statut == 'refusé' ? 'selected' : '' }}>Refusé</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Commentaire</label>
            <textarea name="commentaire" class="form-control">{{ old('commentaire', $conge->commentaire ?? '') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
