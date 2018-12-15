@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/index')}}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">配送设置</h1>
    </header>
@stop
@section('css')
@stop
@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop
@section('show_nav')
	<div class="bar bar-tab y-orderbutton">
        <a href="" class="button bg_red f_fff" id="submit">保&nbsp;&nbsp;存</a>
    </div>
@stop

@section('content')
    <div class="list-block media-list y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner f_5e">
                    <div class="item-title-row">
                        <div class="item-title f13">起送价</div>
                        <div class="item-after f13">
	                        <input type="text" value="{{$data['serviceFee'] ? $data['serviceFee'] : '0.00'}}" class="tr f13 moneyToFixed" id="serviceFee">&nbsp;元
	                        <i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </div>
            </li>
            <li class="item-content">
                <div class="item-inner f_5e">
                    <div class="item-title-row">
                        <div class="item-title f13">配送费</div>
                        <div class="item-after f13">
                        	<input type="text" value="{{$data['deliveryFee'] ? $data['deliveryFee'] : '0.00'}}" class="tr f13 moneyToFixed" id="deliveryFee">&nbsp;元
                        	<i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </div>
            </li>
            <li class="item-content">
                <div class="item-inner f_5e">
                    <div class="item-title-row">
                        <div class="item-title f13">消费方式</div>
                        <div class="item-after f13 y-xffstcmain">{{ $data['sendWayStr'] ? $data['sendWayStr'] : '请选择' }}<i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                    </div>
                </div>
                <input type="hidden" value="{{ implode(',',$data['sendWay']) }}" id="sendWayval">
            </li>
            <li class="item-content psfw @if(!in_array(1,$data['sendWay'])) none @endif">
                <div class="item-inner f_5e">
                    <div class="item-title-row">
                        <div class="item-title f13">配送服务</div>
                        <div class="item-after f13 y-psfwtcmain">{{ $data['sendTypeStr'] ? $data['sendTypeStr'] : '请选择' }}<i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                    </div>
                    <div class="f_999 f12 mt10">配送托管：由平台自动分配平台配送员负责订单的配送，商家需先对订单商品完成打包，等待平台配送员上门取货</div>
                    <div class="f_999 f12 mt10">平台众包：由商家完成订单商品打包后自行安排配送或根据需要呼叫平台配送员负责订单的配送</div>
                </div>
	        	<input type="hidden" value="{{$data['sendType']}}" id="sendTypeval">
            </li>
        </ul>
    </div>
@stop

@section('footer')
	<!-- 底部弹窗 -->
	<!-- 弹窗 -->
	<div class="y-modal-overlay"></div><!-- y-modal-overlay-visible 加上有动画效果 -->
	<!-- 消费方式 -->
	<div class="y-actions-modal" id="y-xffstcmain"><!-- y-modal-in 加上有动画效果 -->
	    <div class="tc p10 bg_fff">请选择</div>
	    <div class="list-block media-list m0 y-checkboxjs">
	        <ul id="sendWayList">
	            <li class="item-content @if(in_array(1,$data['sendWay'])) active @endif" data-id="1">
	                <div class="item-inner f_5e">
	                    <div class="item-title-row">
	                        <div class="item-title f13 name">商家配送</div>
	                        <div class="item-after f13 lh20"><i class="icon iconfont y-checkbox f_red">&#xe638;</i></div>
	                    </div>
	                </div>
	            </li>
	            <li class="item-content @if(in_array(2,$data['sendWay'])) active @endif" data-id="2">
	                <div class="item-inner f_5e">
	                    <div class="item-title-row">
	                        <div class="item-title f13 name">到店消费</div>
	                        <div class="item-after f13 lh20"><i class="icon iconfont y-checkbox f_red">&#xe638;</i></div>
	                    </div>
	                </div>
	            </li>
	            <li class="item-content @if(in_array(3,$data['sendWay'])) active @endif" data-id="3">
	                <div class="item-inner f_5e">
	                    <div class="item-title-row">
	                        <div class="item-title f13 name">到店自提</div>
	                        <div class="item-after f13 lh20"><i class="icon iconfont y-checkbox f_red">&#xe638;</i></div>
	                    </div>
	                </div>
	            </li>
	        </ul>
	    </div>
	    <button class="y-btmbtn" id="sendWayBtn">确定</button>
	</div>
	<!-- 配送服务 -->
	<div class="y-actions-modal" id="y-psfwtcmain"><!-- y-modal-in 加上有动画效果 -->
	    <div class="tc p10 bg_fff">请选择</div>
	    <div class="list-block media-list m0 y-radiojs">
	        <ul id="sendTypeList">
	            <li class="item-content @if($data['sendType'] == 1) active @endif" data-id="1">
	                <div class="item-inner f_5e">
	                    <div class="item-title-row">
	                        <div class="item-title f13 name">配送托管</div>
	                        <div class="item-after f13 lh20"><i class="icon iconfont y-checkbox f_red">&#xe638;</i></div>
	                    </div>
	                </div>
	            </li>
	            <li class="item-content @if($data['sendType'] == 2) active @endif" data-id="2">
	                <div class="item-inner f_5e">
	                    <div class="item-title-row">
	                        <div class="item-title f13 name">平台众包</div>
	                        <div class="item-after f13 lh20"><i class="icon iconfont y-checkbox f_red">&#xe638;</i></div>
	                    </div>
	                </div>
	            </li>
	            <li>
	                <div class="p10 f_999 f12">
	                    <p>1、平台众包：配送运力主要由商家提供，按次收费，由商家自主设置配送费，也可根据需要呼叫平台配送员上门取货并完成配送；</p>
	                    <p>2、配送托管：配送运力全部由平台提供，按次收费，每单配送服务费为 {{$system_send_staff_fee or '0'}} 元，平台自动分配平台配送员上门取货并完成配送。</p>
	                </div>
	            </li>
	        </ul>
	    </div>
	    <button class="y-btmbtn" id="sendTypeBtn">确定</button>
	</div>
