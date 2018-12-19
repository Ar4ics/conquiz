@extends('layouts.app')

@section('title', 'Игра')


@section('content')
    <notifications position="top center"></notifications>
    <game :game="{{ $game }}"
          :player="{{ $player }}"
          :field="{{ $field }}"
          :who-moves="{{ $who_moves }}"
          :question="{{ $question }}"
          :competitors="{{ $competitors }}"
          :user-colors="{{ $user_colors }}"
          :winner="{{ $winner }}"

    >
    </game>
@endsection
