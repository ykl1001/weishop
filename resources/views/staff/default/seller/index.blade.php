@extends('staff.default._layouts.base')
@section('css')
    <style type="text/css">
        /*.y-wdzd{float: right;}
        .y-wdzd span{color: #fff!important;padding: 0 1rem;display: inline-block;border-left: 1px solid #fff;line-height: .8rem;}
        .y-busines{position: relative;padding: 1.8rem 0 1rem;}
        .y-busines p.f14{position: absolute;top: .6rem;left: .5rem;}*/
        .business_statistics{padding: .55rem .65rem .55rem .65rem;}
        .business_statistics p a{width: 100%;height: 100%;display: block;}
        .business_statistics p span{font-size: 1rem;}
    </style>
@stop
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom @stop
@section('content')
    <div class="business_statistics">
        <!-- <h3>今日营业统计</h3> -->
        <p>
            <a href="#" onclick="JumpURL('{{u('Seller/account')}}','#seller_account_view_1',2)"><span style="padding-right:.5rem;">账户余额</span><i>￥{{$seller['balance'] or 0}}</i><i class="icon iconfont f_fff fr">&#xe64b;</i></a>
            <!-- <a href="#" style="float:right;"><span style="color: #fff!important;padding: 0 1rem;display: inline-block;border-left: 1px solid #fff;line-height: .8rem;">我的账单</span></a> -->
        </p>
        {{-- class="pageloding" external--}}
    </div>
    <div class="busines_nun  w_b pr" style="padding:1.8rem 0 1rem;">
        <p class="f14 f_999 pa" style="top: .6rem;left: .5rem;">今日营业统计</p>
        <div class="w_b_f_1 tc child">
            <p>{{$seller['orderNum'] or 0}}</p>
            <div>订单数</div>
        </div>
        <div class="w_b_f_1 tc child">
            <p>￥{{$seller['turnover'] or 0}}</p>
            <div>营业额</div>
        </div>
    </div>
    <div class="blank050"></div>
    <div class="shop-nav-parent">
        <div class="shop-nav mt070 b-t">
            <!-- 全国店不显示，周边店显示 -->
            @if($seller['storeType'] == 0)
                <a href="#" onclick=" $.sao();" class="seller_money">
                    <div class="b-fb8486" style="background: #fe6969!important;"><i class="icon iconfont">&#xe68b;</i></div>
                    <p>扫描核销</p>
                </a>

                <a href="#" onclick="JumpURL('{{u('Seller/authCode')}}','#seller_authCode_view',2)" class="seller_money">
                    <div class="b-fb8486" style="background: #fe6969!important;"><i class="icon iconfont">&#xe68a;</i></div>
                    <p>消费码核销</p>
                </a>
            @endif
            <a href="#" onclick="JumpURL('{{u('Seller/activity')}}','#seller_activity_view',2)" class="">
                <div class="b-fb8486"><i class="icon iconfont">&#xe657;</i></div>
                <p>营销中心</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/analysis')}}','#seller_analysis_view_1',2)" class="">
                <div class="b-88dac3"><i class="icon iconfont">&#xe65b;</i></div>
                <p>经营分析</p>
            </a>
            <a href="{{u('Seller/info')}}" external  class="">
                <div class="b-90cafc"><i class="icon iconfont">&#xe659;</i></div>
                <p>店铺信息</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/evaluation')}}','#seller_evaluation_view',2)" class="">
                <div class="b-fdb563"><i class="icon iconfont">&#xe653;</i></div>
                <p>评价管理</p>
            </a>
            <a href="#" onclick="JumpURL('{{u('Seller/goodslists')}}','#seller_goodslists_view',2)" class="">
                <div class="b-c6a6f1"><i class="icon iconfont">&#xe65c;</i></div>
                <p>商品管理</p>
            </a>
            <!-- 全国店不显示，周边店显示 -->
            @if($seller['storeType'] == 0)
                <a href="#" onclick="JumpURL('{{u('Seller/seller')}}','#seller_seller_view',2)" class="">
                    <div class="b-a2d377"><i class="icon iconfont">&#xe65d;</i></div>
                    <p>服务管理</p>
                </a>
                @endif
                        <!-- 全国店显示，周边店不显示 or 全国店不显示，周边店显示 -->
                @if($seller['storeType'] == 1)
                    <a href="#" onclick="JumpURL('{{u('Seller/freightList')}}','#seller_freight_view',2)" class="">
                        <div class="b_9966cc"><i class="icon iconfont">&#xe632;</i></div>
                        <p>运费设置</p>
                    </a>
                @else
                    <a href="#" onclick="JumpURL('{{u('Seller/sendset')}}','#seller_sendset_view',2)" class="">
                        <div class="b_9966cc"><i class="icon iconfont">&#xe632;</i></div>
                        <p>配送设置</p>
                    </a>
                @endif
                <!--<a href="#" onclick="JumpURL('{{u('Seller/shopdetail')}}','#seller_goodslists_view',2)" class="">-->
			<a href="#" onclick="location.href='{{u('Seller/shopdetail')}}'" class="">
                    <div class="b_b9cf82"><i class="icon iconfont">&#xe68b;</i></div>
                    <p>店铺名片</p>
                </a>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>

    <script type="text/javascript">
        $(function(){
            $.sao = function(){
                if(window.App){
                    window.App.qr_code_scan();
                }else{
                    wx.ready(function () {
                        wx.scanQRCode({
                            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                            scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                            success: function (res) {
                                var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                                if(result != ""){
                                    var data = new Object();
                                    data.code = result;
                                    $.post("{{ u('Seller/checkCode') }}",data, function(res){
                                        if(res.code == 0){
                                            JumpURL("{{ u('Seller/orderAuthCode') }}?id="+res.data.id,'#seller_orderAuthCode_view',2);
                                        }else{
                                            $.alert(res.msg);
                                        }
                                    });
                                }else{
                                    $.alert('亲，扫描失败！');
                                }
                            }
                        });
                    })
                }
            }
        });

        function js_qr_code_scan(val){
            if(val != ""){
                var data = new Object();
                data.code = val;
                $.post("{{ u('Seller/checkCode') }}",data, function(res){
                    if(res.code == 0){
                        $.toast('扫描成功');
                        setTimeout(
                                function(){
                                    JumpURL("{{ u('Seller/orderAuthCode') }}?id="+res.data.id,'#seller_orderAuthCode_view',2);
                                },2000)

                    }else{
                        $.alert(res.msg);
                    }
                });
            }else{
                $.alert('亲，扫描失败！');
            }
        }

    </script>
@stop
@section('preloader')
@stop