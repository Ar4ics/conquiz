@extends('layouts.app')

@section('title', 'Конквистадор')


@section('content')
    <game :game-data="{{ $game }}" :user="{{ Auth::user() }}"></game>
@endsection
