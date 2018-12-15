@foreach($list as $vo)
    <li class="ml10">
        <div class="item-content pl0">
            <div class="item-media">
                <img src="{{ formatImage($vo['avatar'],40,40) }}" onerror="this.src='{{ asset('wap/community/client/images/wdtt.png') }}'">
            </div>
            <div class="item-inner">
                <div class="item-title-row c-gray f12">
                    <div class="item-title">{{$vo['userName']}}
                    </div>
                    <div class="item-after">{{$vo['createTime']}}
                    </div>
                </div>
                <div class="y-starcont mb5">
                    <div class="c-gray4 y-star" style="width:4.5rem">
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    </div>
                    <div class="c-red y-startwo" style="max-width:100px;width:{{$vo['star'] * 21.6}}%;">
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                    </div>
                </div>
                <div class="item-subtitle f14 c-black">{{$vo['content']}}</div>
                @if(!empty($vo['reply']))
                    <div class="f12 x-reply mt10 c-black">商家回复：{{ $vo['reply'] }}</div>
                @endif
                @if(!empty($vo['images']))
                    <div class="mt10">
                        @foreach($vo['images'] as $img)
                            <div class="x-commpic">
                                <img class="y-evaluateico-{{$vo['id']}}" src="{{formatImage($img,65,65)}}"/>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </li>
    <script type="text/javascript">
        $(function(){
            /*图片浏览*/
            var myPhotoBrowserStandalone = $.photoBrowser({
                photos : [
                    @foreach($vo['images'] as $img)
                    "{{formatImage($img,500,500)}}",
                    @endforeach
                ]
            });
            //点击时打开图片浏览器
            $(document).off('click',".y-evaluateico-{{$vo['id']}}");
            $(document).on('click',".y-evaluateico-{{$vo['id']}}",function () {
                myPhotoBrowserStandalone.open();
                $(".photo-browser-of").text("/");
                $(".photo-browser-close-link").removeClass("pull-left").addClass("pull-right f14").text("关闭");
            });
            $(document).off('click','.photo-browser-close-link');
            $(document).on('click','.photo-browser-close-link',function(){
                $(".photo-browser-light").remove();
            })
        })
    </script>
@endforeach
