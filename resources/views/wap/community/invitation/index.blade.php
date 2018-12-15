@extends('wap.community._layouts.base')

@section($css)
<style>
.y-fxfxbtn{line-height:45px;height: 45px;margin: 0 auto;width:60%;}
</style>
@stop
@section('show_top')
    <style>
        .bar .icon{line-height: 2.2rem;}
    </style>
	<header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onClick='$.href("{{u("UserCenter/index")}}")' href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">分享返现</h1>
    </header>
@stop

@section('content')

	<?php
		$bln = 0;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
			$bln = 0;
		} else {
			$bln = 1;
		}
	 ?>
	<div class="content  c-bgfff" id=''>
        {{--<div class="list-block media-list m0 y-sylist">--}}
            {{--<ul>--}}
                {{--<li>--}}
                    {{--<a href="#" class="item-link item-content">--}}
                        {{--<div class="item-media y-yqhhrico"><img src="{{ asset('wap/community/newclient/images/fenxiang.png') }}"></div>--}}
                        {{--<div class="item-inner y-disf">--}}
                            {{--<div class="item-text f12 c-gray y-hgtnone">{{$invitation['shareDescribe'] or "分享至微信、朋友圈等渠道，成功邀请好友即可成为合伙人，多邀多得"}}</div>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</li>--}}
                {{--<li>--}}
                    {{--<a href="#" class="item-link item-content">--}}
                        {{--<div class="item-media y-yqhhrico"><img src="{{ asset('wap/community/newclient/images/makemoney.png') }}"></div>--}}
                        {{--<div class="item-inner y-disf">--}}
                            {{--<div class="item-text f12 c-gray y-hgtnone">{{$invitation['pointsNoExplain'] or "Ⅰ级、Ⅱ级、Ⅲ级合伙人自购或分销订单都会给你返利补贴哦~"}}</div>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</li>--}}
            {{--</ul>--}}
        {{--</div>--}}

        <div class="y-topborgray" style="border-top:none;">
            <div class="y-ewm"><img src="{{ u('Invitation/cancode',['val'=>$images['val']])}}" class="vat"></div>
            <div class="y-tjspbor"><div class="y-tjsptitle c-gray f12"><span>分享至第三方平台</span></div></div>
            <ul class="y-sharecoupontc pt10 clearfix">
                <li class="weixinalertP"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg1.png")}}"><p class="c-gray f12">微信好友</p></a></li>
                <li class="weixinalertF"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg2.png")}}"><p class="c-gray f12">朋友圈</p></a></li>
				@if($bln == 0)
                <li class="weiboalert" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg3.png")}}"><p class="c-gray f12">微博</p></a></li>
				@endif
                <li class="qqalert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg4.png")}}"><p class="c-gray f12">QQ</p></a></li>
                <li  class="zonealert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg5.png")}}"><p class="c-gray f12">QQ空间</p></a></li>
                <li  class="copy_btn pr " data-clipboard-action="copy" data-clipboard-target="#copy_contents">
                    <a href="#">
                        <input type="text" readOnly id="copy_contents" value="{!! $link_url !!}" style="opacity: 0;position: absolute;top: 0;"/>
                        <img src="{{ asset("wap/community/newclient/images/yhqimg6.png")}}">
                        <p class="c-gray f12">复制链接</p>
                    </a>
                </li>
            </ul>
			<div class="y-ddfxbtns none  mt20 mb20">
				<a href="#" class="button button-danger y-fxfxbtn">分享至第三方社交平台</a>
			</div>
            <div class="tc pb10"><a href="{{ u('Invitation/explain') }}" class="c-red f13 lh20 dib y-borbtm">玩法详情</a></div>
            <div class="tc pb10"><a href="{{ u('MakeMoney/index') }}" class="c-red f13 lh20 dib y-borbtm">推荐记录</a></div>
        </div>

        <!-- 分享到微信好友或朋友圈 -->
        <div class="f-bgtk sha-frame none">
            <div class="x-closebg"></div>
            <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
        </div>
    </div>
	<div class="y-qgdshoptc none" id="copy_show">
		<div class="y-qgdshoptcbox">
			<div class="y-tcbg"></div>
			<div class="y-qgdshoptcmain">
				<p class="f14 c-gray" style="margin-top: -10px;margin-bottom: 5px;border-bottom: 1px solid #ccc;">浏览器不支持一键复制,长按复制</p>
				<p class="f12 pr10 pl10" style="color:#4EADE5;word-break:break-all;word-wrap:break-word;">{!! $link_url !!}</p>
			</div>
		</div>
	</div>
    <script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>
@stop


