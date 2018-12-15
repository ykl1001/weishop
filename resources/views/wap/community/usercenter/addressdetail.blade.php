@extends('wap.community._layouts.base')

@section('css')
<style>
    .y-bjshaddr.list-block .item-media{width: 4.5rem;}
</style>
@stop

@section('show_top')
 <header class="bar bar-nav">
    <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url))javascript:$.href('{!! $nav_back_url !!}cartIds={{Input::get('cartIds')}}') @else javascript:$.back(); @endif" data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <h1 class="title f16">我的{{ $title }}</h1>
</header>
@stop

@section('pid')
    id='page-city-picker'
@stop

@section('content')
    <div class="content">
        <div class="content-block-title">联系人</div>
        <div class="list-block mt10 f14 y-bjshaddr">
            <ul>
                <li class="item-content">
                    <div class="item-media">
                        <span>收货人：</span>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input type="text" name="name"  id="name" placeholder="请输入收货人姓名" value="{{ $data['name'] }}" maxlength="8"/>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media">
                        <span>电&nbsp;&nbsp;&nbsp;&nbsp;话：</span>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input type="text" name="mobile"  id="mobile" placeholder="请输入收货人电话" value="{{ $data['mobile'] }}" maxlength="11"/>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title">收货地址</div>
        <div class="list-block mt10 f14 y-bjshaddr">
            <ul>
                <li class="item-content">
                    <div class="item-media">
                        <span>所在城市：</span>
                    </div>
                    <div class="item-inner">
                        <div class="item-title  cityurl ">
                            <input type="text" id="city-picker" placeholder="请选择所在城市" @if(!empty($data['pca_name'])) value="{{$data['pca_name']}}" @endif>
                        </div>
                        <div class="item-after f12 c-black"><i class="icon iconfont">&#xe602;</i></div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media">
                        <span>地&nbsp;&nbsp;&nbsp;&nbsp;址：</span>
                    </div>
                    <input type="hidden" name="detailAddress" id="address" value="{{ $data['detailAddress'] }}"/>
                    <div class="item-inner ml10">
                        <div class="item-title mapurl" style="white-space: inherit;">
                            <div class="pl0 c-gray2 f12" id="address2" style="margin-left: 0.5em;">@if($data['detailAddress']){{ $data['detailAddress'] }} @elseif($data['address']) {{$data['address']}}  @else 点击选择地址 @endif</div>
                        </div>
                        <div class="item-after f12 c-black"><i class="icon iconfont">&#xe602;</i></div>
                    </div>
                </li>
                <li class="item-content  id=" area-li="">
                    <div class="item-media">
                        <span></span>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input id="address3" type="text" placeholder="详细地址" maxlength="20" value="{{$data['detailAddress2']}}" class="c-gray2 f12">
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media">
                        <span>楼号-门牌号：</span>
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input type="text" name="doorplate" id="doorplate" placeholder="输入楼号门牌号等详细信息" maxlength="20"  value="{{ $data['doorplate'] }}"/>
                        </div>
                    </div>
                </li>
                @if($data['id'] > 0)
                    <!-- 编辑 -->
                    @if(!empty($data['mapPoint']) && !is_array($data['mapPoint']))
                        <input type="hidden" id="map_point" value="{{$data['mapPoint']}}"/>
                    @else
                        <input type="hidden" id="map_point" value="{{$data['mapPointStr']}}"/>
                    @endif
                @else
                    <!-- 新增 -->
                    <input type="hidden" id="map_point" value="{{$data['mapPoint']}}"/>
                @endif
                <input type="hidden" id="id" value="{{ $data['id'] }}" />
                <input type="hidden" id="city_id" value="{{ $data['cityId'] }}" />
                <input type="hidden" id="area_id" value="{{ $data['areaId'] }}" />
                <input type="hidden" id="city_name" value="{{ $data['cityName'] }}" />
                <input type="hidden" id="area_name" value="{{ $data['areaName'] }}" />
            </ul>
        </div>
        <div class="p10">
            <a href="javascript:addr_save();" class="button button-big button-fill button-danger">保存</a>
        </div>
    </div>
@stop

