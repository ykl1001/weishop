@extends('admin._layouts.base')
@section('right_content')
	@yizan_begin
		<yz:list>
		<btns>
			<linkbtn label="添加图标" url="{{ u('SellerAuthIcon/create') }}"></linkbtn>
			<linkbtn label="删除" type="destroy"></linkbtn>
			<span style="color:#828282;">(只有当认证图标下没有商家才能删除)</span>
		</btns>
		<table checkbox='1'>
			<columns>
				<column code="id" label="编号" width="90"></column>
                <column code="name" label="名称" css="sort" align="center"></column>
				<column code="icon" label="图标" type="image" css="sort" align="center"></column>
				<column code="sort" label="排序" css="sort"></column>
				<actions> 
					<action type="edit" css="blu"></action>
					<!-- @if( count($list_item['seller']) < 1 ) -->
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
