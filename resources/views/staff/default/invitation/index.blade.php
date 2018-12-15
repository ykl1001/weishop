@extends('staff.default._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Mine/index')}}','#mine_index_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a class="button button-link button-nav pull-right f_999 f15" onclick="JumpURL('{{u('Invitation/explain')}}','#invitation_explain_view',2)" href="#" data-transition='slide-out'>
            说明
        </a>
        <h1 class="title">分享返现</h1>
    </header>
@stop
@section('content')
    <div class="buttons-row y-cashback">
        <a href="{{u('Invitation/userlists')}}" class="button f12 f_fff">
            <p>成功邀请</p>
            <p><span class="f18">{{$userc['count'] or 0}}</span>人</p>
        </a>
        <a href="{{u('Invitation/records')}}" class="button f12 f_fff">
            <p>您已赚到</p>
            <p><span class="f18">{{$userc['money'] or 0}}</span>元</p>
        </a>
    </div>
    <div class="y-ewm"><img src="{{ u('Invitation/cancode',['val'=>$images['val']])}}"></div>
    <div class="buttons-row y-fxfs f14 y-ddfxbtn">
        <a href="#" class="button">
            <div class="y-fxfsimg"><img src="{{ asset('wap/community/newclient/images/y10.png') }}"></div>
            <p class="f_999">分享给微信好友</p>
        </a>
        <a href="#" class="button">
            <div class="y-fxfsimg"><img src="{{ asset('wap/community/newclient/images/y11.png') }}"></div>
            <p class="f_999">分享到朋友圈</p>
        </a>
    </div>
    <!-- 分享到微信好友或朋友圈 -->
    <div class="f-bgtk sha-frame none">
        <div class="x-closebg"></div>
        <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
    </div>
    <script type="text/javascript">
        $(document).on("click","#{{$id_action.$ajaxurl_page}} .sha-frame",function(){
            $(this).addClass('none');
        });
        $(document).on('click','#{{$id_action.$ajaxurl_page}} .y-ddfxbtn', function () {

            if (window.App){
                var share_data ={share_content:'{!! $invitation['shareContent'] !!}',share_imageUrl:'{{$invitation['shareLogo']}}',share_url:'{!! $link_url !!}',share_key:'',share_title:'{{$invitation['shareTitle']}}'};
                window.App.sdk_share(JSON.stringify(share_data));
            }else{
                $("#{{$id_action.$ajaxurl_page}} .sha-frame").removeClass("none");
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
    </script>
@stop
@section('show_nav')@stop
@section('preloader')@stop