@section($js)
    <script type="text/javascript">
        $(function(){
//            if (window.App){
//                $(".y-ddfxbtns").removeClass("none");
//                $(".y-sharecoupontc").addClass("none");
//            }

            //分享到QQ空间
			$(document).off('click','.zonealert');
			$(document).on('click','.zonealert',function(){
                if (window.App){
                    $.shar_type(4);
                }else{
                    if("{{$bln}}" == 1){
                        show_weix_alert();
                    }else{
                        zoneShare("{!! $Invitation['url'] !!}&shareSellerId={{$seller['id']}}","{{$Invitation['title']}}","{!! $Invitation['content'] !!}",'{{$site_config['site_title']}}',"{!! $Invitation['logo'] !!}");
                    }
                }
			});
			//分享到QQ
			$(document).off('click','.qqalert');
			$(document).on('click','.qqalert',function(){
                if (window.App){
                    $.shar_type(5);
                }else{
                    if("{{$bln}}" == 1){
                        show_weix_alert();
                    }else{
                        zoneShare("{!! $Invitation['url'] !!}&shareSellerId={{$seller['id']}}","{{$Invitation['title']}}","{!! $Invitation['content'] !!}",'{{$site_config['site_title']}}',"{!! $Invitation['logo'] !!}",1);
                    }
                }
			});
            //分享到新浪微博
            $(document).off('click','.weiboalert');
            $(document).on('click','.weiboalert', function () {
                if (window.App){
                    $.shar_type(3);
                }else{
                    weiboShare("{!! $link_url !!}","{{$invitation['shareTitle']}}","{{$invitation['shareLogo']}}");
                }
            });

            $(document).off('click','.page-current .weixinalertP');
            $(document).on("click",".page-current .weixinalertP",function(){
                if (window.App){
                    $.shar_type(2);
                }else{
                    $(".sha-frame").removeClass('none');
                }
            });

            $(document).off('click','.page-current .weixinalertF');
            $(document).on("click",".page-current .weixinalertF",function(){
                if (window.App){
                    $.shar_type(1);
                }else{
                    $(".sha-frame").removeClass('none');
                }
            });

            $(document).off('click','.page-current .sha-frame');
            $(document).on("click",".page-current .sha-frame",function(){
                $(this).addClass('none');
            });
			$(document).off('click','.page-current .y-ddfxbtns');

            $.shar_type = function(type){
                    var banner = {!!json_encode($data['banner'])!!};
                    var custom_type = [
                        "CUSTOM_WX",
                        "CUSTOM_WXF",
                        "CUSTOM_SINA",
                        "CUSTOM_QQ",
                        "CUSTOM_QZ",
                        "CUSTOM_CU"
                    ];
                    banner = banner ? banner : [{!!json_encode($invitation['shareLogo'])!!}];
                var share_data = {
                    share_content:'{!! $invitation['shareContent'] !!}',
                    share_imageUrl:"{{$invitation['shareLogo']}}",
                    share_url:'{!! $link_url !!}',
                    share_key:'',
                    share_title:'{{$invitation['shareTitle']}}' ,
                    share_type:type,
                    custom_type: custom_type,
                    share_imageArr:banner
                };
                window.App.sdk_share(JSON.stringify(share_data));
            }

            {{--if (window.App){--}}
                {{--$.shar_type(6);--}}
            {{--}else{--}}
                {{--$(".page-current .y-sharecoupontc").removeClass("none");--}}
                {{--clipCopy();--}}
                {{--if("{{$bln}}" == 0){--}}
                    {{--//clipCopy();--}}
                {{--}--}}
            {{--}--}}
            $(document).off("click",".page-current .copy_btn");
            $(document).on("click",".page-current .copy_btn",function(){
                if (window.App){
                    $.shar_type(6);
                }else{
                    $(".page-current .y-sharecoupontc").removeClass("none");
                    clipCopy();
                }
            });

            //部分IOS返回刷新
            if($.device['os'] == 'ios')
            {
                $(".isExternal").addClass('external');
            }

            //微信分享配置文件
            wx.config({
                debug: false, // 调试模式
                appId: "{{$weixin['appId']}}", // 公众号的唯一标识
                timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
                nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
                signature: "{{$weixin['signature']}}",// 签名
                jsApiList: ['checkJsApi','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ'] // 需要使用的JS接口列表
            });

            wx.ready(function () {
                // 在这里调用 API
                wx.onMenuShareAppMessage({
                    title: "{{$invitation['shareTitle']}}", // 分享标题
                    desc: "{!! $invitation['shareContent'] !!}", // 分享描述
                    link: "{!! $link_url !!}", // 分享链接
                    imgUrl: "{{$invitation['shareLogo']}}", // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: '{{$invitation['shareTitle']}}', // 分享标题
                    link: '{!! $link_url !!}', // 分享链接
                    imgUrl: '{{$invitation['shareLogo']}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareQQ({
                    title: '{{$invitation['shareTitle']}}', // 分享标题
                    desc: "{!! $invitation['shareContent'] !!}", // 分享描述
                    link: '{!! $link_url !!}', // 分享链接
                    imgUrl: '{{$invitation['shareLogo']}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        alert('分享成功');
                        location.reload();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        });
    </script>
@stop
<script type="text/javascript" src="{{ asset('wap/community/newclient/js/dist/clipboard.min.js') }}"></script><!-- 复制 -->