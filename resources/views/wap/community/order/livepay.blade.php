@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav">
    <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {{ $nav_back_url }} @else javascript:$.router.back();  @endif" data-transition='slide-out'>
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
                        <div class="item-title c-gray">合计</div>
                        <div class="item-after c-red">￥{{ $args['money'] }}</div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title f14 c-gray"><i class="icon iconfont mr10">&#xe638;</i>支付方式</div>
        <ul class="y-paylst">

            <?php
            $payment_index = 0;
            $default_payment = '';
            ?>
            @if($user['balance'] > 0 && $args['money'] >= 0)
                <?php
                if($user['balance'] >= $args['money']){
                    $default_payment = 'balancePay';
                }
                ?>
                <li class=" @if($user['balance'] < $args['money']) y-zhye @endif on">
                    <img src="{{ asset('wap/community/newclient/images/ico/zf5.png') }}" />
                    <div class="y-payf y-sytpay @if($user['balance'] < $args['money']) y-paynocenter @endif">
                        <p>余额<span>{{ number_format($user['balance'],2) }}</span>元</p>
                        @if($user['balance'] < $args['money'])
                            <p class="f12 balance_tip c-gray">
                                还需在线支付<span class="c-red">{{ ($user['balance'] >= $args['money']) ? 0 : (number_format($args['money'] - $user['balance'], 2)) }}元</span>
                            </p>
                        @endif
                    </div>
                    <label class="label-switch x-sliderbtn fr mr10 mt5">
                        <input type="checkbox" id="is_balance_pay" checked="checked" >
                        <div class="checkbox"></div>
                    </label>
                </li>
            @endif

            @if($payments)
                @foreach($payments as $key => $pay)
                <li class="@if($payment_index == 0 && $args['money'] - $user['balance'] > 0) on @endif @if(count($payments) == ($payment_index + 1)) last @endif" data-code="{{ $pay['code'] }}">
                    <?php
                    if (empty($default_payment)){
                        $default_payment = $pay['code'];
                    }
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
                <?php $payment_index ++; ?>
                @endforeach
            @endif
        </ul>
        <p class="y-bgnone"><a href="#" class="y-paybtn f16 x-paybtn">确认支付</a></p>
    </div>

    <div class="modal-overlay modal-overlay-visible" style="display: none"></div>
    <div class="modal modal-in" style="display: none; margin-top: -86px;">
        <div class="modal-inner">
            <div class="modal-title"><div class="y-paytop"><i class="icon iconfont c-gray fl close-modal">&#xe604;</i><p class="c-black f18 tc">输入支付密码</p></div></div>
            <div class="modal-text">
                <div class="y-payinput">
                    <input type="passWord" maxlength="1"  name="paypwd">
                    <input type="passWord" maxlength="1"  name="paypwd">
                    <input type="passWord" maxlength="1" name="paypwd">
                    <input type="passWord" maxlength="1"  name="paypwd">
                    <input type="passWord" maxlength="1"  name="paypwd">
                    <input type="passWord" maxlength="1"  name="paypwd">
                </div>
                <div class="tr y-wjmm"><a href="{{ u('UserCenter/repaypwd', ['pay' => 1, 'orderId' => $data['id']]) }}" class="c-red f12">忘记密码?</a></div>
            </div>
        </div>
    </div>
@stop

