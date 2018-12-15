@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a href="{{ u('Index/cityservice',['type'=>2,'oneself'=>$oneself]) }}"><h1 class='title'>{{$cityinfo['name']}}<i class="icon iconfont f14 ml5 c-gray">&#xe601;</i></h1></a>
    </header>
@stop


@section('content')
    <style>
        .y-xzaddrcont{background: #efeff4}
        .x-comment.list-block .item-content{align-items: center; -webkit-align-items: center;}
        .x-comment li.active .y-addron{text-indent: 0; border:0;}
    </style>

    <div class="content y-xzaddrcont">
        <div class="y-map">
            <div id="qqMapContainer" style="min-width:100%;height:4em;min-height:80%;"></div>
            <div class="searchbar mapurl">
                <div class="search-input">
                    <label class="icon iconfont c-gray2" for="search">&#xe65e;</label>
                    <input type="search" id="search" placeholder="输入小区、写字楼、学校等">
                </div>
            </div>
        </div>
        <div class="pt5 pb5 pl10 pr10 f12 c-bgfff c-black mb10" id="nowaddress">
            <i class="icon iconfont">&#xe60d;</i><span id="myaddress">自动定位当前地址</span><i class="icon iconfont f12 fr">&#xe602;</i>
        </div>

        <div class="list-block media-list x-comment nobor">
            <ul>
        @if(!empty($list))
            @foreach($list as $v)
                <li class="x-setDuf @if($defaultAddress['addressId'] == $v['id']) active @endif" data-id="{{ $v['id'] }}">
                    <div class="item-content">
                        <div class="item-media">
                            <i class="icon iconfont mr5 f20 vam c-red y-addron x-setDuf">&#xe612;</i>
                        </div>
                        <div class="item-inner">
                            <div class="item-title-row c-black f14">
                                <div class="item-title">{{ $v['name'] }}</div>
                                <div class="item-after">{{ $v['mobile'] }}</div>
                            </div>
                            <div class="item-subtitle f12 c-gray">{{ $v['address'] }}</div>
                        </div>
                    </div>
                </li>
            @endforeach
        @endif
            </ul>
        </div>
        @if(!empty($userId))
            <a href="{{ u('UserCenter/address',['change'=>2])}}"><div class="tc pt5 pb5 c-bgfff mt10"><i class="icon iconfont c-red f16 mr5">&#xe61d;</i>管理收货地址</div></a>
        @endif

    </div>
@stop

@section($js)
<script type="text/javascript">
    var mapurl = "{!! urldecode(u('Index/addrsearch',['address'=>$cityAddress['address'],'mapPointStr'=>$cityAddress['mapPointStr'],'cityId'=>$cityAddress['cityId'],'oneself'=>$oneself])) !!}";
    $(document).on("touchend",".mapurl",function(){
        window.location.href = mapurl;
    })
</script>
@include('wap.community._layouts.gps')
<script type="text/javascript">
    //精确定位
    $(function(){
        var oneself = "{{$oneself}}";

        $(".x-comment li").click(function(){
            var id = $(this).attr("data-id");
            $.setDefaultAdd(id);
        })

        $.setDefaultAdd = function(id){
            $.post("{{ u('UserCenter/setdefault2') }}",{id:id},function(res){
                if(res.code == 0){
                    if(oneself == 1){
                        $.router.load("{!! u('Oneself/index') !!}", true);
                    }else{
                        $.router.load("{!! u('Index/index') !!}", true);
                    }
                }
            },"json");
        }

        $("#nowaddress").click(function(){
            $("#myaddress").html('定位中请稍候');

            $.gpsPosition(function(gpsLatLng, city, address, mapPointStr,area){
                var data = {
                    "address":address,
                    "mapPointStr":mapPointStr,
                    "city":city,
                    "area":area,
                    "isSetCity":1
                };
                $.post("{{ u('Index/relocation2') }}",data,function(status){
                    if(status.code == 1){
                        $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                        $("#myaddress").html("抱歉，当前城市未开通服务，请选择其他城市吧");
                    }else{
                        $("#myaddress").html(address);
                        if(oneself == 1){
                            $.router.load("{!! u('Oneself/index') !!}", true);
                        }else{
                            $.router.load("{!! u('Index/index') !!}", true);
                        }
                    }
                },'json')
            })
        });

        //部分IOS返回刷新
        if($.device['os'] == 'ios')
        {
            $(".isExternal").addClass('external');
        }
    });
</script>

@stop