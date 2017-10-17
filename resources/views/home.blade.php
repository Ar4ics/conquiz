@extends('layouts.app')

@section('content')
    <div class="container pt-5">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <button><a href="/games">Игровой зал</a></button>
            </div>
        </div>
    </div>
@endsection
