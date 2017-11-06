@extends('layouts.app')

@section('title', 'Конквистадор')


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
