@extends('layouts.app')

@section('title', 'Игровой зал')


@section('content')
    <div class="container">
        <notifications
                :duration="5000"
                animation-name="v-fade-left"
                position="top left">
            <template slot="body" slot-scope="props">
                <div class="custom-template">
                    <div class="custom-template-icon">
                        <i class="icon ion-android-checkmark-circle"></i>
                    </div>
                    <div class="custom-template-content">
                        <div class="custom-template-title">
                            @{{props.item.title}}
                            <div class="custom-template-text"
                                 v-html="props.item.text"></div>
                        </div>
                        <div class="custom-template-close"
                             @click="props.close">
                            <i class="icon ion-android-close"></i>
                        </div>
                    </div>
                </div>
            </template>
        </notifications>
        <div class="row">
            <div class="col-12 col-md-6">
                <chat-messages :game_id="0" :messages="{{ $messages }}"></chat-messages>
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
