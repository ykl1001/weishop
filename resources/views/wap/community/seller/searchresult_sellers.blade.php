@extends('wap.community._layouts.base')

@section('show_top')
    <style type="text/css">
        .clearinput{
            position: absolute;
            top: -6px;
            right: 1px;
            width: 35px;
            height: 42px;
            display: none;
            background: url("{{ asset('images/ico/clear.png') }}") no-repeat center;
            -webkit-background-size: 16px;
            background-size: 16px;
            z-index: 2;
        }
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{u('Seller/search')}}" data-transition='slide-out' external>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <div class="searchbar x-tsearch">
            <!-- 搜索商家\商品 -->

            <div class="search-input pr dib">
                <form id="search_form" >
                    <input type="search" id='search' placeholder='搜索附近商品或门店' name="keyword" value="{{$option['keyword']}}"/>
                </form>
                <div class="clearinput"></div>
            </div>
            <a class="button button-fill button-primary c-bg cq_search_btn" onclick="searchSub()" >搜索</a>
        </div>
    </header>
@stop

@section('content')

    <div class="content" id=''>
        @if($data)
            @if($goods_count>0)
                <div class="content-block-title f12 c-gray" >&nbsp;附近{{$seller_count}}个门店，共{{$goods_count}}个商品
                    <a href="{{u('Seller/search',['search_type'=>'goods','keyword'=>$keyword])}}" class="fr c-red f12" external>按商品查看<i class="icon iconfont f12 c-gray">&#xe602;</i></a>
                </div>
            @endif
            <div class="list-block media-list y-sylist">
                <ul id="appendList">
                    @include('wap.community.seller.searchresult_sellers_item')
                </ul>
            </div>
            @else


                    <!-- 没有搜索到信息 -->
            <div class="x-serno tc c-gray" style="margin-top:40%">
                <img src="{{ asset('wap/community/newclient/images/cry.png') }}" class="mr5">
                <span>没有找到符合的服务！</span>
            </div>
        @endif
    </div>
@stop
@section($js)
    @include('wap.community._layouts.gps')
    <script type="text/javascript">
        var clientLatLng = null;

        $(function(){
            var clientLatLng = null;
            var clientLatLngs = "{{ $defaultAddress['mapPointStr'] }}".split(',');
            clientLatLng = new qq.maps.LatLng(clientLatLngs[0], clientLatLngs[1]);
            $.computeDistanceBegin = function ()
            {
                if (clientLatLng == null) {
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

            $.getClientLatLng = function() {
                citylocation = new qq.maps.CityService({
                    complete : function(result){
                        clientLatLng = result.detail.latLng;
                        $.computeDistanceBegin();
                    }
                });
                citylocation.searchLocalCity();
            }

            $.computeDistanceBetween = function(obj, mapPoint) {
                var distance = qq.maps.geometry.spherical.computeDistanceBetween(clientLatLng, mapPoint);
                if (distance < 1000) {
                    $(obj).html(Math.round(distance) + 'm');
                } else {
                    $(obj).html(Math.round(distance / 1000) + 'Km');
                }
            }
            $.computeDistanceBegin();

            $(document).on("touchend",".search_submit",function(){
                var keyword = $("#keyword").val();
                $.router.load("{!! u('Seller/search') !!}?keyword=" + keyword, true);
            });
            //caiq
            if($.trim($('#search').val())!=''){
                $('.clearinput').show();
            }
            $('.clearinput').on("touchend",function(){
                $('#search').val('').focus();
                $('.clearinput').hide();
            });
            $('#search').keyup(function(){
                if($.trim($('#search').val())!='')
                    $('.clearinput').show();
                else $('.clearinput').hide();
            });
            //分页
            var pageId = 1,nomore=false;
            $('.content').scroll(function(){
                viewH =$(this).height(),//可见高度
                        contentH =$(this).get(0).scrollHeight,//内容高度
                        scrollTop =$(this).scrollTop();//滚动高度
                if((contentH - viewH - scrollTop)<=50&&!nomore){
                    $.showIndicator();
                    pageId++;
                    dt = {};
                    dt.type = 'a';
                    dt.search_type = 'seller';
                    dt.page = pageId;
                    dt.keyword = "{{$option['keyword']}}";
                    dt.sort = '{{$option['sort']}}';
                    $.get("{{u('Seller/search')}}",dt,function(data){

                        if(data.length==0){
                            nomore = true;
                        }
                        $('.seller_list').append(data);
                        $.hideIndicator();
                    });

                }

            });
        });

        $(document).on("touchend",".x-clearhis",function(){
            $(this).siblings("li").remove();
            $(this).find("span").text("暂无历史记录")
        });
        //caiq
        function searchSub(){
            if($.trim($("#search").val())==''){
                $.toast('请输入关键字！');
                return false;
            }else{
                document.forms.search_form.submit();
            }

        };
        $(".x-seegoods").on('click',function(){
            $(this).parents("li").find(".goodslst").removeClass("none");
            $(this).addClass("none");
        });
    </script>
@stop