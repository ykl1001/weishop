@extends('seller._layouts.base')
@section('css')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" />
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no, email=no">
<style type="text/css">
	.m-tab table tbody td{padding: 10px 0px;}
	#checkListTable p{padding:0px 0px 0px 10px;}
	.f12{font-size: 16px;}
	#showhtml{color:#000 !important;font-family: "宋体";font-weight: bold;}
	/*总样式*/
	#showhtml .mt9{margin-top: 9px;}
	#showhtml .f10 p{line-height: 20px;}
	/*最外层*/
	#showhtml .y-previewbox{width: 270px;box-sizing: border-box;padding: 20px 2px 2px;}
	#showhtml .y-previewmain{width: 100%;}
	/*分割线*/
	#showhtml .y-splitlinebox{border-top: 1px dashed #000;margin: 15px 0;height: 1px;}
	#showhtml .y-splitline{position: relative;top: -15px;left: 0;width: 100%;text-align: center;font-size: 16px;}
	#showhtml .y-splitline span{padding: 0 5px;color: #000;background: #fff;font-size: 16px;}
	#showhtml .y-commodity{padding-bottom: 5px;}
	#showhtml .y-commodity p{line-height: 20px;}
	#showhtml .y-commodity li{margin-bottom: 10px;}
	/*.y-commodity li{padding-bottom: 5px;}*/
	#showhtml .y-distribution{width: 100%;display: -webkit-box;display: -webkit-flex;display: flex;-webkit-box-flex: 1;-ms-flex: 1;-webkit-box-pack: justify;-webkit-justify-content: space-between;justify-content: space-between;-webkit-box-align: center;-webkit-align-items: center;align-items: center;}
	#showhtml .y-distribution p{display: inline-block;float: left;box-sizing: border-box;font-size: 16px;text-align: right;}
	#showhtml .y-distribution p.y-w75{width: 75%;text-align: left;}
	#showhtml .y-distribution p.y-w30{width: 30%;text-align: left;}
	#showhtml .y-distribution p.y-w25{width: 25%;padding-left: 5px;}
	#showhtml .y-distribution p.y-w20{width: 20%;padding-left: 5px;}
	#showhtml .y-distribution p span{display: block;}
	#showhtml .y-ewmimg{padding: 15px;}
	#showhtml .y-ewmimg img{width: 100%;vertical-align: top;}
	#showhtml .y-addr{display: block;margin-left: 55px;}
	.xiaopiao {cursor: pointer;}
</style>
	
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<div class="p10">
                @if(STORE_TYPE == 0)
				<!-- 订单管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix" style="border-bottom:0;">
						<span class="ml15 fl">商品类订单管理</span>
					</p>
				</div>
                @endif
                {{--全国店--}}
                @if(STORE_TYPE == 1)
                    @include("seller.order.order_item")
                 @else
                    @include("seller.order.order")
                @endif
			</div>
		</div>
	</div>
	<div id="y-previewbox-color" class="none" >
		<div id="showhtml">		
			 
		</div>
	</div>
</div>
@stop

@section('js')
    <script src="{{ asset('js/jQuery.print.js') }}?{{ TPL_VERSION }}"></script>
    <script type="text/javascript">
        function showUrl(id) {
			$.post("{{u('Order/printer')}}",{orderId:id},function(res){
				$("#showhtml").html(res);
				jQuery('#showhtml').print();
			});
        };
    </script>

@stop