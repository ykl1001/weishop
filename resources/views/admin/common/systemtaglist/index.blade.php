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
			<span style="color:#828282;">(一级分类没有子分类时才能进行删除)</span>
		</btns>
		<table  checkbox="1">
			<columns>
				<column code="id" label="编号"></column>
                <column code="name" label="标签名称" css="name"></column>
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"></column>
				<actions>
                    <a href="{{u('SystemTagList/item',['pid'=>$list_item['id']])}}" >查看分类</a>
                    <action type="edit" css="blu"></action>
					<!-- @if( $list_item['hasOneItem'] == '' ) -->
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