@extends('layouts.app')

@section('title', 'Игровой зал')


@section('content')
<div class="container">
    <notifications position="top center"></notifications>
    <div class="row">
        <div class="col-md-6">
            <create-game :initial-users="{{ $users }}"></create-game>
        </div>
        <div class="col-md-6">
            <games :initial-games="{{ $games }}" :user="{{ Auth::user() }}"></games>
        </div>
    </div>
</div>
@endsection
