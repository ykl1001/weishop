@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10"> 
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">房间费用管理</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list>
							<search> 
								<row>
									<item name="build" label="楼栋号"></item>  
									<item name="roomNum" label="房间号"></item> 
									<item name="name" label="业主名称"></item>    
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<btns>
								<linkbtn label="添加" css="btn-gray" url="{{ u('RoomFee/create') }}"></linkbtn>
								<linkbtn type="destroy" css="btn-gray"></linkbtn>
							</btns>
							<table checkbox="1">
								<columns>
									<column code="id" label="编号" width="40"></column>
									<column code="build" label="楼栋号" width="50">
										<p>{{ $list_item['build']['name'] }}</p>
									</column>
									<column code="roomNum" label="房间号" width="50">
										<p>{{ $list_item['room']['roomNum'] }}</p>
									</column> 
									<column label="收费项目" >
										<p>{{ $list_item['payitem']['name'] }}</p>
									</column>
									<column label="费用" >
										<p>{{ $list_item['fee'] }}</p>
									</column>
									<column code="remark" label="备注" ></column>
									
									<column code="PropertyFeeCount" label="支付订单数" width="80">
										<p>支付：{{$list_item['PropertyFeeCount']}}</p>
										<p>总数：{{$list_item['PropertyFeeCount']+$list_item['NotPropertyFeeCount']}}</p>
									</column>
									<column label="是否存在已支付订单" width="140">
										@if($list_item['PropertyFeeCount'] > 0)
											是
										@else
											-
										@endif
									</column>
									<actions width="80">
										<action label="查看" >
											<attrs>
												<url>{{ u('RoomFee/edit',['id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
										@if($list_item['PropertyFeeCount'] > 0)
											<a href="#" class="ml5 gray">删除</a>
										@else
											<action type="destroy" css="red ml5"></action>
										@endif
									</actions>
									@if(!empty($list_item['PropertyFeeCount']))
										<input type="hidden" class="PropertyFeeCount" value="{{$list_item['id']}}">
									@endif
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
<script type="text/javascript">
	$(function(){
		$("tbody tr .PropertyFeeCount").each(function(k, v){
			$(this).parent().find("input[name='key']").attr("disabled", "disabled");
		})
	})
</script>
@stop
