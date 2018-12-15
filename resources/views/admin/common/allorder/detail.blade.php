@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#refund_reason{color: #DA0809}
	.m-porbar .m-barlst li.on .f-lsbar{top: 9px;}
	.ts{text-align: center;color: #999}
	._gray{color: #ccc;}
	.tds tr{background-color: #fff;}
    /**
    取消订单
    **/
    .y-cancelreason{margin:0 15px;}
    .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
    .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
    .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
    
    .m-porbar{height: auto;padding: 10px 15px;}
    .y-refunddetails li{margin-top: 10px;}
    .y-stepnumber{color: red;}
</style>
@stop

@section('return_link')
		<a href="javascript:history.back(-1);" class="btn mb10"><i class="fa fa-reply mr10"></i>返回</a>
        @if($data['isCanCancel'])
            <a href="javascript:;" class="fr btn mb10 hsbtn-78  ml20" onclick="$.orderVerify(0)">取消订单</a>
		@endif
        @if($data['isCanAccept'])
            <!-- <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderVerify(1)">接单</a> -->
            <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderVerify(1)">发货</a>
        @endif
        @if($data['isCancfOrder'])
            <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderVerify(6)">同意取消</a>
        @endif
        @if($data['isRefundLog'])
        <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.lookRefundLog()">退款日志</a>
        @endif
        @if($data['isCanStartService'])
            <!-- <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderStart(1)">开始配送</a> -->
            <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.checkLogistics()">查看物流</a>
        @endif
        @if($data['isCanFinish'])
            <a href="javascript:;" class="fr btn mb10 hsbtn-78 ml20" onclick="$.orderFinish(1)">完成订单</a>
        @endif
@stop


@section('right_content')
	<!-- @if($data) -->
	<div class="m-ddbgct">
		<!-- 进度条 -->
		<div class="m-ddh">
			<p class="f-tt">
				订单号：{{$data['sn']}}
				<span class="ml20">
					下单时间：{{ Time::toDate($data['createTime']) }}
				</span>
				<span class="ml20" >支付状态：
					@if($data['payStatus']==1) 
					已支付 
					@else 
					<span class='_gray'>等待支付</span>
					@endif
				</span>
				<span class="ml20" >订单状态：{{$data['orderStatusStr']}}</span>
			</p>
            <!--
			<?php $width=(100/count($data['statusNameDate'])).'%'; $_width = ((100/count($data['statusNameDate']))-1).'%';?> 
			@if($data['statusFlowImage'])
				<div class="m-porbar clearfix">
					<img src="{{ asset('images/'.$data['statusFlowImage'].'.png') }}" alt="" class="mt20 pt10 clearfix">
					<ul class="m-barlst clearfix tc mt20 pt10" style="width:900px;">
					@foreach($data['statusNameDate'] as $key => $value)
						@if($data['statusFlowImage'] == 'statusflow_2' && $key == 2)
							<?php $color = '#efbe3b'; ?>
						@elseif($data['statusFlowImage'] == 'statusflow_7' && $key == 3)
							<?php $color = '#eb6868'; ?>
						@else
							@if($value['date']==0)
								<?php $color = '#ccc'; ?>
							@else
								<?php $color = '#7abd54'; ?>
							@endif
						@endif
						<li style="width:{{$width}};*width:{{$_width}};color:{{$color}}">
							<p class="tc">{{$value['name']}}</p>
							<p class="tc">{{ $value['date'] > 0 ? yztime($value['date']) : '' }}</p>
						</li>
					@endforeach
					</ul>
				</div>
			@endif
            -->
            @if($data['statusFlowImage'])
            <div class="m-porbar clearfix">
                <p class="mt5 mb5">订单日志</p>
                @foreach(array_reverse($data['statusNameDate']) as $key => $value)
                    @if($value['date'] > 0)
                        <p>{{yztime($value['date'])}}<span class="ml10">订单状态：{{$value['name']}}</span></p>
                    @endif
                @endforeach
            </div>
            @endif
		</div>

        @if($data['isRefundLog'])
        <div class="m-ddh" style="display:none" id="lookRefundLog">
            <div class="m-porbar clearfix">
                <p class="mt5 mb5">退款日志</p>
                <div class="content c-bgfff">
                    <ul class="y-refunddetails">
                        @if(in_array($dataLog['status'],[6]))
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">平台核审拒绝</span>{{$dataLog['adminDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    <p>{{$dataLog['adminDisposeContent']}}、本次拒绝，如果有疑问请联系平台客服：{{$site_config['wap_service_time']}}</p>
                                </div>
                            </li>
                        @endif
                        @if(in_array($dataLog['status'],[5]))
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">平台核审通过</span>{{$dataLog['adminDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    @if($refundLog['stepThree']['status'] == 1 && $dataLog['status'] == 5)
                                        <div class="y-stepnumber"><span class="y-refundtitle mr20">退款成功</span>{{$refundLog['stepThree']['time']}}</div>
                                        <div class="f_999 f12">
                                            <p>{{$refundLog['stepThree']['brief']}}</p>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endif
                        @if(in_array($dataLog['status'],[4,5,6]) &&  $dataLog['refundType']== 1)
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">商家确认收货</span>{{$dataLog['staffDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    <p>商家已确认收货,等待平台处理</p>
                                </div>
                            </li>
                        @endif

                        @if(in_array($dataLog['status'],[3,4,5,6]) &&  $dataLog['refundType']== 1)
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">买家已退货</span>{{$dataLog['userDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    <p>物流名称：{{$dataLog['userDisposeName']}}</p>
                                    <p>运 单 号：{{$dataLog['userDisposeNumber']}}</p>
                                    <p>退货地址：{{$dataLog['sellerAddress']}}</p>
                                    @if($dataLog['userDisposeImages'][0])
                                    <p>上传凭证</p>
                                        <p class="mt5 y-average">
                                            @foreach($dataLog['userDisposeImages'] as $img)
                                                <a href="{{$img}}" target="_new"><img src="{{$img}}" width="100px" class="vat"></a>
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endif
                        @if($dataLog['status']  == 2)
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">商家拒绝</span>{{$dataLog['sellerDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    <p>原因：{{$dataLog['sellerDisposeContent']}};</p>
                                    @if($dataLog['sellerDisposeExplain'])
                                    <p>说明：{{$dataLog['sellerDisposeExplain']}};</p>
                                    @endif
                                    <p>本次@if($dataLog['refundType'] == 1)退款退货@else仅退款@endif申请被拒绝，您可以再次发起;</p>
                                    @if($dataLog['sellerDisposeImages'][0])
                                        <p class="mt5 y-average">
                                            @foreach($dataLog['sellerDisposeImages'] as $img)
                                                <a href="{{$img}}" target="_new"><img src="{{$img}}" width="100px" class="vat"></a>
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endif
                        @if(in_array($dataLog['status'],[1,3,4,5,6]))
                            <li>
                                <div class="y-stepnumber"><span class="y-refundtitle mr20">商家处理通过</span>{{$dataLog['sellerDisposeTime']}}</div>
                                <div class="c-gray f12">
                                    @if($dataLog['refundType'] == 1)
                                        <p>退货地址：{{$dataLog['sellerAddress']}}</p>
                                        <p>商家同意了本次售后服务申请。请将退货商品包装好，且商品不影响二次销售；请勿发平邮或到付件，商品寄出后，需及时在每笔退款上操作“填写物流信息”，以免影响退款进度</p>
                                    @else
                                        <p>本次退款申请达成</p>
                                    @endif
                                </div>
                            </li>
                        @endif
                        <li>
                            <div class="y-stepnumber"><span class="y-refundtitle mr20">待商家处理</span></div>
                            <div class="c-gray f12">
                                <p>如商家同意，请按照给出的退货地址退货</p>
                                <p>如商家拒绝，您可以再次发起，商家会重新处理。</p>
                                <p>请勿相信任何人给您发来的可以退款的链接，以免钱款被骗。</p>
                                <p></p>
                            </div>
                        </li>
                        <li>
                            <div class="y-stepnumber"><span class="y-refundtitle mr20">{{$data['name']}}:发起了申请</span>{{$dataLog['createTime']}}</div>
                            <div class="c-gray f12">
                                <p>发起了@if($dataLog['refundType'] == 1)退款退货@else仅退款@endif申请，原因:{{$dataLog['content']}}@if($dataLog['refundExplain'])，说明：{{$dataLog['refundExplain']}}@endif</p>
                                @if($dataLog['images'][0])
                                    
                                    <p class="mt5 y-average">
                                        @foreach($dataLog['images'] as $img)
                                            <a href="{{$img}}" target="_new"><img src="{{$img}}" width="100px" class="vat"></a>
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endif


		<!-- 不可操作的订单信息 -->
		<div class="m-ordergk">
			<div class="u-tt clearfix">
				<span class="fl f14">订单概况</span>
			</div>
			<div class="clearfix">
				<div class="fl m-taborder" style="width:100%;background: #FFF;">
					<table>
						<tr>
						    <td style="padding-left:10px">
    						    <p class=" f14">会员名：{{$data['user']['name']}}</p>
								<p class=" f14">服务地址：{{$data['name']}}，{{$data['mobile']}}，{{$data['province']}}{{$data['city']}}{{$data['area']}}{{$data['address']}}</p>
								<p class=" f14">配送时间：{{ Time::toDate($data['appTime']) }}</p>
                                <p class=" f14">支付方式：{{$data['payType']}}</p>
							</td>
						</tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">订单备注：{{ $data['buyRemark']}}</p>
                                @if($data['invoiceRemark'])
                                    <p class=" f14">发票抬头：{{ $data['invoiceRemark'] }}</p>
                                @endif
                                @if($data['giftRemark'])
                                    <p class=" f14">贺卡内容：{{ $data['giftRemark'] }}</p>
                                @endif
                                @if($data['cancelRemark'])
                                    <p class=" f14">取消原因：{{ $data['cancelRemark'] }}</p>
                                @endif
                            </td>
                        </tr>
						<!-- <tr>
						    <td style="padding-left:10px">
    						    <p class=" f14">服务人员：{{$data['staff']['name']}}    {{$data['staff']['mobile']}} {{--<a href="{{ u('Order/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">点击重新指派</a>--}}</p>
							</td>
						</tr> -->
					</table>
				</div>        				
			</div>

            <div class="u-tt clearfix">
                <span class="fl f14">商家信息</span>
            </div>
            <div class="clearfix">
                <div class="fl m-taborder" style="width:100%;background: #FFF;">
                    <table>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">店铺名称：{{$data['seller']['name']}}</p>
                            </td>
                            <td style="padding-left:10px">
                                <p class=" f14">法人/店主：{{$data['seller']['contacts']}}   {{$data['seller']['mobile']}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:10px">
                                <p class=" f14">服务电话：{{$data['seller']['serviceTel']}}</p>
                            </td>
                            <td style="padding-left:10px">
                                <p class=" f14">商家地址：{{$data['seller']['address']}}</p>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
            @include('_layouts.snack')
		</div>
	</div>
	<!-- @else -->
		<div class="ts">未查询到相关订单</div>
	<!-- @endif -->
@stop
@section('js')
<script type="text/tpl" id="serviceContent">
	<div style="width:500px;text-align:center;padding:10px;">
        <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">订单太多，无法及时送达</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
            <li><span id="cancelreason2">商品信息有误，即将下架</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
            <li><span id="cancelreason3">配送人员请假，无人配送</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li class="y-otherrea">
                <span id="cancelreason4">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="4">
                <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
            </li>
        </ul>
	</div>
</script>
<script type="text/tpl" id="express">
 <select id="editable-select-type" class="form-control express-type" style="width: 200px;height: 30px;margin: 10px 20px;">
        <option value="0">物流公司</option>
        <option value="1">其他物流</option>
        <option value="2">无需物流</option>

    </select>
    <input type="text" placeholder="搜索快递" class="u-ipttext mt10 search-express js-type0" style="width: 286px;margin: 10px 20px;">
    <select id="editable-select" class="form-control express-select js-type0" style="width: 300px;height: 30px;margin: 10px 20px;">
        <option value="">请选择快递公司</option>
        @foreach($couriercompany as $key => $value)
            <option value="{{$value}}">{{$key}}</option>
        @endforeach
    </select>
    <input id="company" type="text" placeholder="请输入快递公司" class="u-ipttext mt10  js-type1" style="width: 286px;margin: 10px 20px;display:none;">
    <input type="text" placeholder="请输入快递单号" class="u-ipttext mt10 express-number js-type" style="width: 286px;margin: 10px 20px;">
    <textarea id="remark" placeholder="请输入你的备注信息" class="form-control u-ipttext js-type2"  maxlength="200" style="width: 286px;height:88px;margin: 10px 20px;display: none;" ></textarea>

</script>

    <script type="text/javascript">
        var id = "{{$data['id']}}";

        $(function(){

            //取消原因—其他原因
            $(document).on("click",".y-cancelreason li input",function(){
                $(".y-otherreasons").addClass("none");
            }).on("click",".y-cancelreason li.y-otherrea input",function(){
                $(".y-otherreasons").removeClass("none");
            })

            $(document).on('change','#editable-select-type',function(){
                var val = $(this).val();
                if(val == 0){
                    $('.js-type0').show();
                    $(".js-type").show();
                    $(".js-type1").hide();
                    $(".js-type2").hide();
                }else if(val ==1 ){
                    $('.js-type1').show();
                    $(".js-type").show();
                    $(".js-type0").hide();
                    $(".js-type2").hide();

                }else if(val == 2){
                    $('.js-type2').show();
                    $(".js-type1").hide();
                    $(".js-type0").hide();
                    $(".js-type").hide();

                }
            })


            //取消订单
            $.orderCancel = function() {
                var msg = "确认取消该订单吗？";
                $.ShowConfirm(msg, $.orderCancelOk);
            }
            $.orderCancelOk = function() {
                // alert('取消订单')
                $.refundRemark({{ORDER_STATUS_USER_CANCEL}}); 
            }
            //接单 拒绝接单
            $.orderVerify = function(status) {
                var msg = "取消订单";
                var okfun = $.orderVerifyFalse;
                if(status==1){
                    var dialog = $.zydialogs.open($("#express").html(), {
                        boxid:'SET_GROUP_WEEBOX',
                        width:300,
                        title:'订单发货',
                        showClose:true,
                        showButton:true,
                        showOk:true,
                        showCancel:true,
                        okBtnName: '确认发货',
                        cancelBtnName: '取消',
                        contentType:'content',
                        onOk: function(){
                            $.orderVerifyTrue();
                        },
                        onCancel:function(){
                            $.zydialogs.close("SET_GROUP_WEEBOX");
                        }
                    });
                } else if(status== 6){
                    msg = "确认同意会员取消订单操作？";
                    $.ShowConfirm(msg, $.orderVerify6);
                }
                else
                {
                    $.ShowConfirm(msg, okfun);
                }
            }
            //搜索快递
            $(".search-express").live("keyup", function(){
                var keywords = $(this).val();

                $("select#editable-select option").show();

                if(keywords == '')
                {
                    return false;
                }

                $("select#editable-select option").each(function(k, v){
                    var ctName = $(this).text();
                    if( ctName.indexOf(keywords) == -1)
                    {
                        $(this).hide();
                    }
                })
            });

            $.orderVerify6 = function() {
                // alert('确认退款');
                $.refundRemark({{ORDER_STATUS_CANCEL_USER}},1);
            }

            $.orderVerifyTrue = function (){
                // alert('确认接单');
                $.refundRemark({{ORDER_STATUS_AFFIRM_SELLER}},1);
            }
            $.orderVerifyFalse = function() {
                // alert('拒绝接单');
                $.refundRemark({{ORDER_STATUS_CANCEL_ADMIN}},0);
            }

            //开始订单
            $.orderStart = function() {
                var msg = "确认开始配送吗？";
                $.ShowConfirm(msg, $.orderStartOk);
            } 

            $.orderStartOk = function() { 
                $.refundRemark({{ORDER_STATUS_START_SERVICE}}, 1); 
            }

            //完成订单
            $.orderFinish = function() {
                var msg = "确认完成此订单吗？";
                $.ShowConfirm(msg, $.orderFinishOk);
            } 

            $.orderFinishOk = function() { 
                $.refundRemark({{ORDER_STATUS_FINISH_STAFF}}, 1); 
            }

            $.refundRemark = function(status,type){
                //订单号处理
                if(status == {{ORDER_STATUS_AFFIRM_SELLER}})
                {
                    //处理物流信息
                    var express = new Object();
                    express.keycode = $(".express-select option:selected").val();
                    express.number = $.trim($(".express-number").val());
                    express.from = "{{$from}}";
                    express.to = "{{$to}}";
                    express.key = '';
                    express.orderId = "{{$data['id']}}";
                    express.userId = "{{$data['userId']}}";
                    express.sellerId = "{{$data['sellerId']}}";
                    express.type = $(".express-type option:selected").val();
                    express.remark = $("#remark").val();
                    if(express.type == 0){
                        express.company = $(".express-select option:selected").text();
                        if(express.company == '' || express.keycode == ''){
                            $.ShowAlert("请选择快递公司");
                            return false;
                        }

                        if(express.number == ''){
                            $.ShowAlert("请填写快递单号");
                            return false;
                        }


                    }else if(express.type == 1){
                        express.company = $("#company").val();
                        if(express.company == '' ){
                            $.ShowAlert("请输入快递公司");
                            return false;
                        }
                        if(express.number == ''){
                            $.ShowAlert("请填写快递单号");
                            return false;
                        }

                    }else if(express.type == 2){
                        if(express.remark == ''){
                            $.ShowAlert("请填写备注信息");
                            return false;
                        }
                    }

                    if(express.from == '' || express.to == '' || express.orderId == '' || express.userId == '' || express.sellerId == ''){
                        $.ShowAlert("参数错误，请刷新页面重试！");
                        return false;
                    }
                    $.post("{{ u('AllOrder/postlogistics') }}", express, function(res){

                    });
                }

                if(type != 1){
                    var dialog = $.zydialogs.open($("#serviceContent").html(), {
                        boxid:'SET_GROUP_WEEBOX',
                        width:300,
                        title:'拒绝理由',
                        showClose:true,
                        showButton:true,
                        showOk:true,
                        showCancel:true,
                        okBtnName: '确认理由',
                        cancelBtnName: '取消',
                        contentType:'content',
                        onOk: function(){
                            var cancelradioval = $('.y-cancelreason input[name="reason"]:checked ').val();
                            if(cancelradioval == 4){
                                var refuseContent = $("#cancelreasontext").val();
                                refuseContent = (refuseContent == "") ? $("#cancelreason"+cancelradioval).html() : refuseContent;
                            }else{
                                var refuseContent = $("#cancelreason"+cancelradioval).html();
                            }

                            if(refuseContent != ""){
                                if(!status) {
                                    $.ShowAlert("参数错误");
                                }else{
                                    dialog.setLoading();

                                    $.post("{{ u('Order/refundRemark') }}",{'id':id,'status':status,'refuseContent':refuseContent},function(res){
                                        $.ShowAlert(res.msg);
                                        dialog.setLoading(false);
                                        if(res.status==true) {
                                            window.location.reload();
                                        }
                                    },'json');
                                }
                            }else{
                                $.ShowAlert("请输入理由");
                            }
                        },
                        onCancel:function(){
                            $.zydialogs.close("SET_GROUP_WEEBOX");
                        }
                    });
                }else{
                    if(!status) {
                        $.ShowAlert("参数错误");
                    }else{
                        $.post("{{ u('Order/refundRemark') }}",{'id':id,'status':status},function(res){
                            $.ShowAlert(res.msg);
                            if(res.status==true) {
                                window.location.reload();
                            }
                        },'json');
                    }
                }
            }
            $.checkLogistics = function() {
                var data = new Object();
                data.sellerId = "{{$data['sellerId']}}";
                data.userId = "{{$data['userId']}}";
                data.id = "{{$data['id']}}";
                $.post("{{ u('AllOrder/checkLogistics') }}", data, function(result){
                    var title = result.expressCompany+"："+result.expressNumber;
                    var html = '';
                    if(result.data)
                    {
                       $.each(result.data, function(k, v){
                            html += "<p><span class='ml10 mr10'>"+this.time+"</span><span>"+this.context+"</span></p>";
                        }); 
                    }
                    else
                    {
                        html += '暂未查询到物流信息';
                    }
                    $.ShowAlert(html, title);
                    var left = $(window).width() / 2 - 300;
                    $("#CONFIRM_URL_WEEBOX").css({"width":"600px","left":left});
                    $("#CONFIRM_URL_WEEBOX .zydialog_mc p").css("padding","7px");
                    
                })
            }

            $.lookRefundLog = function() {
                $("#lookRefundLog").slideToggle();
            }
        })

    </script>
@stop