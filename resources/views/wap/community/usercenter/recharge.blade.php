@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">充值</h1>
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
            @if(!$isFx)
                <div class="c-bgfff y-paymoney mb10">
                    <span class="fl f14 mt10">充值金额</span>
                    <div class="y-sum">
                        <input type="text" id="charge">
                        <p class="f12 c-gray mt5">充值后可使用余额进行交易支付</p>
                    </div>
                </div>
            @else
                <div class="c-bgfff y-paymoney mb10">
                    <span class="fl f14 mt10">缴费金额合计</span>
                    <span  id="charge" class="fr f14 mt10 x-noticeico">{{$money}}元</span>
                    <div class="clear"></div>
                </div>
            @endif
            <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
            <ul class="y-paylst">
                <?php
                    $payment_index = 0;
                    $default_payment = '';
                ?>
                @if($payments)
                    @if($user['balance'] > 0 && $money >= 0 && $isFx)
                        <?php
                            if($user['balance'] >= $money){
                                $default_payment = 'balancePay';
                            }
                        ?>
                        <li class=" @if($user['balance'] < $money) y-zhye @endif on">
                            <img src="{{ asset('wap/community/newclient/images/ico/zf5.png') }}" />
                            <div class="y-payf y-sytpay @if($user['balance'] < $money) y-paynocenter @endif">
                                <p>余额<span>{{ number_format($user['balance'],2) }}</span>元</p>
                                @if($user['balance'] < $money)
                                    <p class="f12 balance_tip c-gray">
                                        还需在线支付<span class="c-red">{{ ($user['balance'] >= $money) ? 0 : (number_format($money - $user['balance'], 2)) }}元</span>
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
                        <li class="@if(($pay['code'] == $default_payment) && $money - $user['balance'] > 0 && $isFx) on @endif @if(count($payments) == ($payment_index + 1)) last @endif" data-code="{{ $pay['code'] }}">
                            <?php
                                if (empty($default_payment)){
                                    $default_payment = $pay['code'];
                                }
                                switch ($pay['code']) {
                                    case 'weixin':
                                    case 'weixinJs':
                                        $icon = asset('wap/community/client/images/ico/zf2.png');
                                        break;
                                    case 'alipay':
                                    case 'alipayWap':
                                        $icon = asset('wap/community/client/images/ico/zf3.png');
                                        break;
                                    case 'unionpay':
                                    case 'unionapp':
                                        $icon = asset('wap/community/client/images/ico/zf4.png');
                                        break;
                                }
                            ?>
                            <img src="{{ $icon }}" />
                            <div class="y-payf f16">{{ $pay['name'] }}</div>
                            <i class="icon iconfont">&#xe612;</i>
                        </li>
                        <?php $payment_index ++; ?>
                    @endforeach
                @endif
            </ul>
            <p class="y-bgnone"><a class="y-paybtn f16 x-paybtn">确认充值</a></p>
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
        </div>
@stop

@section($js)
<script type="text/javascript">
    var password = "";
    var payment = "{{ $default_payment }}";
    var balancePay = {{$money > 0 ? 1 : 0}};
    var isCanBalancePay = {{$user['balance'] >= $money ? 1 : 0}};
    var key ='';
    var money;
    // 回调函数
    function PayComplete(result)
    {
        if (result == "Success")
        {
            $.router.load("{{ u('UserCenter/balance') }}", true);
        }
    }
    // 支付
    function btnOK_onclick()
    {
        try
        {
            if (window.App && payment != "balancePay" && typeof(payment) != "undefined")
            {
                var url = "{{u('Order/createpaylog')}}?payment=" + payment + "&money=" + money + "&id={{$data['id']}}" + "&isFx={{$isFx or 0}}";
                if(balancePay == 1){
                    url = "{{u('Order/createpaylog')}}?payment=" + payment + "&money=" + money + "&id={{$data['id']}}&balancePay="+balancePay+"&key="+key + "&isFx={{$isFx or 0}}";
                }
                $.showIndicator();
                var result = $.ajax({ url: url, async: false, dataType: "text"});
                window.App.pay_sdk(result.responseText);
                $.hideIndicator();
            } else {
                url = "{{ u('UserCenter/pay') }}?payment="+ payment +"&money="+money+"&key="+key + "&isFx={{$isFx or 0}}";
                if(payment == 'weixinJs'){
                    url = "{{ u('UserCenter/wxpay') }}?payment="+ payment +"&money="+money+"&key="+key + "&isFx={{$isFx or 0}}";
                }else if(payment == 'balancePay'){
                    if(balancePay == 1){
                        url = "{{ u('UserCenter/pay') }}?payment="+ payment +"&money="+money+"&isFx={{$isFx or 0}}";
                    } else {
                        $.alert('请选择支付方式','提示');
                        return false;
                    }
                }else {
                    if(typeof(payment) == "undefined"){
                        $.alert('请选择支付方式','提示');
                        return false;
                    }
                }
                $.showIndicator();
                window.location.href = url;
            }
        }
        catch (ex)
        {
        }
    }
	
    $(document).on("click", ".remove_item", function (){
        if(password.length >= 6){
            password = '';
        }else if(password.length > 0){
            password = password.substr(0, password.length - 1);
        }
        $.checkkeyup();
    });
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
    $(document).off("click", ".number_item");
    $(document).on("click", ".number_item", function (){
        password += $(this).data('value');
        $.checkkeyup();
    });
    $(document).off("touchend", ".x-paybtn");
    $(document).on("touchend", ".x-paybtn", function (event){
        @if(!$isFx)
            money = parseFloat( $("#charge").val() );
        @else
            money = {{$money}};
            event.stopPropagation();
            event.preventDefault();
            if(balancePay == 1){
                var isPayPwd = "{{ $isPayPwd }}";
                if (isPayPwd == 1){
                    $.showPayPwdModal();
                    return false;
                }else{
                    $.toast("请先设置支付密码");
                    $.router.load("{!! u('UserCenter/paypwd', ['pay' => 1, 'orderId' => $data['id']]) !!}", true);
                }
            }else{
                btnOK_onclick();
            }
            return false;
        @endif
        if(money <= 0 || money=='' || isNaN(money)){
            $.alert('充值金额必须是大于0的数字');
            return;
        }

        if (window.App && payment != "balancePay")
        {
            var result = $.ajax({ url: "{{u('UserCenter/createpaylog')}}?payment=" + payment + "&money=" + money + "&isFx="+ {{$isFx or 0}}, async: false, dataType: "text" });

            window.App.pay_sdk(result.responseText);
        }
        else
        {
            if (payment == 'weixinJs')
            { 
                window.location.href= "{{ u('UserCenter/wxpay') }}?payment=" + payment+"&money="+money + "&isFx="+ {{$isFx or 0}};
            } else
            {
                $.router.load("{{ u('UserCenter/pay') }}?payment=" + payment + "&money=" + money + "&isFx="+ {{$isFx or 0}}, true);
            }
        }
    });

    $(document).on("click", ".y-paylst li", function(){
        if(balancePay == 0 || isCanBalancePay == 0){
            $(".y-paylst li").removeClass("on");
            $(this).addClass("on");
            payment = $(this).data("code");
        }
    });
</script>
@stop