@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
    <style>
        .f-bgtk {
            background: rgba(0,0,0,0.5);
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
        }
        .x-closebg {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        .x-sharepic {
            width: 45%;
            position: absolute;
            top: .25rem;
            right: .5rem;
        }
        .va-2{vertical-align: -2px;}
    </style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="JumpURL('{{ u('Seller/index') }}','#seller_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont va-2">&#xe64c;</span>返回
        </a>
        <h1 class="title">店铺名片</h1>
    </header>
@stop

@section('show_nav')
@stop

@section('contentcss')hasbottom @stop

@section('content')
    <div class="bar bar-footer y-bombtn p0">
        <p class="buttons-row">
            <a href="#" class="button share" external="">分享</a>
            <a href="javascript:$.openappurl();" class="button" external="">保存为图片</a>
        </p>
    </div>
    <div class="content bg_fff" id=''>
        <div class="y-shopcard"><div><img src="{{ asset('code/'.$images) }}"></div></div>
    </div>

    <!-- 分享到微信好友或朋友圈 -->
    <div class="f-bgtk sha-frame none">
        <div class="x-closebg"></div>
        <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
    </div>
@stop


@section($js)
    <script type="text/javascript">
        $.openappurl = function(){
            var openappurl = "{!! urldecode(u('Staff/downimage',['sellerId'=>$seller['id'],'name'=>$seller['name']])) !!}";
            var imageurl = "{!! urldecode($imageurl) !!}";
            if (window.App) {
                App.savepic('{"url":"' + imageurl + '"}');
            } else {
                window.location.href = openappurl;
            }
        }
    </script>
    <script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script><!-- 分享 -->
    <script type="text/javascript">
        <?php
            $share['content'] = trim($share['content']);
            $share['content'] = strip_tags($share['content'],"");
            $share['content'] = ereg_replace("\t","",$share['content']);
            $share['content'] = ereg_replace("\r\n","",$share['content']);
            $share['content'] = ereg_replace("\r","",$share['content']);
            $share['content'] = ereg_replace("\n","",$share['content']);
            $share['content'] = ereg_replace(" "," ",$share['content']);
            $share['content'] = $share['content'] ? $share['content'] : $share['title'];
         ?>
        $(function(){
            $(document).off("click",".sha-frame");
            $(document).on("click",".sha-frame",function(){
                $(this).addClass('none');
            });
            $(document).off('click','.share');
            $(document).on('click','.share', function () {
                if (window.App){
                    var share_data = {share_content:'{!! $share['content'] !!}',share_imageUrl:'{{$share['logo']}}',share_url:'{!! $share['url'] !!}',share_key:'',share_title:'{{$share['title']}}' };
                    window.App.sdk_share(JSON.stringify(share_data));
                }else{
                    $(".sha-frame").removeClass("none");
                }
            });

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
                    title: "{{$share['title']}}", // 分享标题
                    desc: "{!! $share['content'] !!}", // 分享描述
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
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
                    title: "{{$share['title']}}", // 分享标题
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
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
                    title: "{{$share['title']}}", // 分享标题
                    desc: "{!! $share['content'] !!}", // 分享描述
                    link: "{!! $share['url'] !!}", // 分享链接
                    imgUrl: "{{$share['logo']}}", // 分享图标
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