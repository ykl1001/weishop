@extends('error._layouts.base')
@section('content')
    <div class="content" id='error'>
        <div class="x-null pa w100 tc y-null404">
            <div class="y-404">成功</div>
            <p class="f12 c-gray mt10 bold">操作成功</p>
            <a href="{{ u('/') }}" class="button button-light">回首页</a>
            <a href="{{u($c.'/'.$a)}}" class="button button-light">重新加载</a>
        </div>
    </div>
@stop
