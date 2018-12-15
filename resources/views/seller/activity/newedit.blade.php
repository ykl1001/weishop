@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	/*特价商品*/
	.w195{width: 195px;}
    .ml25{margin-left: 25px;}
    .w150{width: 150px;}
    .w80{width: 80px;}

    .mt8{margin-top: 8px;}
    .p-addbtn{padding: 5px 10px;cursor: pointer;}
    .p-sellerlist{width: 600px;margin-top: 5px;cursor: pointer;border: solid #ccc;border-width: 1px 1px 0;}
    .p-sellerlist li:hover{background-color: #eee;}
    .p-sellerlist li{border-bottom: 1px solid #ccc;margin: 0;padding: 5px 10px;}

    .p-sellerlist i.fa{float: right;font-size: 16px;}
    .p-sellerlist i.fa:hover{color: red;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:form id="yz_form" nobtn="1">
			               <yz:fitem name="startTime" label="开始时间" type="date"></yz:fitem>
			                <yz:fitem name="endTime" label="结束时间" type="date"></yz:fitem>
			                <yz:fitem label="活动内容">
			                    <p>首单立减&nbsp;<input type="text" name="cutMoney" class="u-ipttext w163" placeholder="请输入金额" value="{{$data['cutMoney']}}">&nbsp;元</p>
			                </yz:fitem>
			                @if($data['isSystem'] == 1)
			                <yz:fitem label="适用范围">
			                    @if($data['useSeller'] == 0)
			                    	全部商家
			                    @elseif($data['useSeller'] == 1)
			                    	指定商家（含当前商家）
			                    @endif
			                </yz:fitem>
			                @endif
			                @if($data['isSystem'] == 0 && $data['sellerId'] > 0)
			                <div class="u-fitem clearfix">
			                    <span class="f-tt">
			                        &nbsp;
			                    </span>
			                    <div class="f-boxr">
			                          <button type="button" class="u-addspbtn2 zfbtn">作&nbsp;&nbsp;废</button>
			                    </div>
			                </div>
			                @endif
			            </yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		//作废
        $("button.zfbtn").click(function(){
            var status = "{{$data['timeStatus']}}";
            var id = "{{$data['id']}}";

            if(status == 1)
            {
                //进行中，结束
                var statusStr = "活动正在进行中，您确定要作废当前活动？";
            }
            else
            {
                //未开始，已结束，删除
                var statusStr = "您确定要删除活动？";
            }
            
            if(confirm(statusStr))
            {
                $.post("{{ u('Activity/cancellation') }}", {'id':id},function(res){
                    $.ShowAlert(res.msg);

                    if(res.code == 0)
                    {
                        setTimeout(function(){
                            if(status == 1)
                            {
                                window.location.reload();
                            }
                            else
                            {
                                window.location.href = "{{ u('Activity/index') }}";
                            }
                        },2000);
                        
                    }
                })
            }
        });
        
	})
</script>
@stop