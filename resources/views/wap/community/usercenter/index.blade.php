@extends('wap.community._layouts.base')

@section('css')
@stop

@section('show_top')
@stop

@section('content')

@include('wap.community._layouts.bottom')
<script type="text/javascript">
    var BACK_URL = "{{u('Index/index')}}";
</script>
    <div class="content y-wdcontent" id=''>
        <header class="bar bar-nav y-bar">
            @if($user)
            <a class="button button-link button-nav pull-left f14" data-transition='slide-out' href="#">
                我的
            </a>
            <a class="button button-link button-nav pull-right f14" href="{{ u('UserCenter/config') }}" data-no-cache="true">
                <span class="icon iconfont va-2 mr5">&#xe64a;</span>设置
            </a>
            <!-- <h1 class="title f16">我的</h1> -->
            <div class="y-wdtx" onclick="$.href('{{ u('UserCenter/info') }}')">
                <div class="y-wdtximg"><img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{ asset('wap/community/client/images/wdtt.png') }} @endif"></div>
                <p class="f16">{{$user['name']}}</p>
            </div>
            <ul class="y-balancetop clearfix">
                <li onclick="$.href('{{ u('UserCenter/balance') }}')">
                    <span class="f18">{{$balance or '0.00'}}</span>
                    <p class="f12">余额</p>
                </li>
                <li class="pageloading" onclick="$.href('{{ u('Coupon/index') }}')">
                    <span class="f18">{{$counts['proCount'] or '0'}}</span>
                    <p class="f12">{{$wap_promotion}}</p>
                </li>
                <li onclick="$.href('{{ u('UserCenter/integral') }}')">
                    <span class="f18">{{ $integral }}</span>
                    <p class="f12">{{$wap_integral}}</p>
                </li>
            </ul>
            @else
            <div class="y-wdtx">
                <div class="y-wdtximg">
                    <img src="{{ asset('wap/community/client/images/wdtx-wzc.png') }}">
                </div>
                <p class="f16">
                    <a href="{{ u('User/reg') }}" external>注册</a>
                    <a href="{{ u('User/login') }}">登录</a>
                </p>
            </div>
            @endif
        </header>
        <div class="list-block media-list mb0">
            <ul>
                @if($user && !empty($user['fanweId']) && FANWEFX_SYSTEM)
                <li>
                        <a  href="#" class="item-link item-content">
                            <div class="item-inner pr10">
                                <div class="item-title-row">
                                    <div class="item-title f14" onclick="javascript:$.href('{{u('UserCenter/wapcenter')}}')"><span class="c-yellow">[分销商]</span>当前佣金:{{$fx_userinfo['money']}}</div>
                                    <div>
                                        <span class="f14 c-gray" onclick="javascript:$.href('{{u('UserCenter/fxexchange')}}')">立即兑换</span><i class="icon iconfont c-gray2 vat">&#xe623;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a  href="#" class="item-link item-content">
                            <div class="item-inner pr10">
                                <div class="item-title-row">
                                    <div class="item-title f14" onclick="javascript:$.href('{{u('UserCenter/wapcenter')}}')"><span class="c-yellow">分销中心</span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                @elseif($user && empty($user['fanweId']) && FANWEFX_SYSTEM)
                    <li>
                        <a onclick="javascript:$.href('{{u('UserCenter/sharechapman')}}')" class="item-link item-content pl0">
                            <div class="item-inner p10">
                                <div class="item-title-row">
                                    <div class="item-title f14" >
                                    <span class="c-black">
                                    @if($fxInfo)
                                            @if($fxInfo['status'] == 0)
                                                分销商审核中，请等待
                                            @elseif($fxInfo['status'] == 2)
                                                分销商申请被拒绝，请重新申请
                                            @endif
                                        @else
                                            申请成为分销商
                                        @endif
                                    </span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
                <li>
                    <a  href="{{ u('Order/index') }}" class="item-link item-content" data-no-cache="true"  class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f16">我的订单</div>
                                <div>
                                    <span class="f12 c-gray">查看全部订单</span><i class="icon iconfont c-gray2 vat">&#xe602;</i>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>

            <ul class="row no-gutter mb10 tc y-orderAct">
                <li class="col-20 pt10 pb5">
                    <a href="{{ u('Order/index',['status'=>2]) }}">
                        <i class="icon iconfont f24 c-gray">&#xe685;</i>
                        @if($totalnum['paymentstatus'])
                            <span class="y-xxcont c-bg c-white">{{$totalnum['paymentstatus']}}</span>
                        @endif
                        <p class="f12">待付款</p>
                    </a>
                </li>
                <li class="col-20 pt10 pb5">
                    <a href="{{ u('Order/index',['status'=>3]) }}">
                        <i class="icon iconfont f24 c-gray">&#xe684;</i>
                        @if($totalnum['shippedstatus'])
                            <span class="y-xxcont c-bg c-white">{{$totalnum['shippedstatus']}}</span>
                        @endif
                        <p class="f12">待发货</p>
                    </a>
                </li>
                <li class="col-20 pt10 pb5">
                    <a href="{{ u('Order/index',['status'=>4]) }}">
                        <i class="icon iconfont f24 c-gray">&#xe686;</i>
                        @if($totalnum['receiptstatus'])
                            <span class="y-xxcont c-bg c-white">{{$totalnum['receiptstatus']}}</span>
                        @endif
                        <p class="f12">待收货</p>
                    </a>
                </li>
                <li class="col-20 pt10 pb5">
                    <a href="{{ u('Order/index',['status'=>1]) }}">
                        <i class="icon iconfont f24 c-gray">&#xe687;</i>
                        @if($totalnum['ratestatus'])
                            <span class="y-xxcont c-bg c-white">{{$totalnum['ratestatus']}}</span>
                        @endif
                        <p class="f12">待评价</p>
                    </a>
                </li>
                <li class="col-20 pt10 pb5">
                    <a href="{{ u('Logistics/index') }}">
                        <i class="icon iconfont f24 c-gray">&#xe688;</i>
                        <p class="f12">退款/售后</p>
                    </a>
                </li>
            </ul>
        </div>
        <ul class="row no-gutter mb10 tc c-bgfff y-xwdicolist">

            <li class="col-25">
                <a href="{{ u('UserCenter/address',['SetNoCity'=>1]) }}" class="item-link item-content" data-no-cache="true">
                    <i class="icon iconfont c-ff7800">&#xe681;</i>
                    <p class="f12">收货地址</p>
                </a>
            </li>
            <li class="col-25">
                <a href="{{ u('UserCenter/collect') }}" class="item-link item-content"  data-no-cache="true">
                    <i class="icon iconfont c-e54946">&#xe68a;</i>
                    <p class="f12">我的收藏</p>
                </a>
            </li>
            <li class="col-25">
                <a href="{{ u('UserCenter/rate') }}"  data-no-cache="true">
                    <i class="icon iconfont c-e54946">&#xe68e;</i>
                    <p class="f12">我的评价</p>
                </a>
            </li>
            <li class="col-25">
                <a href="{{ u('Integral/index') }}"  data-no-cache="true">
                    <i class="icon iconfont c-e54946">&#xe691;</i>
                    <p class="f12">{{$wap_integral}}商城</p>
                </a>
            </li>
            <li class="col-25">
                <a  href="{{ u('UserCenter/signin') }}" data-no-cache="true">
                    <i class="icon iconfont c-579fe7">&#xe667;</i>
                    <p class="f12">每日签到</p>
                </a>
            </li>
            @if(($invitation['userStatus'] == 1 &&  IS_OPEN_FX) || ( $user['isPay'] == 1 && IS_OPEN_FX && $invitation['userStatus'] == 2))
                <li class="col-25">
                    <a href="@if($user){{ u('Invitation/index') }}@else{{ u('User/login') }}@endif"  data-no-cache="true" external>
                        <i class="icon iconfont c-579fe7">&#xe68d;</i>
                        <p class="f12">推荐有礼</p>
                    </a>
                </li>
            @endif
            @if($invitation['userStatus'] == 2 && IS_OPEN_FX && $user['isPay'] == 0)
                <li class="col-25">
                    <a href="@if($user){{ u('UserCenter/userhelp',['isFx'=>1]) }}@else{{ u('User/login') }}@endif"  data-no-cache="true" external>
                        <i class="icon iconfont c-579fe7">&#xe68d;</i>
                        <p class="f12">我要兼职</p>
                    </a>
                </li>
            @endif
            <li class="col-25">
                <a  onclick="$.href('@if($user){{ u('Seller/settled') }} @else {{u('User/login')}} @endif')" href="#" data-no-cache="true">
                    <i class="icon iconfont c-fdb118">&#xe683;</i>
                    <p class="f12">商家入驻</p>
                </a>
            </li>
            <li class="col-25">
                <a  href="{{ u('UserCenter/userhelp') }}" data-no-cache="true">
                    <i class="icon iconfont c-fdb118">&#xe68c;</i>
                    <p class="f12">用户帮助</p>
                </a>
            </li>
        </ul>
        <div class="y-khrx">
            <p><a class="f12 c-red" href="tel:{{$site_config['wap_service_tel']}}">客服电话：{{$site_config['wap_service_tel']}}</a></p>
            <p class="f12 c-gray">服务时间：{{$site_config['wap_service_time']}}</p>
        </div>
    </div>
@stop