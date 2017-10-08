@extends('layouts.app')

@section('title', 'Групповая беседа')


@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6">
            <create-game :initial-users="{{ $users }}"></create-game>
        </div>
        <div class="col-sm-6">
            <games :initial-games="{{ $games }}" :user="{{ Auth::user() }}"></games>
        </div>
    </div>
</div>
@endsection
