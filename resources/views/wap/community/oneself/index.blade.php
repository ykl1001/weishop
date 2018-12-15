@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav y-sybarnav">
        <h1 class="title tl pl10">
            <i class="icon iconfont c-red f17 mr5 left" onclick="$.relocation()">&#xe650;</i> <!-- 重新定位 -->
            <span onclick="$.href('{{ u('Index/addressmap',['oneself'=>1])}}')" class="f15 p-location">
                @if($orderData['address'])
                    {{$orderData['address']}}
                @else
                    <span id="locationName">定位中请稍候</span>
                @endif
            </span>
            <i class="icon iconfont c-gray f13 ml5 right">&#xe602;</i>
        </h1>
        <a class="button button-link button-nav pull-right open-popup" data-popup=".popup-about" href="{{ u('Oneself/search') }}">
            <i class="icon iconfont c-gray x-searchico">&#xe65e;</i>
        </a>
    </header>
@stop
@section('css')
@stop
@section('content')
    @include('wap.community._layouts.bottom')

    <div class="content" id=''>
        <div id="indexAdvSwiper" class="swiper-container my-swiper" data-space-between='0' >
            <div class="swiper-wrapper">
                @foreach($data['banner'] as $key => $value)
                    <div class="swiper-slide pageloading" onclick="$.href('{{ $value['url'] }}')">
                        <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination swiper-pagination-adv"></div>
        </div>
        <div id="indexNavSwiper" class="swiper-container y-swiper indexNavSwiper" data-space-between='0'>
            <div class="swiper-wrapper">
                @for($i = 0; $i < (ceil(count($menu) / 8)); $i++)
                    <div class="swiper-slide">
                        <ul class="y-nav clearfix">
                            @foreach(array_slice($menu,($i * 8),8) as  $v)
                                <?php
                                if (!preg_match("/^(http|https):/", $v['url'])){
                                    $v['url'] = 'http://'.$v['url'];
                                }
                                ?>
                                <li><a href="{{ $v['url'] }}" class="db" external><img src="{{ $v['menuIcon'] }}"><p class="f13">{{ $v['name'] }}</p></a></li>
                            @endforeach
                        </ul>
                    </div>
                @endfor
            </div>
            <div class="swiper-pagination swiper-pagination-nav"></div>
        </div>
        <!-- 无公告时不显示 -->
        <div class="y-sjnotice c-bgfff f12 y-zyscnotice">
            <span class="fl pa p0 c-red"><i class="icon iconfont va-1 p0 mr5">&#xe647;</i>公告：</span><marquee scrollamount="5" class="c-black mr10">{{$data['article'][0]['content']}}</marquee>
        </div>
        @if(count($data['notice']) > 0)
            <ul class="x-advertising clearfix y-tjlist">
                @foreach($data['notice'] as $k=>$value)
                    @if($k < 4)
                        <li><a href="{{$value['url']}}" class="br pageloading"><img src="{{ formatImage($value['icon'],320) }}"></a></li>
                    @endif
                @endforeach
            </ul>
        @endif
        @if(count($data['notice']) > 4)
            <div class="c-bgfff p10">
                @foreach($data['notice'] as $k=>$value)
                    @if($k > 4)
                        <a href="{{$value['url']}}" class="db pageloading"><img src="{{ formatImage($value['icon'],640) }}" class="w100"></a>
                    @endif
                @endforeach
            </div>
            @endif
                    <!-- 优选水果 -->
            @foreach($data['cate'] as $key => $itme)
                @if($itme['goods'])
                    <div class="card y-shopcart y-yxsplist mb10">
                        <div class="card-header">
                            <div class="w100">
                                <span class="c-yellow f14">{{$itme['name']}}</span>
                                <span onclick="$.href('{{u('Goods/index',['type'=>$itme['type'],'id'=>$itme['sellerId'],'cateId'=>$itme['id']])}}')" class="f12 c-gray  fr">
                                    更多
                                    <i class="icon iconfont f14 vat ml5">&#xe602;</i>
                                </span>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="row no-gutter">
                                @foreach($itme['goods'] as $k => $goods)
                                    <div class="col-33" onclick="$.href('{{u('Goods/detail',['goodsId'=>$goods['id']])}}')">
                                        <div class="tc mb10"><img src="{{ formatImage($goods['images'][0],640,640) }}"></div>
                                        <p>{{$goods['name']}}</p>
                                        <p><span class="c-red f14 mr5">￥{{$goods['price']}}</span>
                                            <!--<span class="c-gray y-delgrid f12">￥{{$goods['price']}}</span>--></p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
    </div>
@stop

@section($js)
    @include('wap.community._layouts.gps')

    <script type="text/javascript">
        window.opendoorpage = true;//当前页可以开门
        //精确定位
        $(function(){
            $("#indexAdvSwiper").swiper({"pagination":".swiper-pagination-adv", "autoplay":2500});
            $("#indexNavSwiper").swiper({"pagination":".swiper-pagination-nav"});

            var qqcGeocoder = null;
            var clientLatLng = null;

            @if(!empty($orderData['mapPointStr']))
            var clientLatLngs = "{{ $orderData['mapPointStr'] }}".split(',');
            clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);
            @else
                $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                        $.router.load("{{u('Index/index')}}?address="+address+"&mapPointStr="+mapPointStr+"&city="+city, true);
                    })
            @endif

            $(document).on("touchend",".data-content ul li",function(){
                        var id = parseInt($(this).data('id'));
                        if (id > 0)
                        {
                            $.router.load("{{u('Seller/detail')}}" + "?staffId=" + id, true);
                        }
                    });

            $.computeDistanceBegin = function ()
            {
                if (clientLatLng == null) {
                    $.gpsposition();
                    return;
                }

                $(".compute-distance").each(function ()
                {
                    var mapPoint = new qq.maps.LatLng($(this).attr('data-map-point-x'), $(this).attr('data-map-point-y'));
                    $.computeDistanceBetween(this, mapPoint);
                    $(this).removeClass('compute-distance');
                })
            }

            $.computeDistanceBetween = function (obj, mapPoint)
            {
                var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
                if (distance < 1000)
                {
                    $(obj).html(Math.round(distance) + 'M');
                } else
                {
                    $(obj).html(Math.round(distance / 1000 * 100) / 100 + 'Km');
                }
            }

            $.SwiperInit = function (box, item, url)
            {
                $(box).infinitescroll({
                    itemSelector: item,
                    debug: false,
                    dataType: 'html',
                    nextUrl: url
                }, function (data)
                {
                    $.computeDistanceBegin();
                });
            }

            $.computeDistanceBegin();

            //重新定位
            $.relocation = function(){
                //异步Session清空
                $.post("{{ u('Index/relocation') }}",function(){
                    $.router.load("{{ u('Index/index') }}", true);
                })
            }

            if(window.App && parseInt({{$loginUserId}})>0){
                var result = getDoorKeys();
                window.App.doorkeys(result.responseText);
            }

        });
    </script>
@stop