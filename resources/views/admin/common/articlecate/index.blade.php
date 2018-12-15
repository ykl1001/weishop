@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<btns>
			<linkbtn type="create" label="添加分类" url="{{ u('ArticleCate/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
		</btns>
		<table pager="no" checkbox="1">
			<columns>  
				<column code="levelname" label="分类名称" align="left"></column>
				<column code="levelrel" label="层级视图" align="left"></column>
				<column code="sort" label="排序" width="60"></column>
				<column code="status" label="状态" type="status" width="60"></column>
				<actions width="100">
					<action type="edit" css="blu"></action>
					<!-- @if( !in_array($list_item['id'],$pids[0]) && $list_item['canDelete'] == 1) -->
					<action type="destroy" css="red"></action>
					<!-- @else -->
						<action type="destroy" click="javascript:;" style="color:#ccc;cursor:default"></action>
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
