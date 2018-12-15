@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 120px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:btn label="点击进入分销平台系统" click="$.manageurl()"></yz:btn>
	@yizan_end
@stop

@section('js')
	<script type="text/javascript">
		var manageurl = "{{$manageurl}}";
		$(function(){
			//点击进入
			$.manageurl = function(){
				window.open(manageurl);
			}
			//自动进入
			window.open(manageurl);
		})
	</script>
@stop