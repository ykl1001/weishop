@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<btns>
			<linkbtn label="添加管理员组" url="{{ u('AdminRole/create') }}"></linkbtn>
			<btn type="destroy" label="删除"></btn>
		</btns>
		<table checkbox="1">
			<columns>
				<column code="id" label="编号"></column>
				<column code="name" label="组名称"></column>
				<column code="status" label="状态" type="status"></column>
				<actions>
					<action type="edit" css="blu"></action>
					<!-- @if($list_item['canDelete'] == 1) -->
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

@stop
