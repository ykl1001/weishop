<?php
$share['content'] = trim($share['content']);
$share['content'] = strip_tags($share['content'],"");
$share['content'] = ereg_replace("\t","",$share['content']);
$share['content'] = ereg_replace("\r\n","",$share['content']);
$share['content'] = ereg_replace("\r","",$share['content']);
$share['content'] = ereg_replace("\n","",$share['content']);
$share['content'] = ereg_replace(" "," ",$share['content']);
$share['content'] = ereg_replace("'","’",$share['content']);
//$share['content'] = $share['content'] ? $share['content'] : $share['title'];

if($shareType == "goods"){
    $share['url'] = u("Goods/detail",['goodsId' => $data['id']]);
}else{
    $share['url'] = u("Seller/detail",['id' => $seller['id']]);
}
$bln = 0;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
    $bln = 0;
} else {
    $bln = 1;
}
?>
<!-- 分享弹窗灰色背景 -->
<div class="y-modal-overlay"></div><!-- y-modal-overlay-visible 加上有动画效果 -->
<!-- 分享弹窗 -->
<div class="y-actions-modal c-bgfff " id="y-xffstcmain"><!-- y-modal-in 加上有动画效果 -->
    <ul class="y-sharecoupontc y-sharecoupontcw100 pt10 clearfix">
        <li class="weiXinF" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg1.png")}}"><p class="c-gray f12">微信好友</p></a></li>
        <li class="weiXinF" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg2.png")}}"><p class="c-gray f12">朋友圈</p></a></li>
        @if($bln == 0)
            <li class="multiShare none" >
                <a href="#">
                    <img src="{{ asset('wap/community/newclient/images/yhqimg7.png')}}">
                    <p class="c-gray f12">多图分享</p>
                </a>
            </li>
        @endif
        <li class="y-qgdshoptc_udb" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg8.png")}}"><p class="c-gray f12">二维码</p></a></li>

        @if($bln == 0)
            <li class="weiboalert" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg3.png")}}"><p class="c-gray f12">微博</p></a></li>
        @endif
        <li class="qqalert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg4.png")}}"><p class="c-gray f12">QQ</p></a></li>
        <li  class="zonealert">
            <a href="#">
                <img src="{{ asset("wap/community/newclient/images/yhqimg5.png")}}">
                <p class="c-gray f12">QQ空间</p>
            </a>
        </li>
        @if($bln == 1)
            <li  class="copy_btn pr " data-clipboard-action="copy" data-clipboard-target="#copy_contents">
                <a href="#">
                    <input type="text" readOnly id="copy_contents" value="{!! $share['url'] !!}&shareUserId={{$seller['id']}}" style="opacity: 0;position: absolute;top: 0;"/>
                    <img src="{{ asset('wap/community/newclient/images/yhqimg6.png')}}">
                    <p class="c-gray f12">复制链接</p>
                </a>
            </li>
        @endif
    </ul>
    <button class="y-btmbtn">取消</button>
</div>
<!-- 分享到微信好友或朋友圈 -->
<div class="f-bgtk sha-frame none">
    <div class="x-closebg"></div>
    <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
</div>
<div class="y-qgdshoptc none" id="y-qgdshoptc_udb">
    <div class="y-qgdshoptcbox">
        <div class="y-tcbg"></div>
        <div class="y-qgdshoptcmain">
            <div class="y-centertwo mb10">
                <div class="y-qgdshoptclogo">



                    <img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{ asset('wap/community/client/images/wdtt.png') }} @endif" width="60">


                    {{--@if($weiXinData['headimgurl'])--}}
                        {{--<img src="{{$weiXinData['headimgurl']}}" width="60">--}}
                    {{--@else--}}
                        {{--<img src="{{ asset('wap/community/newclient/images/y20.png')}}" width="60">--}}
                    {{--@endif--}}
                </div>
                {{--<span class="ml10">{{$nickname}}</span>--}}
                <span class="ml10">{{$user['name']}}</span>


                <span class="ml10">向您推荐</span>
            </div>
            @if($shareType == "goods")
                <p class="f14 c-black pl10 pr10 showimges_t">{{$data['name']}}</p>
                <div class="y-qgdshoptcewm">
                    <img src="{{$data['images']}}" class="showimges_img">
                </div>
            @else
                <p class="f14 c-black pl10 pr10 showimges_t">{{$seller['name']}}</p>
                <div class="y-qgdshoptcewm">
                    <img src="{{$seller['logo']}}" class="showimges_img">
                </div>
            @endif
            <?php
            if($shareType == "goods"){
                $s_url =   u('Seller/cancode',['id'=>$data['id'],'shareType'=>$shareType]) ;
            }else{
                $s_url =   u('Seller/cancode',['id'=>$seller['id']]) ;
            }
            ?>
            <div class="y-ewmandlogo y-tccenter">
                <div class="y-lookewm y-tccenter">
                    <img src="{!! $s_url !!}" class="showimges_cd">
                    <p class="f12">长按或扫描二维码购买</p>
                </div>
                <div class="y-dptclogo">
                    <div>
                        <img src="{{$site_config['app_logo']}}">
                    </div>
                    <p class="f12">{{$site_config['site_name']}}</p>
                </div>
            </div>
            <div class="y-bcspewm openappurl">
                <div class="y-bcspewmbtn">
                    <img src="{{ asset('wap/community/newclient/images/y18.png')}}">
                </div>
                <p class="f13">保存商品二维码图片到手机</p>
            </div>
        </div>
    </div>
