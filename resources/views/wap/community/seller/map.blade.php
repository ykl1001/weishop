<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel=stylesheet href="{{ asset('staff/css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm.min.css') }}?{{ TPL_VERSION }}">
    <link rel="stylesheet" href="{{ asset('wap/community/newclient/suimobile/sm-extend.min.css') }}?{{ TPL_VERSION }}">
    <link rel=stylesheet href="{{ asset('staff/css/jquery.mobile.custom.structure.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/jquery.mobile.icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/theme-a.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/morris.css') }}">
    <script src="{{ asset('js/jquery.1.8.2.js') }}"></script>
    <script src="{{ asset('staff/js/jquery.mobile.custom.min.js') }}"></script>
    <script src="{{ asset('wap/community/newclient/suimobile/zepto.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newclient/suimobile/sm-extend.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('wap/community/newbase.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('js/hammer.min.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <script src="{{ asset('image.js') }}?{{ TPL_VERSION }}" charset='utf-8'></script>
    <style>
        #resetMapPos{
            position: absolute;top: 10px;left: 0px; z-index: 9999;width: 40px;height: 30px;background-color: #fff;line-height: 13px;-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            box-shadow: rgba(0, 0, 0, 0.498039) 0px 0px 6px;
            border-radius: 2px;text-align: center;padding: 5px 0;
        }
        #resetMapPos a{ color: #ff2d4b;  font-size: 12px;}
    </style>
</head>
<body>

