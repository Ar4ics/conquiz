@extends('layouts.app')

@section('title', 'Игровой зал')


@section('content')
    <div class="container">
        <notifications position="top center"></notifications>
        <div class="row">
            <div class="col-12 col-md-6">
                <chat-messages :game_id="0" :title="'Общий чат'"></chat-messages>
            </div>
            <div class="col-12 col-md-6">
                <create-game :initial-users="{{ $users }}"></create-game>
            </div>
        </div>

        <div class="row">
            <games :initial-games="{{ $games }}" :user="{{ Auth::user() }}"></games>
        </div>
    </div>
@endsection