@section($js)
    <script>
        var payment = "{{ $default_payment }}";
        // 回调函数
        function PayComplete(result)
        {
            if (result == "Success")
            {
                $.router.load("{{u('Property/livelog')}}");
            }
        }

        // 支付
        function btnOK_onclick(){
            var money = parseFloat( "{{ $args['money'] }}" );
            if(money <= 0 || money=='' || isNaN(money)){
                $.toast('充值金额必须是大于0的数字');
                return;
            }

            var title = "{{$args['title']}}";
            var args = "{{$args['args']}}";
            if (window.App && payment != "balancePay"){
                if(balancePay == 1){
                    var url = "{{u('Order/createlivelog')}}?payment=" + payment + "&money=" + money+"&title=" + title+"&args="+args+"&balancePay="+balancePay+"&key="+key;
                }else{
                    var url = "{{u('Order/createlivelog')}}?payment=" + payment + "&money=" + money+"&title=" + title+"&args="+args;
                }
                $.showIndicator();
                var result = $.ajax({ url: url, async: false, dataType: "text" });
                $.toast('loading...',5000);
                window.App.pay_sdk(result.responseText);
                $.hideIndicator();
            }
            else
            {
                if(balancePay == 1){
                    var url = "{{ u('Order/createlivelog') }}?payment=" + payment + "&money=" + money+"&title="+title+"&args="+args+"&balancePay="+balancePay+"&key="+key;
                }else{
                    var url = "{{u('Order/handpay')}}?payment=" + payment + "&money=" + money+"&title=" + title+"&args="+args;
                }
                window.location.href = url;
            }
        }

        $(document).on("click", ".x-paylst li", function ()
        {
            payment = $(this).data("code");
            $(this).addClass("on").siblings().removeClass("on");
        });

        var balancePay = "{{$user['balance'] > 0 ? 1 : 0}}";
        var isCanBalancePay = "{{$user['balance'] >= $args['money'] ? 1 : 0}}";

        $("#is_balance_pay").change(function(){
            balancePay = $("#is_balance_pay:checked").val() == "on" ? 1 : 0;
            if(balancePay == 1){
                if(isCanBalancePay == 1){
                    $(".y-paylst li").removeClass("on");
                    payment = 'balancePay';
                }
                $(".balance_tip").show();
            } else {
                $(".balance_tip").hide();
            }
        });

        $(document).on("touchend", ".close-modal", function(){
            $("input[name=paypwd]").val("");
            $("input[name=paypwd]").eq(0).focus();
            $(this).parents(".modal").css("display", "none").hide();
            $(".modal-overlay").css("display","none").hide();
        })
        $(document).on("touchend",".y-paylst li",function(){
            if(balancePay == 0 || isCanBalancePay == 0){
                $(".y-paylst li").removeClass("on");
                $(this).addClass("on");
                payment = $(this).data("code");
            }
        });

        $(document).on("touchend", ".x-paybtn", function (){
            if(balancePay == 1){
                var isPayPwd = "{{ $isPayPwd }}";
                if (isPayPwd == 1){
                    $.showPayPwdModal();
                }else{
                    $.toast("请先设置支付密码");
                    $.router.load("{!! u('UserCenter/paypwd', ['pay' => 2, 'type' => 2,'args' => $args]) !!}", true);
                }
            }else{
                btnOK_onclick();
            }
        })

        $.showPayPwdModal = function(){
            $("input[name=paypwd]").val("");
            $("input[name=paypwd]").eq(0).focus();
            $(".modal").css("display", "block").show();
            $(".modal-overlay").css("display","block").show();
        }

        $(document).on("keyup","input[name=paypwd]", function (e) {
            if(e.keyCode == 8){
                $("input[name=paypwd]").val("");
                $("input[name=paypwd]").eq(0).focus();
            }else{
                var index = $(this).index();
                if($(this).val() != ""){
                    $("input[name=paypwd]").eq(parseInt(index)+1).focus();
                }
                if(index == 5){
                    $(this).parents(".modal").css("display", "none").hide();
                    $(".modal-overlay").css("display","none").hide();
                    var pwd = "";
                    $("input[name=paypwd]").each(function(){
                        pwd += $(this).val();
                    })
                    $.post("{{ u('UserCenter/checkpaypwd') }}", {password : pwd}, function(res){
                        if(res.status){
                            key = res.data;
                            btnOK_onclick();
                        }else{
                            $.toast(res.msg);
                        }
                    },"json");
                }
            }

        })
    </script>
@stop
