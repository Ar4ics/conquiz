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

                <a href="/games" class="btn btn-primary">Игровой зал</a>
            </div>
        </div>
    </div>
@endsection
