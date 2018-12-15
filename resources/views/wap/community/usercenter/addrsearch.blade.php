@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav y-barnav">
    <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <div class="searchbar">
        <div class="search-input">
            <label class="icon iconfont c-gray2 f14" for="search">&#xe65e;</label>
            <input type="search" id="addr_search" placeholder="请输入小区、写字楼、学校等">
            <span class="del iconfont" style="position:absolute;top:1px;right:10px;display:none;">&#xe630;</span>
        </div>
    </div>
    <a class="button button-link button-nav pull-right open-popup addr_save" data-popup=".popup-about" id="search_btn" >
        <span class="icon iconfont c-gray f15">搜索</span>
    </a>
</header>
@stop

@section('content')
    
    <!-- /content -->
    <!-- <div role="main" class="ui-content" id="addlst">
    </div>
    <div id="qqMapContainer" style="display:none;min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div> -->

    <div class="content y-xzaddrcont">
        <div id="qqMapContainer" style="display:none;min-width:100%;max-width:640px; height:100%;min-height:80%; position:absolute;left:0px;top:0px;"></div>
        <div class="list-block y-syt y-xzaddr" id="addlst">
            <!-- <ul>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title">
                            <i class="icon iconfont c-gray f20">&#xe60d;</i><span class="f14">[当前]群生国际E区</span>
                            <p class="f12 c-gray ml20">八一七中路</p>
                        </div>
                    </div>
                </li>
            </ul> -->
        </div>
    </div>
@stop

