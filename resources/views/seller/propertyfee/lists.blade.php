@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10"> 
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">线下缴费</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list> 
							<table pager="no">
								<columns>
									<column code="id" label="编号" width="40"></column>
									<column code="build" label="楼栋号" width="50">
										<p>{{ $list_item['build']['name'] }}</p>
									</column>
									<column code="roomNum" label="房间号" width="50">
										<p>{{ $list_item['room']['roomNum'] }}</p>
									</column>
									<column code="name" label="业主" >
										<p>{{ $list_item['puser']['name'] }}</p>
									</column> 
									<column label="收费项目" >
										<p>{{ $list_item['roomfee']['payitem']['name'] }}</p>
									</column>
									<column label="费用" >
										<p>{{ $list_item['fee'] }}</p>
									</column>
									<column label="计费开始时间" width="100px">
										<p>{{ yztime($list_item['beginTime'], 'Y-m-d') }}</p>
									</column>
									<column label="计费结束时间" width="100px">
										<p>{{ yztime($list_item['endTime'], 'Y-m-d') }}</p>
									</column>  
									<column label="备注" >
										<p>{{ $list_item['roomfee']['remark'] }}</p>
									</column>
								</columns>
							</table>
						</yz:list> 
					<div> 
					<div>
					<p style="height: 50px;line-height: 50px;margin-left: 10px;"> </p>
					</div>  
					</div>  
						<yz:form id="yz_form" action="createOrder">  
							<div id="total-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                    合计:
                                </span>
                                <div class="f-boxr">
                                    <p >{{number_format($totalFee, 2)}}</p>
                                </div>
                            </div>
                            <yz:fitem  name="puserId" label="缴费业主"> 
                                <select id="puserId" name="puserId" style="min-width:100px;width:auto" class="sle ">
                                	@if(count($users) > 0)
                                	@foreach($users as $user)
                                    <option value="{{$user['id']}}" >{{$user['name']}}</option> 
                                    @endforeach 
                                    @else
                                    <option value="" >此房间内暂无业主</option>  
                                    @endif
                                </select> 
                            </yz:fitem>
							<input type="hidden" name="propertyFeeId" value="{{$args['propertyFeeId']}}" />
                        </yz:form>
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