@section($js)
    @include('wap.community._layouts.gps')

    <script type="text/javascript">
    var cartIds = "{{ Input::get('cartIds') }}";

    var mapurl = "{!! urldecode(u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity'),'address'=>$defaultAddress['address'],'mapPointStr'=>$defaultAddress['mapPointStr'],'cityId'=>$defaultAddress['cityId']])) !!}";

    var id = "{{ Input::get('id') }}";
    var plateId = "{{ Input::get('plateId') }}";
    var postId = "{{ Input::get('postId') }}";
    var cityurl  = "{!! urldecode(u('Index/cityservice',['type'=>1,'SetNoCity'=>Input::get('SetNoCity')])) !!}&cartIds="+cartIds;
    var change = "{{ Input::get('change') }}";
    var newadd = "{{ Input::get('newadd') }}";
    var arg = "{{ $arg }}";

    $(function($){
        $(document).on("pageInit", "#page-city-picker", function(e) {
            $("#city-picker").cityPicker({

            });
        });
        $.init();

//        $(document).on("touchend",".cityurl",function(){
//            $.router.load(cityurl, true);
//        })

        $("#nowaddress").click(function(){
            $.showPreloader('定位中请稍候...');
            $.gpsPosition(function(gpsLatLng, city, address, mapPointStr, area){
                var data = {
                    "address":address,
                    "mapPointStr":mapPointStr,
                    "city":city,
                    "area":area
                };
                $.post("{{ u('UserCenter/saveMap') }}",data,function(res){
                    if(res.code == 1){
                        $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                    }else{
                        $("#address").val(address);
                        $("#address2").html(address);
                        $("#city").val(city);
                        $("#map_point").val(mapPointStr);
                        $("#city_id").val(res.data.id);

                        areaSelect = "";
                        areas = res.data.areas;
                        if(areas.length > 0){
                            for(i = 0; i < areas.length; i++){
                                if(res.data.area.id == areas[i].id){
                                    areaSelect += "<option selected value='"  + areas[i].id + "'>" + areas[i].name + "</option>";
                                } else {
                                    areaSelect += "<option value='"  + areas[i].id + "'>" + areas[i].name + "</option>";
                                }

                            }
                            $("#area").append(areaSelect);
                            $("#area-li").removeClass("none");
                        }
                    }
					$.hidePreloader();
                },"json");
            })
        });

        $(".mapurl").unbind("touchend");
        $(document).on("touchend",".mapurl",function(){
            if (cartIds != '' && newadd != 1) {
                mapurl = "{!! u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity')]) !!}&cartIds=" + cartIds;
            }
            if (plateId > 0) {
                mapurl = "{!! u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity')]) !!}&plateId=" + plateId + "&postId=" + postId;
            }
            if(id > 0) {
                mapurl = "{!! u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity')]) !!}&id=" + id;
            }
            if(change > 0) {
                mapurl = "{!! u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity')]) !!}&change=" + change;
            }
            var city_id = $("#city_id").val();
            var old_city_id = "{{$defaultAddress['cityId']}}";
            var map_point = $("#map_point").val();
            var address = $("#address").val();
            if(old_city_id != city_id && city_id != ""){
                mapurl = "{!! u('UserCenter/addressmap',['SetNoCity'=>Input::get('SetNoCity')]) !!}&cityId=" + city_id + "&mapPointStr=" + map_point + "&address=" + address;
            }

            var data = getData();
            $.post("{{ u('UserCenter/saveAddrData') }}",data,function(res){
                $.href(mapurl);
            },"json");
            
        })

        $(document).on("touchend", ".y-mraddrmain", function(){
            $(this).find(".y-fxk .iconfont").toggle();
            if($(this).hasClass("on")){
                $(this).removeClass("on");
            }else{
                $(this).addClass("on");
            }
        });

    })

    function getData(){
        var obj = new Object();
        obj.id = $.trim($("#id").val());
        obj.name = $.trim($("#name").val())
        obj.mobile = $.trim($("#mobile").val());
        obj.detailAddress = $.trim($("#address").val());
        obj.detailAddress2 = $.trim($("#address3").val());
        obj.mapPoint = $.trim($("#map_point").val());
        obj.doorplate = $.trim($("#doorplate").val());
        obj.cityId = $.trim($("#city_id").val());
        obj.areaId = $.trim($("#area_id").val());
        obj.city = $.trim($("#city_name").val());
        obj.area = $.trim($("#area_name").val());
        obj.SetNoCity = "{{ Input::get('SetNoCity') }}";
        return obj;
    }

    function addr_save() {
        $.showPreloader('请稍候...');
        var data = getData();
        $.post("{{ u('UserCenter/saveaddress') }}",data,function(res){
            $.hidePreloader();
            if(res.code == 0){
                if(arg > 0) {
                    var return_url = "{!! u('Order/order') !!}?cartIds=" + arg+'&addressId='+res.data.id;
                }else if(cartIds != '') {
                    var return_url = "{!! u('GoodsCart/index',['cartIds'=>Input::get('cartIds'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                } else if (plateId > 0) {
                    var return_url = "{!! u('Forum/addbbs',['plateId'=>Input::get('plateId'),'postId'=>Input::get('postId'),'addressId' => ADDID]) !!}".replace("ADDID", res.data.id);
                }else if(change > 0) {
                    var return_url = "{!! u('UserCenter/address',['SetNoCity'=>Input::get('SetNoCity')]) !!}?change=" + change;
                }else{
                    var return_url = "{!! u('UserCenter/address',['SetNoCity'=>Input::get('SetNoCity')]) !!}";
                } 
                $.alert(res.msg,function(){
                    $.router.load(return_url, true);
                });
            }else{
                $.alert(res.msg);
            }
        },"json");
    }
</script>
@stop
