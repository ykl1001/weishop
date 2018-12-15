@extends('wap.community._layouts.base')
@section('show_top')@stop

@section('content')
    <div class="content y-sharelinkcont" id=''>
        <div class="y-sharelinkbox" style="background: url('{{$invitation['inviteLogo']}}') 50% 50% no-repeat;background-size:100% 100%">
            <div class="y-sharelinktop">
                <img src="{{$weixinUserDsy['headimgurl'] or $site_config['app_logo'] }}">
                <span class="ml10 mr10">{{$weixinUserDsy['nickname']}}</span><span>正在邀请您注册{{$site_config['site_title']}}</span>
            </div>
            <div class="y-sharelinkbtm">
                <div class="" style="height: 200px">
                    <h3 class="c-white none">成为掌柜有哪些好处？</h3>
                    <ul class="y-sharelinkul none">
                        <li><span></span>自己购买的商品，有返利超优惠</li>
                        <li><span></span>分享商品给朋友，购买成功拿返利</li>
                        <li><span></span>邀请他人成掌柜，他人分享订单也可获返利</li>
                    </ul>
                </div>
                <a href="javascript:$.loginWeixin();" class="button button-fill c-bgc42720 mt10 y-sharelinkbtn"><i class="icon iconfont mr10 f20">&#xe64b;</i>微信快捷登录</a>
                <a href="javascript:$.href('{{u('User/reg',['isGuide'=>1])}}');" class="button button-fill c-yellow5 mt10 y-sharelinkbtn"><i class="icon iconfont mr10 f20">&#xe614;</i>手机号码注册</a>
            </div>
        </div>

    </div>
@stop
@section($js)
    <script type="text/javascript">
        $.loginWeixin = function() {
            var weixinlogin = {'scope':'snsapi_userinfo','state':'fanwe'};
            $.showPreloader('请稍等，正在进入微信...');
            setTimeout(function(){
                window.App ? window.App.wxlogin(JSON.stringify(weixinlogin)) : window.location.href = "{{ u('User/weixin') }}";
            }, 500);
        }
    </script>
@stop