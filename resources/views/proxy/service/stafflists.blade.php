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
			<table>
				<columns>
					<column code="id" label="编号" width="50"></column>
					<column code="seller" label="商家名称">
						<p>{{ $list_item['seller']['name'] ? $list_item['seller']['name'] : $list_item['name'] }}</p>
					</column>
					<column code="staff" label="人员名称">
						<p>{{ $list_item['name'] }}</p>
					</column>
					<column code="type" label="类型" width="100">
						<p>{{$types[$list_item['type']]}}</p>
					</column>
					<column code="mobile" label="手机号" width="100"></column> 
					<actions width="80">
						<action label="详情" >
							<attrs>
								<url>{{ u('service/staffEdit',['sellerId'=>$list_item['seller']['id'], 'id'=>$list_item['id']]) }}</url>
							</attrs>
						</action> 
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
