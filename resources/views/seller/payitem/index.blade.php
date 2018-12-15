@extends('seller._layouts.base')
@section('css')
@stop
@section('content') 
<div>
	<div class="m-zjgltbg">					
		<div class="p10">
			<div class="g-fwgl">
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">收费项目管理</span>
				</p>
			</div>
			<div class="m-tab m-smfw-ser">
				@yizan_begin
					<yz:list>
						<btns>
							<linkbtn label="添加" url="{{ u('PayItem/create') }}" css="btn-gray"></linkbtn>
						</btns>
						<table>
							<columns>
								<column code="id" label="编号" width="50"></column>
								<column code="name" label="项目名称" >
									<p>{{ $list_item['name'] }}</p>
								</column>
								<column code="price" label="单价（元）" width="100">
									<p>{{ $list_item['price'] }}</p>
								</column>
								<column label="计费方式" >
									<p>{{ Lang::get('api_seller.property.charging_item.'.$list_item['chargingItem']) }}</p>
								</column>
								<column label="计费单位" >
									<p>{{ Lang::get('api_seller.property.charging_unit.'.$list_item['chargingUnit']) }}</p>
								</column> 
								<actions width="80">
									<action label="编辑" >
										<attrs>
											<url>{{ u('PayItem/edit',['id'=> $list_item['id']]) }}</url>
										</attrs>
									</action>
									<action type="destroy" css="red"></action>
								</actions>
							</columns>
						</table>
					</yz:list>
				@yizan_end
			</div> 
		</div>
	</div>
</div>
	
@stop

@section('js')
@stop