</div>
<!--
<div class="y-qgdshoptc none" id="y-qgdshoptc_udb">
    <div class="y-qgdshoptcbox">
        <div class="y-tcbg"></div>
        <div class="y-qgdshoptcmain">
            @if($shareType == "goods")
                <div class="y-qgdshoptclogo"><img src="{{$data['logo']}}" class="showimges_img" width="60"></div>
                <p class="f14 c-black showimges_t">{{$data['name']}}</p>
                <div class="y-qgdshoptcewm"><img src="{{ u('Seller/cancode',['id'=>$data['id'],'shareType'=>$shareType])}}" class="showimges_cd"></div>
            @else
        <div class="y-qgdshoptclogo"><img src="{{$seller['logo']}}" class="showimges_img" width="60"></div>
                <p class="f14 c-black showimges_t">{{$seller['name']}}</p>
                <div class="y-qgdshoptcewm"><img src="{{ u('Seller/cancode',['id'=>$seller['id']])}}" class="showimges_cd"></div>
            @endif
        <div class="y-ewmandlogo y-tccenter none">
            <div class="y-lookewm y-tccenter">
                <img src="{{ asset('wap/community/newclient/images/y19.png')}}"><p class="f12">长按或扫描二维码查看详情</p>
                </div>
                <div class="y-dptclogo"><div>
                        <img src="{{ asset('wap/community/newclient/images/y20.png')}}"></div><p class="f12">掌管生活</p>
                </div>
            </div>
            <div class="y-bcspewm openappurl" ><div class="y-bcspewmbtn"><img src="{{ asset('wap/community/newclient/images/y18.png')}}"></div><p class="f13">保存商品二维码图片到手机</p></div>
        </div>
    </div>
</div>
-->
<div class="y-qgdshoptc none" id="copy_show">
    <div class="y-qgdshoptcbox">
        <div class="y-tcbg"></div>
        <div class="y-qgdshoptcmain">
            <p class="f14 c-gray" style="margin-top: -10px;margin-bottom: 5px;border-bottom: 1px solid #ccc;">浏览器不支持一键复制,请长按复制</p>
            <p class="f12 pr10 pl10" style="color:#4EADE5;word-break:break-all;word-wrap:break-word;">{!! $share['url'] !!}&shareSellerId={{$seller['id']}}</p>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script><!-- 分享 -->
