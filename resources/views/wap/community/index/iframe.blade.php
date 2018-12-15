@extends('wap.community._layouts.base')

@section('content')
<style type="text/css">
iframe#myiframe{
	border:0;
	width:100%;
	height:100%;
	overflow:scroll;
	margin:2.2rem 0 0 0;
}

</style>

<iframe src="{{$url}}" id="myiframe" frameborder="0" ></iframe>

@stop

@section($js)
<script type="text/javascript">
	$('a.pull-left')[0].href = "{{u('Index/index')}}";
	$('h1.title').html($('h1.title').html()+"-"+"{{$site_config['site_title']}}");
</script>
@stop