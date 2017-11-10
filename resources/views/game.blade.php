@extends('layouts.app')

@section('title', 'Игра')


@section('content')
    <notifications position="top center"></notifications>
    <game :game-data="{{ $game }}"
          :player="{{ $player }}"
          :boxes="{{ $boxes }}"
          :who-moves="{{ $who_moves }}"
          :initial-question="{{ $question }}"
          :competitive-box="{{ $competitive_box }}"

    >
    </game>
@endsection
