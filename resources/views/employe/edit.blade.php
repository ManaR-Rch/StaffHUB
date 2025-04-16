@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier l'employé</h2>

    <form action="{{ route('employes.update', $employe->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('employes.form')

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>
@endsection
