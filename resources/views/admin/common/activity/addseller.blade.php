@extends('admin._layouts.base')
@section('css')

@stop
@section('right_content')
    @yizan_begin
        <yz:list>
			<search>
				<row>
					<item name="name" label="商家名称"></item>
					<!-- 返回时的type -->
					<input type="text" name="type" id="type" class="none" value="{{$args['type']}}">
					<btn type="search"></btn>
				</row>
			</search>
            <btns>
                <btn label="确认添加" css="btn-green addSeller"></btn>
                <linkbtn label="返回" url="{{ u('Activity/add') }}?type={{$args['type']}}" css="btn-blue"></linkbtn>
            </btns>
			<table checkbox="1">
				<columns>
                    <column code="name" label="商家名称" width="80%" align="left"></column>
                </columns>
			</table>
        </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//保存以选择的列表
		$(".addSeller").click(function(){
			var sellerIds = [];
			$.each($("table tr .checker span.checked input[name='key']"), function(k, v){
				sellerIds[k] = $(this).val();
			});

			$.post("{{ u('Activity/saveSellerIds') }}", {'sellerIds':sellerIds}, function(res){
				window.location.reload();
			});
		});
	});
</script>
@stop