@stop

@section($js)
<script type="text/javascript">
	$(function(){
	    //消费方式
	    $(document).on("click",".page-current .y-xffstcmain",function(){
	        $(".page-current .y-modal-overlay").addClass("y-modal-overlay-visible");
	        $(".page-current #y-xffstcmain").addClass("y-modal-in").removeClass("y-modal-out");
	    })
	    //配送服务
	    $(document).on("click",".page-current .y-psfwtcmain",function(){
	        $(".page-current .y-modal-overlay").addClass("y-modal-overlay-visible");
	        $(".page-current #y-psfwtcmain").addClass("y-modal-in").removeClass("y-modal-out");
	    })
	    //关闭弹窗
	    $(document).on("click",".page-current .y-modal-overlay",function(){
	        $(".page-current .y-modal-overlay").removeClass("y-modal-overlay-visible");
	        $(".page-current .y-actions-modal").removeClass("y-modal-in").addClass("y-modal-out");
	    })
	    //弹窗复选按钮
	    $(document).on("click",".page-current .y-checkboxjs li",function(){
	        if($(this).hasClass("active")){
	            $(this).removeClass("active");
	        }else{
	            $(this).addClass("active");
	        }
	    })
	    //弹窗单选按钮
	    $(document).on("click",".page-current .y-radiojs li.item-content",function(){
	        $(this).addClass("active").siblings(".item-content").removeClass("active");
	    })
	    //金额处理
	    $(".page-current .moneyToFixed").focus(function(){
	    	if(this.value <= 0)
	    		this.value = '';
	    });
	    $(".page-current .moneyToFixed").blur(function(){
	    	if(this.value < 0)
	    		this.value = Math.abs(this.value);
            if(isNaN(this.value) || this.value=='')
                this.value = 0.00;
            $(this).val(parseFloat($(this).val()).toFixed(2));
        });
        //消费方式
        $(document).off("click",".page-current #sendWayBtn");
        $(document).on("click", ".page-current #sendWayBtn", function(){
        	var text  = '';
        	var val   = '';
        	var first = true;
        	//隐藏配送服务
        	$('.psfw').addClass('none');

        	$(".page-current ul#sendWayList li").each(function(k, v){
        		if($(this).hasClass('active'))
        		{
        			if(first)
        			{
        				text += $(this).find('.name').text();
        				val  += $(this).data('id');
        				first = false;
        			}
        			else
        			{
        				text += ","+$(this).find('.name').text();
        				val  += ","+$(this).data('id');
        			}

        			if($(this).data('id') == 1)
        			{
        				$('.psfw').removeClass('none');
        			}
        			
        		}
        	});

        	if(text == '')
        	{
        		text = '请选择';
        	}

        	text += '<i class="icon iconfont ml5 f14">&#xe64b;</i>';

        	$(".page-current .y-xffstcmain").html(text);
        	$(".page-current #sendWayval").val(val);

        	$(".page-current .y-modal-overlay").removeClass("y-modal-overlay-visible");
	        $(".page-current .y-actions-modal").removeClass("y-modal-in").addClass("y-modal-out");
        });
         //配送服务
        $(document).off("click",".page-current #sendTypeBtn");
        $(document).on("click", ".page-current #sendTypeBtn", function(){
        	var text  = '';
        	var val   = '';
        	$(".page-current ul#sendTypeList li").each(function(k, v){
        		if($(this).hasClass('active'))
        		{
    				text = $(this).find('.name').text();
    				val  = $(this).data('id');
        		}
        	});

        	if(text == '')
        	{
        		text = '请选择';
        	}

        	text += '<i class="icon iconfont ml5 f14">&#xe64b;</i>';

        	$(".page-current .y-psfwtcmain").html(text);
        	$(".page-current #sendTypeval").val(val);

        	$(".page-current .y-modal-overlay").removeClass("y-modal-overlay-visible");
	        $(".page-current .y-actions-modal").removeClass("y-modal-in").addClass("y-modal-out");
        });
        //提交
        $(document).off("click",".page-current #submit");
        $(document).on("click",".page-current #submit", function(){
        	$.showPreloader('数据保存中...');
        	var data = {};
        	data.serviceFee 	= $("#serviceFee").val();
        	data.deliveryFee	= $("#deliveryFee").val();
        	data.sendWay 		= $("#sendWayval").val();
        	data.sendType 		= $("#sendTypeval").val();

        	$.post("{{ u('Seller/sendsetSave') }}", data, function(res){
        		$.hidePreloader();
        		$.alert(res.msg);
        	})
        });

	})
</script>
@stop