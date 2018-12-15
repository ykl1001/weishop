@extends('wap.community._layouts.base') 
@section('show_top')
<header class="bar bar-nav">
    <a class="button button-link button-nav pull-left" href="javascript:cancell_back();" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <h1 class="title f16">收银台</h1>

</header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <div class="list-block y-syt">
            <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title c-gray">商家</div>
                        <div class="item-after c-black">{{ $data['sellerName'] }}</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title c-gray">合计</div>
                        <div class="item-after c-red">￥{{ number_format($data['payFee']-$data['payMoney'], 2) }}</div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
        <ul class="y-paylst">
            @if($payments)
                <?php
                $default_payment = '';
                ?> 
                @if($data['user']['balance'] > 0 && $data['payMoney'] <= 0)
                <?php 
                    if($data['user']['balance'] >= $data['payFee']){
                        $default_payment = 'balancePay';
                    }
                ?>
                <li class=" @if($data['user']['balance'] < $data['payFee']) y-zhye @endif on">
                    <img src="{{ asset('wap/community/newclient/images/ico/zf5.png') }}" />
                    <div class="y-payf y-sytpay @if($data['user']['balance'] < $data['payFee']) y-paynocenter @endif">
                        <p>余额<span>{{ number_format($data['user']['balance'],2) }}</span>元</p>
                        @if($data['user']['balance'] < $data['payFee'])
                            <p class="f12 balance_tip c-gray">
                                还需在线支付<span class="c-red">{{ ($data['user']['balance'] >= $data['payFee']) ? 0 : (number_format($data['payFee'] - $data['user']['balance'], 2)) }}元</span>
                            </p>
                        @endif
                    </div>
                    <label class="label-switch x-sliderbtn fr mr10 mt5">
                        <input type="checkbox" id="is_balance_pay" checked="checked" >
                        <div class="checkbox"></div>
                    </label>
                </li>
                @endif
                @foreach($payments as $key => $pay)
                <?php
                if (empty($default_payment)){
                    $default_payment = $pay['code'];
                }
                ?>
                <li class="@if(($pay['code'] == $default_payment) && $data['payFee'] - $data['user']['balance'] > 0) on @endif" data-code="{{ $pay['code'] }}">
                    <?php
                    switch ($pay['code']) {
                        case 'alipay':
                        case 'alipayWap':
                            $icon = asset('wap/community/client/images/ico/zf3.png');
                            break;
                        case 'weixin':
                        case 'weixinJs':
                            $icon = asset('wap/community/client/images/ico/zf2.png');
                            break;
                        case 'unionpay':
                        case 'unionapp':
                            $icon = asset('wap/images/ico/yl.png');
                            break;
                    }
                    ?>
                    <img src="{{ $icon }}" />
                    <div class="y-payf">{{ $pay['name'] }}</div>
                    <i class="icon iconfont">&#xe612;</i>
                </li>
                @endforeach
            @endif
        </ul>
        <p class="y-bgnone"><a href="#" class="y-paybtn f16 x-paybtn">确认支付</a></p>
    </div> 
    <style type="text/css">
    .y-bggray{position: fixed;top: 0;left: 0;right: 0;bottom: 0;z-index: 999;background: rgba(0,0,0,.5);}
    .y-keyboard{position: fixed;bottom: 0;left: 0;right: 0;z-index: 1000;}
    .y-keyboard li{width: 33.3%;line-height: 45px;height: 45px;box-sizing: border-box;border: solid #dadada;border-width: 1px 1px 0 0;float: left;text-align: center;background: #fff;}
    .c-bgccc{background: #ccc!important;}
    .y-keyboard li a{display: block;font-size: 18px;}
    .y-keyboard li:nth-child(3n){border-right: 0;}
    .y-p10{padding: .5rem;}
    </style>
    <div class="pay-password none">
        <div class="y-bggray"></div>
        <div class="y-keyboard ">
            <div class="c-bgfff p10"><i class="icon iconfont close_password c-gray fl">&#xe604;</i><p class="c-black f18 tc">输入支付密码</p></div>
            <div class="y-p10 pb0 c-bgfff">
                <div class="y-payinput ml10 mr10">
                    <span><img src="{{asset('wap/community/newclient/images/mmimg.png')}}"></span> 
                </div>
            </div>
            <div class="tr c-bgfff p10"><a href="{{ u('UserCenter/repaypwd', ['pay' => 1, 'orderId' => $data['id']]) }}" class="c-red f12">忘记密码?</a></div>
            <ul>
                <li><a href="javascript:;" class="number_item" data-value="1">1</a></li>
                <li><a href="javascript:;" class="number_item" data-value="2">2</a></li>
                <li><a href="javascript:;" class="number_item" data-value="3">3</a></li>
                <li><a href="javascript:;" class="number_item" data-value="4">4</a></li>
                <li><a href="javascript:;" class="number_item" data-value="5">5</a></li>
                <li><a href="javascript:;" class="number_item" data-value="6">6</a></li>
                <li><a href="javascript:;" class="number_item" data-value="7">7</a></li>
                <li><a href="javascript:;" class="number_item" data-value="8">8</a></li>
                <li><a href="javascript:;" class="number_item" data-value="9">9</a></li>
                <li class="c-bgccc"><a href="javascript:;"></a></li>
                <li><a href="javascript:;" class="number_item" data-value="0">0</a></li>
                <li class="c-bgccc"><a href="javascript:;" class="remove_item"><i class="icon iconfont">&#xe604;</i></a></li>
            </ul>
        </div> 
    </div>
@stop

@section($js) 
<script type="text/javascript">
    var password = "";
    var payment = "{{ $default_payment }}";
    var balancePay = {{$data['user']['balance'] > 0 ? 1 : 0}}; 
    var isCanBalancePay = {{$data['user']['balance'] >= $data['payFee'] ? 1 : 0}};
    var key = "";
    $(document).on("touchend",".y-paylst li",function(){
        if(balancePay == 0 || isCanBalancePay == 0){
            $(".y-paylst li").removeClass("on");
            $(this).addClass("on");
            payment = $(this).data("code"); 
        }
    });
    $("#is_balance_pay").change(function(){
        balancePay = $("#is_balance_pay:checked").val() == "on" ? 1 : 0;
        if(balancePay == 1){
            if(isCanBalancePay == 1){
                $(".y-paylst li").removeClass("on");
                payment = 'balancePay';
            }
            $(".balance_tip").show();
            //$(".balance_tip").parent().parent().addClass('y-zhye');
        } else {
            $(".balance_tip").hide();
            //$(".balance_tip").parent().parent().removeClass('y-zhye'); 
        } 
    });
    $(document).on("click", ".number_item", function (){
        password += $(this).data('value');
        $.checkkeyup(); 
    });
    $(document).on("click", ".remove_item", function (){
        if(password.length >= 6){
            password = '';
        }else if(password.length > 0){
            password = password.substr(0, password.length - 1);
        }
        $.checkkeyup();
    });
    $(document).off("touchend", ".x-paybtn");
    $(document).on("touchend", ".x-paybtn", function (event){
        event.stopPropagation();
        event.preventDefault();
        if(balancePay == 1){
            var isPayPwd = "{{ $isPayPwd }}";
            if (isPayPwd == 1){
                $.showPayPwdModal();
            }else{
                $.toast("请先设置支付密码");
                $.router.load("{!! u('UserCenter/paypwd', ['pay' => 1, 'orderId' => $data['id']]) !!}", true);
            }
        }else{
            btnOK_onclick();
        }
    }) 
 
    $(document).on("click", ".close_password", function(){
        password = "";
        $(".y-payinput span").css("width","0%");
        $(".pay-password").addClass('none'); 
		FANWE.JS_BACK_HANDLER = null;
    });

    $.checkkeyup = function(){
        var sumw = 17 * password.length
        if(sumw > 100){ 
            sumw = 100; 
            if(password.length == 6){
                $(".modal").removeClass("modal-in").addClass("modal-out").remove();
                $(".modal-overlay").remove();
                $.showPreloader('确认支付中...');
                $.post("{{ u('UserCenter/checkpaypwd') }}", {password : password}, function(res){
                    if(res.status){
                        key = res.data;
                        $.hidePreloader();
                        btnOK_onclick();
                    }else{
                        $.hidePreloader();
                        $.toast(res.msg);
                    }
                },"json");
            }
        }
        $(".y-payinput span").css("width",sumw+"%");
        return true;
    }

    $.showPayPwdModal = function(){
        // $.modal({
            // title:  '<div class="y-paytop"><i class="icon iconfont c-gray fl close-modal">&#xe604;</i><p class="c-black f18 tc">输入支付密码</p></div>',
            // text: $("#pay-password").html()
        // });
        FANWE.JS_BACK_HANDLER = function() {
            $(".y-payinput span").css("width","0%");
            $(".pay-password").addClass('none');
            return true;
        }
        $(".pay-password").removeClass('none');
        $(".y-payinput span img").css("width",$(window).width()-40);
 
    }

    // 支付
    function btnOK_onclick()
    {
        try
        {
			
            if (window.App && payment != "balancePay" && typeof(payment) != "undefined")
            {
				
                var url = "{{u('Order/createpaylog')}}?payment=" + payment + "&id={{$data['id']}}";
                if(balancePay == 1){
                    url = "{{u('Order/createpaylog')}}?payment=" + payment + "&id={{$data['id']}}&balancePay="+balancePay+"&key="+key;
                }
                $.showIndicator();
                var result = $.ajax({ url: url, async: false, dataType: "text"});
                window.App.pay_sdk(result.responseText);
                $.hideIndicator();
            } else {
                 var url = '';
                if(payment == 'weixinJs'){
                    url = "{{ u('Order/wxpay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay+"&key="+key;
                }else if(payment == 'balancePay'){
                    if(balancePay == 1){
                        url = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment;
                    } else {
                        $.alert('请选择支付方式','提示');
                        return;
                    }
                }else {
                    if(typeof(payment) == "undefined"){
                        $.alert('请选择支付方式','提示');
                        return;
                    }
                    url = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment+"&balancePay="+balancePay+"&key="+key;
                }
                //$.router.load(url, true);//可能无法跳转
                $.showIndicator();
                window.location.href = url;
            }
        }
        catch (ex)
        {
        }
    }
    // 回调函数
    function PayComplete(result)
    {
        if(result == "Success")
        {
            $.router.load("{{ u('Order/detail',array('id'=>$data['id'])) }}", true);
        }
    }
    function cancell_back(){
        $.confirm('确认取消支付吗？', '取消支付', function () {
            var url = "{!! u('Order/detail',['id'=>$data['id']]) !!}";
            window.location.href = url;
        });
    }
    function js_back(){
        var is_handler = false;
        if (FANWE.JS_BACK_HANDLER) {
            is_handler = FANWE.JS_BACK_HANDLER.call(this);
            FANWE.JS_BACK_HANDLER = null;
        }
        if (is_handler) {
            return;
        }
        //关闭确认框
        if($(".modal-in").length>0){
            $.closeModal();
            return;
        }
        cancell_back();
    }
</script>
@stop
