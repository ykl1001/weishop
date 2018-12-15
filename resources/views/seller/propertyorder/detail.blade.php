@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10"> 
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">缴费详情</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
					<div class="m-tab m-smfw-ser pt20"> 
                        <div class="m-spboxlst " style=""> 
							<div id="name-form-item" class="u-fitem clearfix ">
					            <span class="f-tt">
					                 订单号:
					            </span>
					            <div class="f-boxr">
					                {{$data['sn']}}
					            </div>
					        </div>
							<div id="name-form-item" class="u-fitem clearfix ">
					            <span class="f-tt">
					                 流水号:
					            </span>
					            <div class="f-boxr">
					                {{$data['userPayLog']['sn']}}
					            </div>
					        </div>
                            <div id="remark-form-item" class="u-fitem clearfix ">
					            <span class="f-tt">
					                 缴费日期:
					            </span>
					            <div class="f-boxr">
					                {{yztime($data['createTime'])}}
					            </div>
					        </div>	 
						</div> 
	                </div>
					<yz:list>  
						<table pager="no">
							<columns>
								<column code="id" label="编号" width="40">
									<p>{{$list_item['propertyFee']['id']}}</p>
								</column>
								<column code="build" label="楼栋号" width="50">
									<p>{{ $list_item['propertyFee']['build']['name'] }}</p>
								</column>
								<column code="roomNum" label="房间号" width="50">
									<p>{{ $list_item['propertyFee']['room']['roomNum'] }}</p>
								</column>
								<column code="name" label="业主" >
									<p>{{ $list_item['propertyFee']['room']['owner'] }}</p>
								</column> 
								<column label="收费项目" >
									<p>{{ $list_item['propertyFee']['roomfee']['payitem']['name'] }}</p>
								</column>
								<column label="费用" >
									<p>{{ $list_item['propertyFee']['fee'] }}</p>
								</column>
								<column label="计费开始时间" width="100px">
									<p>{{ yztime($list_item['propertyFee']['beginTime'], 'Y-m-d') }}</p>
								</column>
								<column label="计费结束时间" width="100px">
									<p>{{ yztime($list_item['propertyFee']['endTime'], 'Y-m-d') }}</p>
								</column>  
								<column label="备注" >
									<p>{{ $list_item['propertyFee']['roomfee']['remark'] }}</p>
								</column>
							</columns>
						</table>
					</yz:list> 
					<div> 
						<span class="f-tt" >合计:{{$data['payFee']}}</span>
					</div>   
					@yizan_end
				</div>
			</div>
		</div>
	</div>
	
@stop

@section('js')
<script type="text/javascript">
jQuery(function($){ 
});
</script>
@stop