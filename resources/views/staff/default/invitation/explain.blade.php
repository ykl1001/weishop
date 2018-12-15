@extends('staff.default._layouts.base')

@section('css')
<style type="text/css">
.y-gywm{min-height: 100%;padding: .5rem .5rem 0;}
</style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Invitation/index')}}','#invitation_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">说明</h1>
    </header>
@stop

@section('content')
    <div class="y-gywm bg_fff">
        <div class="lists_item_ajax">
            <p class="f14">{!! $explain !!}</p>
        </div>
    </div>
@stop

@section('show_nav')
@stop

@section('preloader')@stop

