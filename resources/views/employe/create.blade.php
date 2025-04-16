@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Ajouter un nouvel employé</h2>

    <form action="{{ route('employes.store') }}" method="POST">
        @csrf

        @include('employes.form')

        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
