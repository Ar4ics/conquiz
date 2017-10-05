@extends('layouts.app')

@section('title', 'Конквистадор')


@section('content')
    <game :game-data="{{ $game }}" 
    :user="{{ Auth::user() }}" 
    :user-color="{{ $user_color }}"
    :status="{{ $status }}"
    :boxes="{{ $boxes }}">
    </game>
@endsection
