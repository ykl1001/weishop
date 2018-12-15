@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
		<btns>
			<linkbtn label="添加版块" url="{{ u('ForumPlate/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
		</btns>
		<table checkbox="1">
			<columns>
				<column code="name" label="分类编号" align="center"  ></column>
				<column code="icon" label="图标">
					<img src="{{$list_item['icon']}}" style="max-width:32px;"/>
				</column> 
				<column code="sort" label="排序" css="sort"></column> 
				<column code="status" label="状态" type="status"></column>
				<actions> 
					<action type="edit" css="blu"></action>
					<!-- @if(!$list_item['isSystem']) -->
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