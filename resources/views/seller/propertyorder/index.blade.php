@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
<div>
		<div class="m-zjgltbg">					
			<div class="p10"> 
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">物业费列表</span>
					</p>
				</div>
				<div class="m-tab m-smfw-ser">
					@yizan_begin
						<yz:list>
							<search> 
								<row>
		                            <yz:fitem  name="buildId" label="楼栋号">
										<yz:select name="buildId" options="$builds" first="请选择楼栋" firstvalue="0" textfield="name" valuefield="id" selected="$search_args['buildId']"></yz:select>  
		                            </yz:fitem>
		                            <yz:fitem  name="roomId" label="房间号">
		                                <select id="roomId" data-id="{{$search_args['roomId']}}" name="roomId" style="min-width:100px;width:auto" class="sle ">
		                                    <option value="" >请选择房间号</option>  
		                                </select>
		                            </yz:fitem> 
									<item name="name" label="业主名称"></item>  
									<item name="sn" label="订单号"></item>  
									<item name="beginTime" label="开始时间" type="date"></item>
									<item name="endTime" label="结束时间" type="date"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search> 
							<table >
								<columns> 
									<column code="sn" label="订单号" width="150"></column>
									<column label="楼宇" width="50">
										<p><a href="{{u('PropertyBuilding/roomindex',['buildId'=>$list_item['puser']['build']['id']])}}" target="_blank">{{ $list_item['puser']['build']['name'] }}</a></p>
									</column>
									<column code="name" label="房间" >
										<p><a href="{{u('PropertyBuilding/roomedit',['buildId'=>$list_item['puser']['build']['id'],'id'=>$list_item['puser']['room']['id']])}}" target="_blank">{{ $list_item['puser']['room']['roomNum'] }}</a></p>
									</column> 
									<column label="业主名" >
										<p><a href="{{u('PropertyUser/index',['name'=>$list_item['puser']['name']])}}" target="_blank">{{ $list_item['puser']['name'] }}</a></p>
									</column>
									<column label="缴费日期" >
										<p>{{ yztime($list_item['createTime'], 'Y-m-d H:i:s') }}</p>
									</column> 
									<column label="费用" >
										<p>{{ $list_item['payFee'] }}</p>
									</column>
									<actions width="80">
										<action label="查看" >
											<attrs>
												<url>{{ u('PropertyOrder/detail',['id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>  
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
<script type="text/javascript">
jQuery(function($){
    $("#buildId").change(function() {
        var buildId = $(this).val();
        var u_id = new Array(); 
        if(buildId > 0){
        	$.post("{{ u('PropertyOrder/searchroom') }}",{"buildId":buildId},function(result){
	            var html = '<option value="" >请选择房间号</option>';
	            if(result.data.list.length > 0){
	            	var data = result.data.list;
		            var roomId = $("#roomId").data('id');
		            $.each(data, function(index,e){ 
		                if (u_id.indexOf(data[index].id) == -1){
		                	if(roomId == e.id){
		                		html += " <option class='uid" + e.id + "' value=" + e.id + " selected >" + e.roomNum + "</option>";
		                	} else {
		                		html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.roomNum + "</option>";
		                	}  
		                }
		            });	
	            } 
	            $('#roomId').html(html);
	        },'json');
        } else {
        	var html = '<option value="" >请选择房间号</option>';
        	$('#roomId').html(html);
        }
        
    }).trigger('change'); 
    $.takeList = function(){
    	var input = $("input[name=key]");
    	var propertyFeeId = new Array();
    	var k = 0;
    	for (var i = 0; i < input.length; i++) { 
    		if($(input[i]).parent().hasClass('checked')){
    			propertyFeeId[k] = $(input[i]).val();
    			k++;
    		} 
    	}   
    	if(propertyFeeId.length <= 0){
    		$.ShowAlert('请选择要支付的账单');
    		return;
    	} 
        $.post("{{ u('PropertyFee/checkRoomFee') }}",{"id":propertyFeeId},function(result){   
            if(result.status){
            	window.location.href="{{ u('PropertyFee/lists') }}?propertyFeeId="+propertyFeeId;
            } else {
            	$.ShowAlert(result.msg);
            }
        },'json'); 
    };
});
</script>
@stop