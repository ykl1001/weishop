@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
		<btns>
			<linkbtn label="添加分类" url="{{ u('SellerCate/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
			<span style="color:#828282;">(只有当分类下没有商家数据时才能进行删除)</span>
		</btns>
		<table pager="no" checkbox="1">
			<columns>
				<column code="id" label="分类编号" width="90"></column>
				<!-- @if($list_item['pid']==0) -->
				<column code="levelname" label="分类名称" align="left" style="font-weight:bold" css="name"></column>
				<!-- @else -->
				<column code="levelname" label="分类名称" align="left" css="name">
					{!! $list_item['levelname'] !!}
				</column>
				<!-- @endif -->
				<column code="levelrel" label="层级视图" css="sort" align="left"></column>
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"  width="80"></column>
				<actions  width="80">
					<action type="edit" css="blu"></action>
                    <!-- @if( count($list_item['sellers']) < 1 && $list_item['childsCount'] < 1 ) -->
                    <action type="destroy" css="red"></action>
                    <!-- @else -->
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