<script type="text/javascript">
    var show_data = {};
    show_data.shareType = "{{$shareType or 'seller'}}";
    show_data.shareUserId  = "{{$loginUserId}}";
    show_data.id  = "{{$data['id']}}";
    <?php
       $s_urls =   u('Seller/shopdetail',['shareUserId'=>$loginUserId,'goodsId'=>$data['id'],'id' =>$data['sellerId'],'shareType'=>$shareType]) ;
    ?>
    //openappurl =  "{!!$s_urls!!}" ;//$(".showimges_cd").attr("src");
    var openappurl =  "{!!$s_urls!!}" ;
    $(function(){
        $(document).off("click",".page-current .openappurl");
        $(document).on("click",".page-current .openappurl",function(){
            if (window.App) {
                App.savepic('{"url":"' + openappurl + '"}');
            } else {
                window.location.href = openappurl+"&html=true";
            }
        });
        $(document).off("click",".page-current .y-modal-overlay,.y-btmbtn");
        $(document).on("click",".page-current .y-modal-overlay,.y-btmbtn",function(){
            $(".y-modal-overlay").removeClass("y-modal-overlay-visible");
            $(".y-actions-modal").removeClass("y-modal-in").addClass("y-modal-out");
        });
        $(document).off("click",".page-current .y-qgdshoptc_udb");
        $(document).on("click",".page-current .y-qgdshoptc_udb",function(){
            $("#y-qgdshoptc_udb").removeClass("none");
            $(".y-modal-overlay").removeClass("y-modal-overlay-visible");
            $(".y-actions-modal").removeClass("y-modal-in").addClass("y-modal-out");
            cakShare();
        });
        $(document).off("click",".page-current .y-qgdshoptc");
        $(document).on("click",".page-current .y-qgdshoptc",function(){
            $("#y-qgdshoptc_udb").addClass("none");
        });
        $(document).off('click','.multiShare');
        $(document).on('click','.multiShare', function () {
            multiShare();
        });
        $(document).off('click','.share');
        $(document).on('click','.share', function () {
            if("{{$loginUserId or 0}}" == 0){
                $.toast("未登录");
                $.router.load("{{u('User/login')}}", true);
                return false;
            }
            isJsShare = false;
            if(show_data.shareType == "goods"){
                $(".showimges_cd").attr("src","{!! u('Seller/cancode',['id'=>$data['id'],'shareType'=>$shareType,'shareUserId'=>$loginUserId]) !!}");
                $(".showimges_img").attr("src","{{$data['logo']}}");
                $(".showimges_t").html("{{$data['name']}}");
                $(".showimges_cu").html("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}");
            }else{
                show_data.shareType = "seller";
                show_data.id  = "{{$seller['id']}}";
                openappurl =   "{!! u('Seller/shopdetail',['shareType'=>'seller']) !!}&id="+show_data.id+"&goodsId="+show_data.id;
                $(".showimges_cd").attr("src","{!! u('Seller/cancode',['id'=>$seller['id']]) !!}");
                $(".showimges_img").attr("src","{{$seller['logo']}}");
                $(".showimges_t").html("{{$seller['name']}}");
                $(".showimges_cu").html("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}");
            }
            if (window.App){
                var banner = {!!json_encode($data['banner'])!!};
                var custom_type = [
                    "CUSTOM_WX",
                    "CUSTOM_WXF",
                    "CUSTOM_IMAGES",
                    "CUSTOM_QR",
                    "CUSTOM_SINA",
                    "CUSTOM_QQ",
                    "CUSTOM_QZ",
                    "CUSTOM_CU"
                ];
                banner = banner ? banner : {!!json_encode($share["logo"])!!};
                if(!$.isArray(banner)){
                    banner = [banner];
                }
                if("{{$shareType}}" == "goods"){
                    var share_data = {
                        share_content:'{!! $data["name"] !!}',
                        share_imageUrl:"{{$share['logo']}}",
                        share_url:"{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}",
                        share_key:'1',
                        share_title:"{{$share['title']}}",
                        custom_type: custom_type,
                        share_imageArr:banner
                    };
                }else{
                    var share_data = {
                        share_content:'{!! $share["content"] or $seller["name"] !!}',
                        share_imageUrl:"{{$share['logo']}}",
                        share_url:"{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}",
                        share_key:'1',
                        share_title:"{{$share['title']}}",
                        custom_type: custom_type,
                        share_imageArr:banner
                    };
                }
                window.App.sdk_share(JSON.stringify(share_data));
        }else{
            $.showWxBotton();
            $(".y-modal-overlay").addClass("y-modal-overlay-visible");
            $("#y-xffstcmain").addClass("y-modal-in").removeClass("y-modal-out");
            clipCopy();
        }
    });
    $(document).off("click",".page-current .sha-frame");
    $(document).on("click",".page-current .sha-frame",function(){
        $(this).addClass('none');
    });
    $(document).on("click",".page-current .weiXinF",function(){
        show_weix_alert();
    });
    //分享到QQ空间
    $(document).off('click','.zonealert');
    $(document).on('click','.zonealert',function(){
        if(isJsShare){
            zoneShare("{!! $share['url'] !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId,"{{$nickname}}为您推荐一件新品！",show_js_data.name,'{{$site_config['site_title']}}',show_js_data.image);
            cakShare();
        }else{
            if("{{$bln}}" == 1){
                show_weix_alert();
            }else{
                zoneShare("{!! u('Goods/detail') !!}?&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['content'] !!}",'{{$site_config['site_title']}}',"{!! $share['logo'] !!}");
            }
        }
    });
    //分享到QQ
    $(document).off('click','.qqalert');
    $(document).on('click','.qqalert',function(){
        if(isJsShare){
            zoneShare("{!! u('Goods/detail') !!}?goodsId="+show_js_data.id+"&shareUserId={{$loginUserId}}&shareSellerId="+show_js_data.sellerId,"{{$nickname}}为您推荐一件新品！",show_js_data.name,'{{$site_config['site_title']}}',show_js_data.image,1);
            cakShare();
        }else{
            if("{{$bln}}" == 1){
                show_weix_alert();
            }else{
                zoneShare("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['content'] !!}",'{{$site_config['site_title']}}',"{!! $share['logo'] !!}",1);
                cakShare();
            }
        }
    });
    //分享到新浪微博
    $(document).off('click','.weiboalert');
    $(document).on('click','.weiboalert', function () {
        if(isJsShare){
            weiboShare("{!! u('Goods/detail') !!}?goodsId=" + show_js_data.id + "&shareUserId={{$loginUserId}}&shareSellerId=" + show_js_data.sellerId, "{{$nickname}}为您推荐一件新品！", show_js_data.image,'',function(){
                cakShare();
            });
        }else {
            weiboShare("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['logo'] !!}",'',function(){
                cakShare();
            });
        }
    });
    //微信分享配置文件
    wx.config({
        debug: false, // 调试模式
        appId: "{{$weixin['appId']}}", // 公众号的唯一标识
        timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
        nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
        signature: "{{$weixin['signature']}}",// 签名
        jsApiList: ['checkJsApi','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 需要使用的JS接口列表
    });
    $.showWxBotton = function (){
        wx.ready(function () {
            var share_datas = {};
            if(show_data.shareType == "goods"){
                share_datas.share_content = '{!! $data["name"] !!}';
                share_datas.share_imageUrl= "{{$share['logo']}}";
                share_datas.share_url= "{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}";
                share_datas.share_title= "{{$share['title']}}";
            }else{
                share_datas.share_content='{!! $share["content"] !!}';
                share_datas.share_imageUrl="{{$share['logo']}}";
                share_datas.share_url="{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}";
                share_datas.share_title="{{$share['title']}}";
            }
            // 在这里调用 API
            wx.onMenuShareAppMessage({
                title: share_datas.share_title, // 分享标题
                desc: share_datas.share_content, // 分享描述
                link: share_datas.share_url, // 分享链接
                imgUrl: share_datas.share_imageUrl, // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    $.toast('分享成功');
                    cakShare();
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareTimeline({
                title: share_datas.share_title, // 分享标题
                desc: share_datas.share_content, // 分享描述
                link: share_datas.share_url, // 分享链接
                imgUrl:  share_datas.share_imageUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    $.toast('分享成功');
                    cakShare();
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQQ({
                title: share_datas.share_title, // 分享标题
                desc: share_datas.share_content, // 分享描述
                link: share_datas.share_url, // 分享链接
                imgUrl:  share_datas.share_imageUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    $.toast('分享成功');
                    cakShare();
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareWeibo({
                title: share_datas.share_title, // 分享标题
                desc: share_datas.share_content, // 分享描述
                link:share_datas.share_url, // 分享链接
                imgUrl: share_datas.share_imageUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    $.toast('分享成功');
                    cakShare();
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQZone({
                title:share_datas.share_title, // 分享标题
                desc: share_datas.share_content, // 分享描述
                link: share_datas.share_url, // 分享链接
                imgUrl:  share_datas.share_imageUrl, // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    $.toast('分享成功');
                    cakShare();
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    }
    if("{{$bln}}" == 1){
        /* $(document).off("click",".page-current .copy_btn");
         $(document).on("click",".page-current .copy_btn",function(){
         show_weix_alert();
         });*/
    }
    });
    function cakShare (){
        if(isJsShare){
            $.post("{!! u('Index/setShareNum') !!}",show_data,function(res){
                if(res.data == 1) {
                    $(".share_num" + show_js_data.id).html(parseInt($(".share_num" + show_js_data.id).html()) + 1);
                }
            });
        }else{
            $.post("{!! u('Index/setShareNum') !!}",show_data,function(res){});
        }
    }
</script>
<script type="text/javascript" src="{{ asset('wap/community/newclient/js/dist/clipboard.min.js') }}"></script><!-- 复制 -->