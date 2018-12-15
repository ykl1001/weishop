@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<php>
		$types = array(
			0 => '个人加盟人员',
			1 => '配送人员',
			2 => '服务人员',
			3 => '配送和服务人员',
		);
	</php>
		<yz:list>
			<search> 
				<row>
					<item name="sellerId" type="hidden"></item>
					<item name="name" label="员工姓名"></item>
					<item name="mobile" label="员工电话"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<btns>
				@if($sellerId > 0 && $seller['type'] == 2)
					<linkbtn type="add">
						<attrs>
							<url>{{ u('Staff/create',['sellerId'=>$sellerId]) }}</url>
						</attrs>
					</linkbtn>
				@endif
	            <linkbtn label="删除" type="destroy"></linkbtn>
			</btns>
			<table checkbox="1">
				<columns>
					<column code="id" label="编号" width="50"></column>
					<column code="seller" label="商家名称" align="left">
						<p>{{ $list_item['seller']['name'] ? $list_item['seller']['name'] : $list_item['name'] }}</p>
					</column>
					<column code="staff" label="人员名称">
						<p>{{ $list_item['name'] }}</p>
					</column>
					<column code="type" label="类型" >
						<p>{{$types[$list_item['type']]}}</p>
					</column>
					<column code="mobile" label="手机号"></column>
					<column code="status" type="status" label="状态" width="80"></column>
					<actions width="80">
						<action label="编辑"  css="blu">
							<attrs>
								<url>{{ u('Staff/edit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']]) }}</url>
							</attrs>
						</action>&nbsp;&nbsp;
                        @if($list_item['seller']['type'] == 2)
                            <action type="destroy" css="red"></action>
                        @endif
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
