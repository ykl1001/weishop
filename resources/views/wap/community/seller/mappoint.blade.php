@extends('wap.community._layouts.base')

@section('css')
    <style>
        .y-maptwo {
            max-height: none;
            position: relative;
            overflow: hidden;
            height: 500px;
        }
    </style>
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="{{ u('Seller/reg',['isdata'=>$isData]) }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        @if(!$isData)
            <a class="button button-link button-nav pull-right open-popup addr_save" data-popup=".popup-about">
                <span class="icon iconfont c-gray f24">&#xe610;</span>
            </a>
        @endif
        @if(!$isData)
            <h1 class="title f16">选择地址</h1>
        @else
            <h1 class="title f16">查看地址</h1>
        @endif
    </header>
@stop

@section('content')
    <!-- new -->
    <div class="content" id=''>
        <!-- 地图 -->
        <div class="sellermap">
            <div class="searchbar row c-bgfff ml0 mt10 y-dpaddrsac">
                <div class="search-input col-80">
                    <input type="search" id="address1"  @if($isData)readonly="readonly" @endif class="f16" value="{{$data['address']}}">

                </div>
                @if(!$isData)
                    <a class="button button-fill col-20 y-searchbtn c-black f16 addr_save1">搜索</a>
                @endif
            </div>
            <div class="y-maptwo">
                <div id="qqMapContainer" style="min-width:100%;max-width:640px; height:100%;min-height:100%; z-index:1;position:absolute;left:0px;top:0px;"></div>
            </div>
        </div>
        <input type="hidden" id="map_point" value="{{$data['mapPointStr']}}"/>
        <input type="hidden" id="city" value="{{$data['city']}}"/>
    </div>
@stop

@section($js)
    @include('wap.community._layouts.gps')

    <script type="text/javascript">


        var defaultMapPoint = "{{$data['mapPointStr']}}";
        var qqGeocoder,qqMap,qqMarker,citylocation,qqcGeocoder = null;
        $(function(){
            @if (!empty($data['mapPointStr']))
            mapCenter = new qq.maps.LatLng({{$data['mapPointStr']}});
            @else
                mapCenter = null;
            @endif 
            qqMap = new qq.maps.Map(document.getElementById('qqMapContainer'),{
                @if (!empty($data['mapPointStr']))
                center: mapCenter,
                @endif
                zoom: 14
            });
            qqMarker = new qq.maps.Marker({
                @if (!empty($data['mapPointStr']))
                position: mapCenter,
                @endif
                map:qqMap,
                draggable:true
            });

            qq.maps.event.addListener(qqMarker, 'dragend', function(event) {
                qqMarker.setPosition(event.latLng);
                $("#map_point").val(event.latLng.getLat() + ',' + event.latLng.getLng());
                qqcGeocoder.getAddress(event.latLng);
            });
            qq.maps.event.addListener(qqMap, 'click', function(event) {
                qqMarker.setPosition(event.latLng);
                $("#map_point").val(event.latLng.getLat() + ',' + event.latLng.getLng());
                qqcGeocoder.getAddress(event.latLng);
            });

            @if (empty($data['mapPointStr']))
            $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                qqMap.setCenter(gpsLatLng);
                qqMarker.setPosition(gpsLatLng);
                $("#map_point").val(mapPointStr);
                $("#address span").text(address);
                $("#address1").val(address);
                $("#city").val(city);
            })
            @endif

            qqcGeocoder = new qq.maps.Geocoder({
                complete : function(result){
                    var nowNearPoi = null;
                    var nearPoi;
                    for(var nearPoiKey in result.detail.nearPois){
                        nearPoi = result.detail.nearPois[nearPoiKey];
                        if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                            nowNearPoi = nearPoi;
                        }
                    }
                    var address  = result.detail.address;
                    if (nowNearPoi) {
                        address = nowNearPoi.address + nowNearPoi.name;
                    }
                    var reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                    address = address.replace(reg, '');
                    reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                    address = address.replace(reg, '');

                    $("#city").val(result.detail.addressComponents.city);
                    $("#address span").text(address);
                    $("#address1").val(address);
                }
            });
            qqGeocoder = new qq.maps.Geocoder({
                complete : function(result){

                    qqMap.setCenter(result.detail.location);
                    qqMarker.setPosition(result.detail.location);
                    $("#map_point").val(result.detail.location.getLat() + ',' + result.detail.location.getLng());
                    $("#city").val(result.detail.addressComponents.city);
                }
            });


            // 确认地图
            $(document).on("touchend",".addr_save",function(){
                var address = $("#address1").val();
                var mapPoint = $("#map_point").val();
                var city = $("#city").val();
                $.post("{!! u('Seller/mapPointSave') !!}",{address:address,mapPoint:mapPoint,city:city},function(res){
                    $.href("{{ u('Seller/reg',['isdata'=>1]) }}");
                });
            })
            // 搜索位置
            $(document).on("touchend",".addr_save1",function(){
                if($.trim($("#address1").val()) != ""){
                    qqGeocoder.getLocation($("#address1").val());
                    $("#detail_input").val($("#address1").val());
                    $("#address span").text($("#address1").val());
                }
            })

        })

    </script>
@stop

