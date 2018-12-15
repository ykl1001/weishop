@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/img3.png') }}">
@stop
@section('right_content')
	<div class="main">
		<div class="x-shouc x-bor">
		</div>
		<div class="showbnt">
            <p class="mt20 tc">
                <span class="btn btn2">正在安装中...</span>
            </p>
            <p class="tc mt20 mb0">
                <input type="button" class="btn btn2 mr15" value="上一步" />
                <input type="submit" class="btn next" value="下一步" />
            </p>
        </div>
	</div>
@stop
@section('js')
<script>
    jQuery(function($){
        $.install = function(){
            $.post("{{u('Index/install')}}", $(this).serialize(), function(result){
                $.ShowAlert(result.msg);
            },'json');
        };
        $.install();
    });
</script>
@stop