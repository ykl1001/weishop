@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			@yizan_yield("search")
			<search method="get">
				<row>
					<item name="name" label="标签分类名"></item>
					<btn type="search"></btn>
				</row>
			</search>
			@yizan_stop
		<btns>
			<linkbtn label="添加分类" url="{{ u('SystemTag/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
			<span style="color:#828282;">(只有当标签在标签列表中未被占用时才能进行删除)</span>
		</btns>
		<table pager="no" checkbox="1">
			<columns>
				<column code="id" label="分类编号" width="90"></column>
				<column code="name" label="标签分类名"></column>
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"></column>
				<actions> 
					<action type="edit" css="blu"></action>
					<!-- @if( count($list_item['systemTagList']) < 1 ) -->
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