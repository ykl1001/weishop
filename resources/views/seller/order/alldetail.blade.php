@extends('seller._layouts.base')
@section('css')
<style>
	p{word-wrap:break-word; word-break:normal;}
	.tds tr{background-color: #fff;}
    .green{ color: green }
</style>
@stop
@section('content')
		<div class="m-zjgltbg" style="background: #FFF;">
			<div class="p10">
                <div class="mb20" style="height: 20px">
                    <span class="ml15 fl"><a href="{{ u('Order/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a></span>
                        @if($data['isPay'])
                             <span class="ml15 fr"><a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(0)">关闭订单</a></span>
                        @endif
                        @if($data['isCanAccept'])
                             <span class="ml15 fr"><a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(1)">发货</a></span>
                        @endif
                        @if($data['isLogistics'])
                             <span class="ml15 fr"><a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.checkLogistics()">查看物流</a></span>
                        @endif
                        @if($data['isCancfOrder'])
                            <span class="ml15 fr"><a href="javascript:;" class="fr btn mb10 hsbtn-78 mt10 ml20" onclick="$.orderVerify(6)">同意取消</a></span>
                        @endif
                        @if($refund)
                            @if($refund['status'] == 0)
                                 <span class="ml15 fr"><a href="javascript:;"  class="fr btn mb10 hsbtn-78 mt10 ml20" id="udb_refund_btn_n" data-status="2">拒绝退款</a></span>
                                 <span class="ml15 fr"><a href="javascript:;" id="udb_refund_btn_y" class="fr btn mb10 hsbtn-78 mt10 ml20" data-status="1">同意退款</a></span>
                            @endif
                            @if($refund['status'] == 3 && $refund['refundType'] == 1)
                                 <span class="ml15 fr"><a href="javascript:;"   class="fr btn mb10 hsbtn-78 mt10 ml20" id="udb_refund_btn_yes" data-status="4">确定收货</a></span>
                            @endif
                        @endif
                </div>
				<!-- 订单详情 -->
        		<div class="m-ordergk mt20">
        			<div class="u-tt clearfix mt20">
                        <p class="f-bhtt f14 clearfix" style="background: #F3F6FA;">
                            <span class="ml15">订单编号：{{ $data['sn'] }}</span>
                            <span class="ml15">下单时间：{{ yzTime($data['createTime']) }}</span>
                            <span class="ml15">支付状态：{{ $data['payStatus'] ? "已支付" :"未支付" }}</span>
                            <span class="ml15">订单状态：{{ $data['orderStatusStr'] }}</span>
                        </p>
        			</div>
        			<div class="clearfix" style="margin-top: -1px;">
                        <div class="fl m-taborder" style="width:100%;">
                            <table>
                                <tr>
                                    <td style="padding-left:10px">
                                        <p class=" f14">订单日志</p>
                                        @foreach( $data['statusNameDate'] as $vs)
                                            @if($vs['date'] > 0)
                                                <p class=" f14">{{ yzTime($vs['date']) }}  订单状态：{{ $vs['name'] }}</p>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                @if($data['buyRemark'] || $data['invoiceRemark'] || $data['giftRemark'] || $data['cancelRemark'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            @if($data['buyRemark'])
                                                <p class=" f14">订单备注：{{ $data['buyRemark']}}</p>
                                            @endif
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
                                @endif
                            </table>
                        </div>
                        @if($refund)
                        <div class="u-tt clearfix" style="background: #F3F6FA;">
                            <span class="fl f14 pl10">退款日志</span>
                        </div>
                        <div class="fl m-taborder" style="width:100%;background: #FFF;">
                            <table>
                                <tr>
                                <td style="padding-left:10px">
                                    <ul class="y-refunddetails">
                                        @if($userRefund['stepThree']['status'] == 1)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">退款成功</span>
                                                    <span class="f_999 f12 green">{{$userRefund['stepThree']['time']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>{{$userRefund['stepThree']['brief']}}</p>
                                                </div>
                                            </li>
                                        @endif
                                        @if(in_array($refund['status'],[5]))
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">平台核审通过</span>
                                                    <span class="f_999 f12 green">{{$refund['adminDisposeTime']}}</span>
                                                </div>
                                            </li>
                                        @endif
                                        @if(in_array($refund['status'],[6]))
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">平台核审拒绝</span>
                                                    <span class="f_999 f12 green">{{$refund['adminDisposeTime']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>{{$refund['adminDisposeContent']}}</p>
                                                </div>
                                            </li>
                                        @endif

                                        @if(in_array($refund['status'],[4]) && $refund['refundType'] == 1)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">退款中</span>
                                                    <span class="f_999 f12 green">{{$refund['staffDisposeTime']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>待平台处理</p>
                                                </div>
                                            </li>
                                        @endif

                                        @if(in_array($refund['status'],[3,4,5,6]) &&  $refund['refundType']== 1)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">买家已退货</span>
                                                    <span class="f_999 f12 green">{{$refund['userDisposeTime']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>物流公司：{{$refund['userDisposeName']}}</p>
                                                    <p>物流单号：{{$refund['userDisposeNumber']}}</p>
                                                    <p>物流公司：退货凭证</p>
                                                    @if($refund['userDisposeImages'][0])
                                                        <p class="mt5 y-average">
                                                            @foreach($refund['userDisposeImages'] as $img)
                                                                <img src="{{$img}}" width="24%" class="vat">
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif
                                        @if($refund['status'] == 1 && $refund['refundType'] != 1)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle">平台处理中</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>请勿相信任何人给您发来的可以退款的链接，以免钱款被骗。</p>
                                                </div>
                                            </li>
                                        @endif
                                        @if(in_array($refund['status'],[1,3,4,5,6]))
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">商家处理通过</span>
                                                    <span class="f_999 f12 green">{{$data['sellerDisposeTime']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>退货地址：{{$refund['sellerAddress']}}</p>
                                                    @if($refund['refundType'] == 1)
                                                        <p>商家同意了本次今后服务申请。请将退货商品包装好，且商品不影响二次销售；请勿发平邮或到付件，商品寄出后，需及时在每笔退款上操作“填写物流信息”，以免影响退款进度</p>
                                                    @else
                                                        <p>本次退款申请达成</p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif

                                        @if($refund['status']  == 2)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">退款失败</span>
                                                    <span class="f_999 f12 green">{{$refund['sellerDisposeTime']}}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle green">商家拒绝</span>
                                                    <span class="f_999 f12 green">{{$refund['sellerDisposeTime']}}</span>
                                                </div>
                                                <div class="f_999 f12">
                                                    <p>您拒绝了本次退款申请</p>
                                                    @if($refund['sellerDisposeImages'])
                                                        <p class="mt5 y-average">
                                                            @if($refund['sellerDisposeImages'][0])
                                                                @foreach($refund['sellerDisposeImages'] as $img)
                                                                    <img src="{{$img}}" width="24%" class="vat">
                                                                @endforeach
                                                            @endif
                                                        </p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif

                                        @if($refund['status'] == 0)
                                            <li>
                                                <div class="y-stepnumber"></div>
                                                <div class="y-titlerow">
                                                    <span class="y-refundtitle">商家处理中</span>
                                                </div>
                                            </li>
                                        @endif
                                        <li>
                                            <div class="y-stepnumber"></div>
                                            <div class="y-titlerow">
                                                <span class="y-refundtitle green">{{$data['user']['name']}}:发起了申请</span>
                                                <span class="c-gray f12 green">{{$refund['createTime']}}</span>
                                            </div>
                                            <div class="f_999 f12">
                                                <p>发起了@if($refund['refundType'] == 1)退款退货@else仅退款@endif申请，原因:{{$refund['content']}}@if($refund['refundExplain'])，说明：{{$refund['refundExplain']}}@endif</p>

                                                @if($refund['images'][0])
                                                    <p class="mt5 y-average">
                                                        @foreach($refund['images'] as $img)
                                                            <img src="{{$img}}" width="24%" class="vat">
                                                        @endforeach
                                                    </p>
                                                @endif
                                            </div>
                                        </li>
                                    </ul>
                                 </td>
                                </tr>
                            </table>
                        </div>
                        @endif
                        <div class="u-tt clearfix" style="background: #F3F6FA;">
                            <span class="fl f14 pl10">订单概况</span>
                        </div>
                        <div class="fl m-taborder" style="width:100%;background: #FFF;">
                            <table>
                                <tr>
                                    <td style="padding-left:10px">
                                        <p class=" f14">会 员 名：{{ $data['user']['name'] }}</p>
                                        <p class=" f14">收货信息：{{ $data['name'] }}，{{ $data['mobile'] }}，{{$data['province']}}{{$data['city']}}{{$data['area']}}{{ $data['address'] }}</p>
                                        <p class=" f14">支付方式：{{ $data['payType'] }}</p>
                                    </td>
                                </tr>
                                @if($data['buyRemark'] || $data['invoiceRemark'] || $data['giftRemark'] || $data['cancelRemark'])
                                    <tr>
                                        <td style="padding-left:10px">
                                            @if($data['buyRemark'])
                                                <p class=" f14">订单备注：{{ $data['buyRemark']}}</p>
                                            @endif
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
                                @endif
                            </table>
                        </div>
                        @include('_layouts.snack')
                    </div>
				</div>
			</div>
		</div>
@stop

@section('js')
<script type="text/tpl" id="WithdrawMoney">
	<div style="width:500px;text-align:center;padding:10px;">
		<textarea name='disposeRemark' id='content' placeholder='请务必填写退款备注。' style="width:480px;height:100px;border:1px solid #EEE"></textarea>
	</div>
</script>

<script type="text/tpl" id="serviceContent">
	<div style="width:250px;text-align:center;padding:10px;">
           <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">未及时付款</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
            <li><span id="cancelreason2">买家不想买了</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
            <li><span id="cancelreason3">买家信息填写错误，重新拍</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li><span id="cancelreason4">恶意买家/同行捣乱</span><input type="radio" name="reason" class="fr y-radio" value="4"></li>
            <li><span id="cancelreason5">缺货</span><input type="radio" name="reason" class="fr y-radio" value="5"></li>
            <li><span id="cancelreason6">买家拍错了</span><input type="radio" name="reason" class="fr y-radio" value="6"></li>
            <li class="y-otherrea">
            <span id="cancelreason8">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="8">
            <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
        </li>
    </ul>
	</div>
</script>
<script type="text/tpl" id="pais">
<div style="width:100%;text-align:center;padding:10px;">
	<ul class="x-rylst">
        @foreach ($staff as $key=>$val)
		  <li data-id="{{ $val['id'] }}" @if($val['id'] == $data['sellerStaffId']) class="on" @endif>{{ $val['name'] }}<i></i></li>
        @endforeach
		<!-- <li class="btn f-bluebtn suiji">随机指派</li> -->
        <div class="clearfix"></div>
	</ul>
    <p style="margin-bottom: 40px;"></p>
</div>
<script type="text/javascript">
var staffId = "";
  $(".x-rylst li").click(function(){
    	if($(this).hasClass("on")){
    		$(this).removeClass("on");
            staffId = "";
    	}else{
            $(".x-rylst li").each(function(){
                $(this).removeClass("on");
            });
    		$(this).addClass("on");
            staffId = $(this).data("id");
    	}
    });
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

		$(function(){
            var orderId = {{$data['id'] or 0}}
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

            //搜索快递
        $("#udb_refund_btn_y,#udb_refund_btn_n,#udb_refund_btn_yes").click(function(){

                var id = {{$refund['id'] or 0}};
                var orderId = {{$data['id']}};
                var  status = $(this).attr('data-status');
                if(status == 2){
                    window.location.href = '{{ u('Order/refunddispose',['id' => $data['id']]) }}';
                    return false;
                }
                var title,msg;
                if(status == 1){
                     title = "同意退款"
                     msg = "同意退款以后￥{{$refund['money']}}将退回买家帐号"
                }
                if(status == 4){
                    title = "确认收货";
                    msg = "提交后由平台处理退款"
                }
                var dialog = $.zydialogs.open("<p class='m10'>"+msg+"</p>", {
                    boxid:'SET_GROUP_WEEBOX',
                    width:300,
                    title:title,
                    showClose:true,
                    showButton:true,
                    showOk:true,
                    showCancel:true,
                    okBtnName: title,
                    cancelBtnName: '取消',
                    contentType:'content',
                    onOk: function(){
                        $.post("{{ u('Order/refund') }}",{id:id,orderId:orderId,status:status},function(res){
                            if(res.msg == null){
                                $.ShowAlert("成功受理退款订单");
                                window.location.reload();
                            }else{
                                $.toast(res.msg);
                                window.location.reload();
                            }
                        },'json');
                    },
                    onCancel:function(){
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                });

        });
		$("#isReceivabilitySeller").click(function(){
			var dialog = $.zydialogs.open($("#pais").html(), {
		        boxid:'SET_GROUP_WEEBOX',
		        width:300,
		        title:'指派人员',
		        showClose:true,
		        showButton:true,
		        showOk:true,
		        showCancel:true,
		        okBtnName: '确认指派',
				cancelBtnName: '取消返回',
		        contentType:'content',
		        onOk: function(){
			        if(staffId == ""){
			        	$.ShowAlert("没有选择指定的人员");
			        	return false;
				    }
		        	$.post("{{ u('Order/designate') }}",{'staffId':staffId,'orderId':id},function(res){
		        		$.ShowAlert(res.msg);
	    				if(res.code==80000) {
	    					window.location.reload();
		    			}
	    			},'json');
		        },
		        onCancel:function(){
		            $.zydialogs.close("SET_GROUP_WEEBOX");
		        }
	    	});
			/* $(".suiji").click(function(){
				 var serviceContent = $(".textarea").val();
		         var money = $("#money").val();
				 $.post("{{ u('Order/reassign') }}",{'orderId':id},function(res){
	        		$.ShowAlert(res.msg);
    				if(res.code == 80000) {
    					window.location.reload();
	    			}
    			},'json');
				// $.ShowAlert("开发中，请使用指定派发");
	        });*/
		});
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
			var msg = "确定要关闭订单？";
			var okfun = $.orderVerifyFalse;
			if(status==1){
				msg = "确认发货？";
				okfun = $.orderVerifyTrue;
                var dialog = $.zydialogs.open($("#express").html(), {
                    boxid:'SET_GROUP_WEEBOX',
                    width:500,
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
			}else if(status==2){
				msg = "确认取消退款？";
				okfun = $.orderVerify2;
            }else if(status==3){
                msg = "确认退款？";
                okfun = $.orderVerify3;
            }else if(status== 6){
                msg = "确认同意会员取消订单操作？";
                okfun = $.orderVerify6;
            }
            if(status !=1){
			    $.ShowConfirm(msg, okfun);
            }
		}
		$.orderVerifyTrue = function (){
			// alert('确认接单');
			$.refundRemark({{ORDER_STATUS_AFFIRM_SELLER}},1);
		}
		$.orderVerifyFalse = function() {
			// alert('拒绝接单');
			$.refundRemark({{ORDER_STATUS_CANCEL_SELLER}},0);
		}
		$.orderVerify2 = function (){
			// alert('取消退款');
			$.refundRemark({{ORDER_REFUND_SELLER_REFUSE}},2);
		}
		$.orderVerify3 = function() {
			// alert('确认退款');
			$.refundRemark({{ORDER_REFUND_SELLER_AGREE}},3);
		}
        $.orderVerify6 = function() {
            // alert('确认退款');
            $.refundRemark({{ORDER_STATUS_CANCEL_USER}},3);
        }
        //开始订单
        $.orderStart = function() {
            var msg = "确认开始服务吗？";
            $.ShowConfirm(msg, $.orderStartOk);
        }

        $.orderStartOk = function() {
            $.refundRemark({{ORDER_STATUS_START_SERVICE}}, 1);
        }
        //完成订单
        $.orderFinish = function() {
            var msg = "确认完成订单吗？";
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
                $.post("{{ u('Order/postlogistics') }}", express, function(res){
                    if(res.code == 0){
                        if(!status) {
                            $.ShowAlert("参数错误");
                        }else{
                            $.zydialogs.open("<p style='margin: 10px'>正在处理···</p>",{
                                width:300,
                                title:"订单处理中",
                                showButton:false,
                                showClose:false,
                                showLoading:true
                            }).setLoading();
                            $.post("{{ u('Order/refundRemark') }}",{'id':orderId,'status':status},function(res){
                                $.zydialogs.close();
                                $.ShowAlert(res.msg);
                                if(res.status==true) {
                                    window.location.reload();
                                }
                            },'json');
                        }
                    }
                });
            }else{
                if(type != 1 && type != 3){
                    if(type == 2){
                        var open =  "#WithdrawMoney";
                    }else{
                        var open = "#serviceContent";
                    }
                        var dialog = $.zydialogs.open($(open).html(), {
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
                                    $.post("{{ u('Order/refundRemark') }}",{'id':orderId,'status':status,'refuseContent':refuseContent},function(res){
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
                        $.post("{{ u('Order/refundRemark') }}",{'id':orderId,'status':status},function(res){
                            $.ShowAlert(res.msg);
                            if(res.status==true) {
                                window.location.reload();
                            }
                        },'json');
                    }
                }
            }
		}

	})

    $.checkLogistics = function() {
        var data = new Object();
        data.sellerId = "{{$this->sllerId}}";
        data.userId = "{{$data['userId']}}";
        data.id = "{{$data['id']}}";
        $.post("{{ u('Order/checkLogistics') }}", data, function(result){
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
    //取消原因—其他原因
    $(document).on("click",".y-cancelreason li input",function(){
        $(".y-otherreasons").addClass("none");
    }).on("click",".y-cancelreason li.y-otherrea input",function(){
        $(".y-otherreasons").removeClass("none");
    }).on("keyup",'.search-express', function(){
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

</script>
@stop