<div>
    <!-- /header -->
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>服务范围</h1>
        <a href="javascript:$.return_back()" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>
    <!-- /content -->
    <div role="main" class="ui-content" id="mapPos-form-item" >
        @if(!$isData)
        <div class="x-lh50 clearfix">
            <div class="x-seatxt fl">
                <input type="text" name="address" id="map-address-1" value="" />
            </div>
            <div class="x-seabtn">
                <input type="button" value="搜索" id="map-search-1"/>
            </div>
        </div>
        @endif
        <div style="width: 540px; height: 400px; position: relative; border: 1px solid rgb(204, 204, 204); ">
            <div class="x-map" id="map-container-1" style="width: 540px; height: 400px; position: relative; overflow: hidden; -webkit-transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
            @if(!$isData)
            <div id="resetMapPos"><a class="ui-link">重置<br>范围</a>
            </div>
            @endif
        </div>
        <input type="hidden" name="mapPoint" id="map-point-1" value="@if($data['mapPoint']){{ $data['mapPoint'] }} @endif">
        <input type="hidden" name="mapPos" id="map-pos-1" value="@if($data['mapPosStr']){{ $data['mapPosStr'] }}@endif">
        @if(!$isData)
        <p class="f12 mt15 mb15">请搜索或者手动选择店铺的位置，并划定服务范围。</p>
        <input type="hidden" name="userId" value="{{$args['userId']}}">
        <button class="btn" id="map-save">保存</button>
        @else
                <button class="btn" id="retur_reg">返回</button>
        @endif
    </div>

    <script charset="utf-8" src="http://map.qq.com/api/js?v=2.exp&key={{ Config::get('app.qq_map.key') }}&libraries=geometry,convertor"></script>
    <script type="text/javascript" src="https://3gimg.qq.com/lightmap/components/geolocation/geolocation.min.js"></script>
    <script>
        $.gpsPosition = function (resultFun) {
            var isTranslatePoint = false;
            var gpsLatLng = null;
            var qqcGeocoder = new qq.maps.Geocoder({complete: function (result) {
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
                    if(nowNearPoi.address != "" && nowNearPoi.address.indexOf(nowNearPoi.name) > -1){
                        nowNearPoi.address = "";
                    }
                    address = nowNearPoi.address + nowNearPoi.name;
                } else {
                    if (result.detail.addressComponents.streetNumber != '' && address.indexOf(result.detail.addressComponents.streetNumber) > -1) {
                        address = address.replace(result.detail.addressComponents.streetNumber, '');
                    }
                    if(result.detail.addressComponents.street != result.detail.addressComponents.streetNumber){
                        address += result.detail.addressComponents.street + result.detail.addressComponents.streetNumber;
                    }else{
                        address += result.detail.addressComponents.street;
                    }
                }

                var reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                address = address.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                address = address.replace(reg, '');

                var mapPointStr = gpsLatLng.lat + "," + gpsLatLng.lng;
                resultFun.call(this, gpsLatLng, result.detail.addressComponents.city, address, mapPointStr, result.detail.addressComponents.district);
            }
            });

            var bigios = 0 ;
            if(navigator.userAgent.match(/OS 10_[1-9] /i)  || navigator.userAgent.match(/OS 10_[1-9]_[0-9] /i)){
                bigios = 1;
            }

            if (!window.App && bigios > 0) {
                var geolocation = new qq.maps.Geolocation("{{ Config::get('app.qq_map.key') }}", "myapp");
                geolocation.getLocation(function(result){
                    gpsLatLng = new qq.maps.LatLng(result.lat, result.lng);
                    qqcGeocoder.getAddress(gpsLatLng);
                }, function (error){
                    citylocation.searchLocalCity();
                });
                return;
            }

            var translatePoint = function (position){
                if (isTranslatePoint) {
                    return;
                }
                isTranslatePoint = true;
                var currentLat = position.coords.latitude;
                var currentLon = position.coords.longitude;
                qq.maps.convertor.translate(new qq.maps.LatLng(currentLat, currentLon), 1, function (res) {
                    latlng = res[0];
                    gpsLatLng = latlng;
                    qqcGeocoder.getAddress(gpsLatLng);
                });
            }

            var citylocation = new qq.maps.CityService({
                complete: function (result) {
                    if (isTranslatePoint) {
                        return;
                    }
                    isTranslatePoint = true;

                    gpsLatLng = result.detail.latLng;
                    qqcGeocoder.getAddress(result.detail.latLng);
                }
            });

            if (navigator.geolocation) {
                if (window.App) {
                    App.position();
                    window.js_position = function(Latitude, Longitude) {
                        if (Latitude > 0 && Longitude > 0) {
                            var position = new Object();
                            position.coords = new Object();
                            position.coords.latitude = Latitude;
                            position.coords.longitude = Longitude;
                            position.type = 'app';

                            translatePoint(position);
                        }
                    }
                }

                navigator.geolocation.getCurrentPosition(translatePoint, function (error){
                    citylocation.searchLocalCity();
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 10,
                    timeout: 3000
                });
            } else {
                citylocation.searchLocalCity();
            }
        }
    </script>

    <script type="text/javascript">
    var qqGeocoder1,qqMap1,qqMarker1,qqPolygon1,currentLat,currentLon = null,qqLatLngs1 = null;
    var defaultMapPoint1 = "{{$data['mapPoint']}}";
    var mapCenter1;

    jQuery(function($){
        $(window).load(function(){
            @if (!empty($data['mapPoint']))
                mapCenter1 = new qq.maps.LatLng({{$data['mapPoint']}});
            @endif
            qqMap1 = new qq.maps.Map(document.getElementById("map-container-1"),{
                @if (!empty($data['mapPoint']))
                center: mapCenter1,
                @endif
                zoom: 13
            });
            qqMarker1 = new qq.maps.Marker({
                map:qqMap1,
                @if (!empty($data['mapPoint']))
                position: mapCenter1,
                @endif
                @if($isData)
                    draggable:false,
                @else
                    draggable:true
                @endif
            });

            //重置范围
            $(document).on("touchend","#resetMapPos",function(){
                $.createPolygon1(mapCenter1, true);
            });



            $.createPolygon1 = function(latLng, isReset){
                if(!qqPolygon1 || isReset){
                    var tmpLng = qq.maps.geometry.spherical.computeOffset(latLng, 500, 0);
                    //西北角
                    var wnLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, -90);
                    //东北角
                    var enLatLng = qq.maps.geometry.spherical.computeOffset(tmpLng, 500, 90);
                    //东南角
                    var esLatLng = qq.maps.geometry.spherical.computeOffset(enLatLng, 1000, 180);
                    //西南角
                    var nwLatLng = qq.maps.geometry.spherical.computeOffset(wnLatLng, 1000, 180);
                    qqLatLngs1 = [wnLatLng,enLatLng,esLatLng,nwLatLng];

                    if(!qqPolygon1) {
                        qqPolygon1 = new qq.maps.Polygon({
                            map:qqMap1,
                            editable:true,
                            visible:true,
                            path:qqLatLngs1
                        });
                    } else {
                        qqPolygon1.setPath(qqLatLngs1);
                    }

                } else {
                    var heading = qq.maps.geometry.spherical.computeHeading(mapCenter1, latLng);
                    var distance = qq.maps.geometry.spherical.computeDistanceBetween(mapCenter1, latLng);
                    qqLatLngs1 = new Array();
                    qqPolygon1.getPath().forEach(function(element, index){
                        qqLatLngs1.push(qq.maps.geometry.spherical.computeOffset(element, distance, heading));
                    });
                    qqPolygon1.setPath(qqLatLngs1);
                }

                mapCenter1 = latLng;
            }

            qq.maps.event.addListener(qqMarker1, "dragend", function(event) {
                $("#map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
                $.createPolygon1(event.latLng);
            });
            qq.maps.event.addListener(qqMap1, "click", function(event) {
                qqMarker1.setPosition(event.latLng);
                $.createPolygon1(event.latLng);
                $("#map-point-1").val(event.latLng.getLat() + "," + event.latLng.getLng());
            });

            @if (empty($data['mapPoint']))
                $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                    $.setLocation(gpsLatLng);
                })
            @else
                var mapPos1 = "{{$data['mapPosStr']}}".split("|");
                var mpLatLng1;
                qqLatLngs1 = new Array();
                for(var mpIndex = 0; mpIndex < mapPos1.length; mpIndex++){
                    mpLatLng1 = mapPos1[mpIndex].split(",");
                    qqLatLngs1.push(new qq.maps.LatLng(mpLatLng1[0],mpLatLng1[1]));
                }
                qqPolygon1 = new qq.maps.Polygon({
                    map:qqMap1,
                    editable:true,
                    visible:true,
                    path:qqLatLngs1
                });
            @endif

            qqGeocoder1 = new qq.maps.Geocoder({
                complete : function(result){
                    $.setLocation(result.detail.location);
                }
            });

            $(document).on("touchend","#map-search-1",function(){
                if($.trim($("#map-address-1").val()) != ""){
                    qqGeocoder1.getLocation($("#map-address-1").val());
                }
            });

            $.setLocation = function(latLng) {
                qqMap1.setCenter(latLng);
                qqMarker1.setPosition(latLng);
                $.createPolygon1(latLng);
                $("#map-point-1").val(latLng.getLat() + "," + latLng.getLng());
            }



            @if (empty($data['mapPoint']))
                $.createPolygon1(mapCenter1, true);
            @endif

        })

        $(document).off('touchend', '#map-save');
        $(document).on("touchend","#map-save",function(){
            // var addr = $("#map-address-1").val();
            var mapPoint = $("#map-point-1").val();
            var maplatLngs = new Array();
            qqPolygon1.getPath().forEach(function(element, index){
                maplatLngs.push(element.getLat() + "," + element.getLng());
            });
            $("#map-pos-1").val(maplatLngs.join("|"));
            var mapPos = $("#map-pos-1").val();
            $.post("{{u('Seller/mapSave')}}", {'mapPos':mapPos,'mapPoint':mapPoint}, function(result){
                location.href = "{!! u('Seller/reg') !!}?isdata=0";
            });
        });
        $(document).on("touchend","#retur_reg",function(){
            location.href = "{!! u('Seller/reg') !!}";
        });

        $.return_back = function() {
            location.href = "{!! u('Seller/reg') !!}";
        }
        
    })

    </script>
</body>
</html>