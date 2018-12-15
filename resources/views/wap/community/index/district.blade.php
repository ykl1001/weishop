@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <h1 class="title f16">选择小区</h1>
        <a class="button button-link button-nav pull-right open-popup c-yellow" onclick="javascript:$.href('{{u('Property/index')}}');" data-popup=".popup-about" external>随便逛逛</a>
    </header>
@stop

@section('content')
    @include('wap.community._layouts.bottom')

    <div class="content" id=''>
        <div class="searchbar row x-searchplot mt10 ml0 pl10 mb10">
            <div class="search-input fl">
                <input type="text" placeholder="输入小区名称" name="keywords" value="{{$args['keywords']}}" id="keywords">
            </div>
            <a class="button button-fill button-primary tc f16 c-black fl" id="search">搜索</a>
        </div>
        <div class="list-block x-splotlst nobor f14">
            <div class="list-block x-splotlst nobor f14">
                <ul>
                    <li class="item-content">
                        <div class="item-inner" onclick="javascript:$.href('{{u('index/cityservice',['type'=>4])}}');">
                            <div class="item-title mycity">当前城市：@if(empty($cityinfo['name'])) 请稍候,正在定位中... @else {{$cityinfo['name']}} @endif</div>
                            <div class="f12 c-gray">切换<i class="icon iconfont f13 ml5">&#xe602;</i></div>
                        </div>
                    </li>
                </ul>
            </div>

          @if(empty($list))
                <div class="x-null pa w100 tc" style="top:47%;">
                    <i class="icon iconfont"></i>
                    <p class="f12 c-gray mt10">很抱歉！没有相关数据！</p>
                </div>
          @else
                {{--<div class="list-block x-splotlst nobor f14">--}}
                    {{--<ul>--}}
                        {{--<li class="item-content">--}}
                            {{--<div class="item-inner" onclick="javascript:$.href('{{u('index/cityservice',['type'=>4])}}');">--}}
                                {{--<div class="item-title">当前城市：{{$cityinfo['name']}}</div>--}}
                                {{--<div class="f12 c-gray">切换<i class="icon iconfont f13 ml5">&#xe602;</i></div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</div>--}}

                <ul>
                    @foreach($list as $item)
                        <li class="item-content" onclick="$.href('{!! u('District/detail', ['districtId'=>$item['id']])!!}')">
                            <div class="item-inner">
                                <div class="item-title">{{$item['name']}}</div>
                                @if($item['province']['name'])
                                    <div class="item-after c-gray">{{$item['province']['name']}}{{$item['city']['name']}}</div>
                                @else
                                    <i class="icon iconfont c-gray f13">&#xe602;</i>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@stop

@section($js)

@include('wap.community._layouts.gps')
<script type="text/javascript">
    //精确定位
    $(function ($)
    {
        @if(empty($orderData['mapPointStr']))
            $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                $.router.load("{{u('Index/district')}}?address="+address+"&location="+mapPointStr+"&city="+city, true);
            })
        @endif

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

//        //手动获取定位
//        $.relocation = function() {
//            $('.ts span').text('定位中请稍候...');
//        }

        <?php $args = http_build_query($args);?>
        var args = "{!! $args !!}";

        $(document).on("touchend","#search",function(){
            var keywords = $("#keywords").val();
            $.router.load("{!! u('Index/district') !!}?"+args+"&keywords=" + keywords, true);
        })

    });
</script>
@stop