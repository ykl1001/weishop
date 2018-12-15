@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
		<btns>
			<linkbtn label="添加标签" url="{{ u('SystemTagList/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
			<span style="color:#828282;">(二级分类未被商品或服务占用时才能进行删除)</span>
		</btns>
		<table  checkbox="1">
			<columns>
				<column code="id" label="编号"></column>
				<column code="name" label="标签分类"></column>
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"></column>
				<actions> 
					<action type="edit" css="blu"></action>
					<!-- @if( $list_item['useTag'] == '' ) -->
					<action type="destroy" css="red"></action>
					<!-- @else -->
					<span css="gray" style="color:#ccc">删除</span>
					<script type="text/javascript">
                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled');
                    </script>
					<!-- @endif -->
				</actions>
			</columns>
			
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});
</script>
@stop