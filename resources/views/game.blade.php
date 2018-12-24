@extends('layouts.app')

@section('title', 'Игра')


@section('content')
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
    <game :game="{{ $game }}"
          :player="{{ $player }}"
          :field="{{ $field }}"
          :who-moves="{{ $who_moves }}"
          :question="{{ $question }}"
          :competitive-box="{{ $competitive_box }}"
          :user-colors="{{ $user_colors }}"
          :winner="{{ $winner }}"
          :messages="{{ $messages }}">
    </game>
@endsection
