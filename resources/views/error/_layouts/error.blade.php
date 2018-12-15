@extends('error._layouts.base')
@section('content')
    <div class="content" id='error'>
        <div class="x-null pa w100 tc y-null404">
            <div class="y-404">404</div>
            <p class="f12 c-gray mt10 bold">亲，您要找的页面走丢了哦</p>
            <a href="{{ u('/') }}" class="button button-light">回首页</a>
            <a href="javascript:funCreload();" class="button button-light">重新加载</a>
        </div>
    </div>

    <script type="text/javascript">
        funCreload = function(){
            location.reload();
        }
    </script>
@stop