@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="javascript:$.href('{{ u('Index/index') }}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$data['typeName']}}专区</h1>
        <a class="button button-link button-nav pull-right open-popup y-tcimage" data-popup=".popup-about" href="#">
            <!-- 分享 -->
            <i class="icon share iconfont c-black f12">规则</i>
        </a>
    </header>
@stop

@section($css)
<style>
    .list-block ul ul {
        padding-left: 0;
    }
    .rest {
        border: 1px solid #313233;
        height: .9rem;
        line-height: 0.2rem;
        padding: 0.5rem;
        border-radius: .2rem;
    }
</style>
@stop

@section('content')
    @include('wap.community._layouts.bottom')
    @if(empty($data))
        <div class="x-null pa w100 tc">
            <i class="icon iconfont">&#xe645;</i>
            <p class="f12 c-gray mt10">很抱歉，没有该专区!</p>
        </div>
    @else
        <div class="content infinite-scroll infinite-scroll-bottom"  data-distance="50" id="">

            <div class=" c-bgfff" style="line-height: 0.5">
                <a class="db"><img src="{{ $data['image'] }}" class="w100"></a>
            </div>

            <!-- 附近推荐商户 -->
            <div class="list-block media-list y-sylist mt10">
                <ul id="wdddmain" class="row no-gutter y-recommend">

                </ul>
            </div>

            <div class="pa w100 tc allEnd none">
                <p class="f12 c-gray mt5 mb5">没有更多了</p>
            </div>
            <!-- 加载提示符 -->
            <div class="infinite-scroll-preloader none">
                <div class="preloader"></div>
            </div>
        </div>


        <script type="text/tpl" id="content_html">
            <ul class="y-cancelreason tl f13">
                <p>{!! $data['content'] !!}</p>
            </ul>
        </script>

    @endif
@stop

@section($js)
    @include('wap.community._layouts.gps')

    <script>
        var qqcGeocoder = null;
        var clientLatLngs = "{{ $args['mapPoint'] }}".split(',');
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
                $(obj).html(Math.round(distance) + 'm');
            } else
            {
                $(obj).html(Math.round(distance / 1000 * 100) / 100 + 'Km');
            }
        }

        // 处理返回值
        function HandleResult(res)
        {
            if (res.code < 0)
            {
                $.toast('您未登录，无法加入购物车');
                window.location.href = "{{u('User/login')}}";
            }
            else if (res.code > 0)
            {
                $.toast(res.msg);
            }

            return false;
        }

        $(function(){
            var id = "{{ Input::get('id') }}";

            @if(empty($defaultAddress['mapPointStr']))
                $.gpsPosition(function(gpsLatLng, city, address, mapPointStr,area){
                    $.router.load("{{u('Index/special')}}?id="+id+"&address="+address+"&mapPointStr="+mapPointStr+"&city="+city+"&area="+area, true);
                })
            @endif



            //简介
            var textcancel = $("#content_html").html();
            $(document).on("touchend",".y-tcimage",function(){
                $.modal({
                    title:  '专题介绍',
                    text: textcancel,
                    buttons: [
                        {
                            text: '取消',
                            bold:true,
                            onClick: function() {

                            }
                        }
                    ]
                })
            });
            //购买
            $(document).on("touchend", ".rest", function ()
            {
                var sellerId = $(this).attr('data-sellerId');
                var goodsId = $(this).attr('data-goodsId');
                var normsId = $(this).attr('data-normsId');
                var storeType = $(this).attr('data-storeType');
                var num = $(this).attr('data-num')*1+1;
                $.post("{{u('Goods/saveCart')}}", { sellerId:sellerId,type:1,goodsId: goodsId, normsId: normsId, num: num, serviceTime: 0 }, function(res){
                    if(res.code == 0){
                        if(storeType == 1){
                            $.href('{{u("Goods/detail")}}'+'?showgo=1&goodsId='+goodsId);
                        }else{
                            $.href('{{u("Goods/index")}}'+'?type=1&showgo=1&id='+sellerId);
                        }
                    }
                    HandleResult(res);
                } );
            });


            //上拉
            var groupLoading = false;
            var groupPageIndex = 2;
            var nopost = 0;
            var type = "{{$data['type']}}"
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                if(nopost == 1){
                    return false;
                }
                // 如果正在加载，则退出
                if (groupLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');
                groupLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = groupPageIndex;
                data.id = id;

                $.post("{{ u('Index/specialList') }}", data, function(result){
                    groupLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        groupPageIndex++;
                        $('#wdddmain').append(result);
                        $.computeDistanceBegin();
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                        nopost = 1;
                    }
                });
            });

            //ajax加载商家或者商品列表
            var ajaxData = {page:1,id:id};
            var ajaxObj = $("#wdddmain");
            var ajaxUrl = "{{ u('Index/specialList') }}";
            $.ajaxListFun(ajaxObj, ajaxUrl, ajaxData, function(result){
                $.computeDistanceBegin();
            });

        })
    </script>
@stop