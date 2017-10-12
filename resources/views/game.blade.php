@extends('layouts.app')

@section('title', 'Конквистадор')


@section('content')
    <notifications></notifications>
    <game :game-data="{{ $game }}"
          :player="{{ $player }}"
          :boxes="{{ $boxes }}"
          :who-moves="{{ $who_moves }}"
          :initial-question="{{ $question }}"
    >
    </game>
@endsection