@section($js)
@include('wap.community._layouts.gps')
<script type="text/javascript">
    var qqGeocoder,qqcGeocoder,qqMap,qqMarker,currentLat,currentLon = null,citylocation = null;

    var mapurl = "{!! urldecode(u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity'),'address'=>$defaultAddress['address'],'mapPointStr'=>$defaultAddress['mapPointStr'],'cityId'=>$defaultAddress['cityId'],'newadd'=>Input::get('newadd')])) !!}";
    var url = "{!! urldecode(u('UserCenter/addressdetail',['SetNoCity'=>Input::get('SetNoCity'),'address'=>$defaultAddress['address'],'mapPointStr'=>$defaultAddress['mapPointStr'],'cityId'=>$defaultAddress['cityId'],'newadd'=>Input::get('newadd')])) !!}";
    var nullimg = "{{  asset('wap/community/client/images/ico/error.png') }}";
    var cartIds = "{{ Input::get('cartIds') }}";
    var id = "{{ Input::get('id') }}";
    var plateId = "{{ Input::get('plateId') }}";
    var postId = "{{ Input::get('postId') }}";
    var change = "{{ Input::get('change') }}";
    var mapCenter = null;
    
    $(function(){
        $(document).ready(function(){
            $("input[id=addr_search]").focus();
        });
        $(".y-xzaddrcont").css("min-height",$(window).height()-$(".bar-nav").height());

        if(cartIds != '') {
            url += "&cartIds=" + cartIds;
            mapurl += "&cartIds=" + cartIds;
        }
        if(plateId > 0) {
            url += "&plateId=" + plateId + "&postId=" + postId;
            mapurl += "&plateId=" + plateId + "&postId=" + postId;
        }
        if(id > 0) {
            mapurl += "&id=" + id;
            url += "&id=" + id;
        }
        if(change > 0) {
            mapurl += "&change=" + change;
            url += "&change=" + change;
        }

        @if (!empty($data['mapPointStr']))
        mapCenter = new qq.maps.LatLng({{$data['mapPointStr']}});
        @endif

        $.setLocation = function(latLng) {
            qqMap.setCenter(latLng);
            qqMarker.setPosition(latLng);
        }
            
        qqMap = new qq.maps.Map(document.getElementById("qqMapContainer"),{
            @if (!empty($data['mapPointStr']))
            center: mapCenter,
            @endif
            zoom: 13
        });
        qqMarker = new qq.maps.Marker({
            @if (!empty($data['mapPointStr']))
            position: mapCenter,
            @endif
            map:qqMap,
            draggable:true
        });

        qqcGeocoder = new qq.maps.Geocoder({
            complete : function(result){
                var nowNearPoi = null;
                var nearPoi;
                html = '';
                if (result.detail.nearPois.length > 0) {
                    var address = "";
                    var reg = "";

                    html += '<ul class="x-addlst">';
                    for(var nearPoiKey in result.detail.nearPois){
                        nearPoi = result.detail.nearPois[nearPoiKey];
                        if (nowNearPoi == null || nowNearPoi.dist > nearPoi.dist) {
                            nowNearPoi = nearPoi;
                        }
                        address  = nearPoi.address;
                        reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                        address = address.replace(reg, '');
                        reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                        address = address.replace(reg, '');
                        reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                        address = address.replace(reg, '');
                        reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                        address = address.replace(reg, '');

                        html += '<li data-mappoint="'+nearPoi.latLng+'" data-name="'+nearPoi.name+'" data-city="'+result.detail.addressComponents.city+'" data-address="'+address+'" data-area="'+result.detail.addressComponents.district+'" class="item-content"><div class="item-inner"><div class="item-title"><i class="icon iconfont c-gray f20">&#xe60d;</i><span class="f14">'+nearPoi.name+'<p class="f12 c-gray ml20">'+nearPoi.address+'</p></div></div></li>';
                    }
                    html += '</ul>';
                } else {
                    if ($("#addlst ul li").length < 1) {
                        html += '<div class="x-null pa w100 tc"><i class="icon iconfont">&#xe645;</i><p class="f12 c-gray mt10">很抱歉！未找到符合条件的地址</p></div>';
                    }
                }

                $("#addlst").append(html);
            }
        });

        @if (empty($data['mapPointStr']))
            $.gpsPosition(function(gpsLatLng, city, address, mapPointStr){
                $.setLocation(gpsLatLng);
                qqcGeocoder.getAddress(gpsLatLng);
            })
        @else
            $.setLocation(mapCenter);
            qqcGeocoder.getAddress(mapCenter);
        @endif

        qq.maps.event.addListener(qqMarker, 'dragend', function(event) {
                    qqMarker.setPosition(event.latLng);
                    qqcGeocoder.getAddress(event.latLng);
                });
        qq.maps.event.addListener(qqMap, 'click', function(event) {
            qqMarker.setPosition(event.latLng);
            $(".x-addlst").empty();
            qqcGeocoder.getAddress(event.latLng);
        });

        $.setLocation = function(latLng) {
            qqMap.setCenter(latLng);
            qqMarker.setPosition(latLng);
        }


        qqGeocoder = new qq.maps.Geocoder({
            complete : function(result){
                $("#addlst").empty();
                var location = result.detail.location;
                $.setLocation(location);
                qqcGeocoder.getAddress(location);
            }
        });

        var searchService;
            searchService = new qq.maps.SearchService({
                //检索成功的回调函数
                complete: function(results) {
                    $("#addlst").empty();
                    var pois = results.detail.pois;
                    var html = '';
                    if (pois.length > 0) {
                        html += '<ul class="x-addlst">';
                        for (var i = 0, l = pois.length; i < l; i++) {
                            var poi = pois[i];
                            if(poi.address == undefined || poi.address == ""){
                                poi.address = poi.name;
                            }
                            html += '<li data-mappoint="' + poi.latLng.lat +','+ poi.latLng.lng + '" data-name="' + poi.name + '" data-lat="' + poi.latLng.lat +'" data-lng="'+ poi.latLng.lng + '" data-address="' + poi.address  + '" class="item-content"><div class="item-inner"><div class="item-title"><i class="icon iconfont c-gray f20">&#xe60d;</i><span class="f14">' + poi.name + '<p class="f12 c-gray ml20">' + poi.address + '</p></div></div></li>';
                        }
                        html += '</ul>';
                    } else {
                        html += '<div class="x-null pa w100 tc"><i class="icon iconfont">&#xe645;</i><p class="f12 c-gray mt10">很抱歉！未找到符合条件的地址</p></div>';
                    }
                    $("#addlst").append(html);

                }
            });
 
        $('#addr_search').keyup(function() {
            var addr = "{{ $cityinfo['name'] }}";
            addr += $.trim($("#addr_search").val());

			if(addr!=''){
				$('.search-input .del').show();
			}else{
				$('.search-input .del').hide();
			}

        });

        $('#search_btn').click(function() {
            var addr = $.trim($("#addr_search").val());
            if(addr==''){
                $.alert('请输入搜索关键字');return false;
            }
            //根据输入的城市设置搜索范围
            searchService.setLocation("{{ $cityinfo['name'] }}");
            //设置搜索页码

            searchService.setPageIndex(0);
            //设置每页的结果数
            searchService.setPageCapacity(10);
            //根据输入的关键字在搜索范围内检索
            searchService.search(addr);
        });
        
		$('.search-input .del').click(function(){
			$("#addr_search").val('').focus();
			$(this).hide();
		});

        var qqcGeocoder3 = new qq.maps.Geocoder({
            complete : function(result){
                var address  = selectedPointData.detailAddress2;
                var name = selectedPointData.detailAddress;
                var reg = new RegExp("^" + result.detail.addressComponents.country, "gi");
                address = address.replace(reg, '');
                name = name.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.province, "gi");
                address = address.replace(reg, '');
                name = name.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.city, "gi");
                address = address.replace(reg, '');
                name = name.replace(reg, '');
                reg = new RegExp("^" + result.detail.addressComponents.district, "gi");
                address = address.replace(reg, '');
                name = name.replace(reg, '');

                selectedPointData.city = result.detail.addressComponents.city;
                selectedPointData.area = result.detail.addressComponents.district;
                selectedPointData.detailAddress = name;
                selectedPointData.detailAddress2 = address;
                $.post("{{ u('UserCenter/saveMap') }}",selectedPointData,function(res){
                    window.location.href= url;
                },"json");
            }
        });
        var selectedPointData;
        $("#addlst").on("click", ".x-addlst li", function () {
            var address = $(this).attr('data-name');
            var detailAddress2 = $(this).attr('data-address');
            var mapPoint = $(this).attr('data-mappoint');
            var city = $(this).attr('data-city');
            var area = $(this).attr('data-area');

            selectedPointData = {
                    "detailAddress":address,
                    "detailAddress2":detailAddress2,
                    "mapPoint":mapPoint,
                    "city":city,
                    "area":area
            };

            if(!area){
                var latLng = new qq.maps.LatLng($(this).attr('data-lat'),$(this).attr('data-lng'));
                $.toast("正在解析定位信息...");
                qqcGeocoder3.getAddress(latLng);
                return;
            }

            $.post("{{ u('UserCenter/saveMap') }}",selectedPointData,function(res){
                window.location.href= url;
            },"json");
        });

        $("#addlst").on("click", ".x-null a", function () {
            window.location.href= mapurl;
        });

        // $("#addlst .x-null").css("padding-top",$(window).height()*0.2);   

    })
</script>
@stop