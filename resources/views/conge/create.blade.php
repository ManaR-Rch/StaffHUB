@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demander un congé</h2>

    <form action="{{ route('conges.store') }}" method="POST">
        @csrf

        @include('conges.form')

        <button type="submit" class="btn btn-success">Envoyer la demande</button>
    </form>
</div>
@endsection
