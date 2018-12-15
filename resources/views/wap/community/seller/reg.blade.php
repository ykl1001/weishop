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
        <a class="button button-link button-nav pull-left isExternal" href="{{ u('UserCenter/index') }}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">我要开店</h1>
    </header>
@stop

@section('content')
    <?php
    if($option['type'] && !isset($option['sellerType']))
    {
        $option['sellerType'] = $option['type'];
    }
    ?>
    <!-- new -->
    <div class="content" id=''>
        <!-- 表单 -->
        <div class="list-block media-list y-syt y-wykd">
            <ul>
                <php>
                    $option['sellerType'] = $option['sellerType'] ? $option['sellerType'] : 2;
                </php>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>加盟类型</span>
                            </div>
                            <div class="item-after @if(!$isData)y-league @endif">
                                <span class="c-black">
                                    @if($option['sellerType'] == 2)
                                        商家加盟
                                    @else
                                        个人加盟
                                    @endif
                                </span>
                                <i class="icon iconfont ml10 c-gray2 y-down">&#xe601;</i>
                                <i class="icon iconfont ml10 c-gray2 y-up y-none">&#xe603;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺类型</span>
                            </div>
                            <div class="item-after @if(!$isData) y-store @endif">
                                <span class="c-black">
                                    @if($option['storeType'] == '1')
                                        全国店
                                    @elseif($option['storeType'] == '0')
                                        周边店
                                    @else
                                        全国店
                                    @endif
                                </span>
                                <i class="icon iconfont ml10 c-gray2 y-down">&#xe601;</i>
                                <i class="icon iconfont ml10 c-gray2 y-up y-none">&#xe603;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺LOGO</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label id="imglabel-1" class="img-up-lb"  id="upload-a">
                                            @if(!empty($option['logo']))
                                                <img id="_logoInput" data-field="logoInput" src="{{ formatImage($option['logo'],100,100) }}" alt="" class="y-logo image_upload">
                                            @else
                                                <img id="_logoInput" data-field="logoInput" src="{{ asset('wap/community/client/images/wykdimg.png') }}" alt="" class="y-logo image_upload">
                                            @endif
                                        </label> 
                                        <input type="text" id="logoInput" value="{{$option['logo']}}" class="hideImg" style="display:none;">
                                    </span>
                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺名称</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black"><input id="name" value="{{$option['name']}}"  @if($isData)readonly="readonly" @endif type="text" name="name" placeholder="请输入店铺名称"></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content business_type">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>经营类型</span>
                            </div>
                            <div class="item-after">
                                @if($cate_str)
                                    <span class="c-gray2 tr udb_text">{{$cate_str}}</span>
                                @else
                                    <span class="c-gray2 udb_text">未选择</span>
                                @endif

                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="mt10">
                <li class="item-content" id="checkAddress">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺地址</span>
                            </div>
                            <div class="item-after" id="address">
                                <span class="c-gray2">@if(!empty($option['address'])){{$option['address']}}@else请选择地址@endif</span>
                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black"><input type="text" id="address_detail" value="{{$option['addressDetail']}}"  @if($isData)readonly="readonly" @endif name="address_detail" placeholder="详细地址(门牌号/楼层等)"></span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content gos">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>服务范围</span>
                            </div>
                            <div class="item-after" id="mapPos">
                                <span class="c-black">
                                    @if(!empty($option['mapPosStr']))
                                        已选择
                                    @else
                                        未选择
                                    @endif
                                    <input class="y-edittxt f14" id="mapPosStr" value="{{$option['mapPosStr']}}" type="hidden" name="mapPosStr" >
                                    <input class="y-edittxt f14" id="mapPoint" value="{{$option['mapPoint']}}" type="hidden" name="mapPoint" >
                                </span>
                                <i class="icon iconfont ml10 c-gray2">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content refundAddress">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>退货地址</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input id="refundAddress" value="{{$option['refundAddress']}}" type="text" name="refundAddress" placeholder="请输入退货详细地址">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>手机号码</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" readonly="true" id="mobile" value="{{$login_user['mobile']}}" @if($isData)readonly="readonly" @endif type="text" name="mobile" placeholder="请输入手机号码" maxlength="11">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>店主/法人代表</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="contacts" value="{{$option['contacts']}}" @if($isData)readonly="readonly" @endif type="text" name="contacts" placeholder="请输入店主/法人代表姓名">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>服务电话</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="serviceTel" value="{{$option['serviceTel']}}" type="text" @if($isData)readonly="readonly" @endif name="serviceTel" placeholder="请输入服务电话">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证号码</span>
                            </div>
                            <div class="item-after y-wykdwidth">
                                <span class="c-black">
                                    <input class="y-edittxt f14" id="idcardSn" value="{{$option['idcardSn']}}" type="text" name="idcardSn" placeholder="请输入身份证号码" maxlength="18">
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证正面照片</span>
                            </div>
                            <div class="item-after y-wykdimg">

                                <form>
                                  <span class="c-black">
                                         <label id="imglabel-2" class="img-up-lb"  id="upload-a2">
                                             @if(!empty($option['idcardPositiveImg']))
                                                 <img id="_idcardPositiveImgInput" data-field="idcardPositiveImgInput" class="image_upload" src="{{ formatImage($option['idcardPositiveImg'],320) }}" alt="">
                                             @else
                                                 <img id="_idcardPositiveImgInput" data-field="idcardPositiveImgInput" class="image_upload" src="{{ asset('wap/community/client/images/sfzm.png') }}" alt="">
                                             @endif
                                         </label>
                                             <input type="text" id="idcardPositiveImgInput" value="{{$option['idcardPositiveImg']}}" class="hideImg" style="display:none;">

                                    </span>

                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>身份证背面照片</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label id="imglabel-3" class="img-up-lb" id="upload-a3">
                                            @if(!empty($option['idcardNegativeImg']))
                                                <img id="_idcardNegativeImgInput" data-field="idcardNegativeImgInput" class="image_upload" src="{{ formatImage( $option['idcardNegativeImg'],320) }}" alt="">
                                            @else
                                                <img id="_idcardNegativeImgInput" data-field="idcardNegativeImgInput" class="image_upload" src="{{ asset('wap/community/client/images/sfbm.png') }}" alt="">
                                            @endif
                                        </label>
                                            <input type="text" id="idcardNegativeImgInput" value="{{$option['idcardNegativeImg']}}" class="hideImg" style="display:none;">

                                    </span>

                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title">
                                <i class="c-red vat mr5">*</i>
                                <span>证件照片</span>
                            </div>
                            <div class="item-after y-wykdimg">
                                <form>
                                    <span class="c-black">
                                        <label id="imglabel-4" class="img-up-lb" id="upload-a4">
                                            @if(!empty($option['businessLicenceImg']))
                                                <img id="_businessLicenceImgInput" data-field="businessLicenceImgInput" class="image_upload" src="{{ $option['businessLicenceImg'] }}" alt="">
                                            @else
                                                <img id="_businessLicenceImgInput" data-field="businessLicenceImgInput" class="image_upload" src="{{ asset('wap/community/client/images/yyzz.png') }}" alt="">
                                            @endif
                                        </label>
                                            <input type="text" id="businessLicenceImgInput" value="{{$option['businessLicenceImg']}}" class="hideImg" style="display:none;">
                                        
                                    </span>

                                </form>
                                <i class="icon iconfont ml10 mt20 c-gray2 vat">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner f14">
                        <div class="item-title-row">
                            <div class="item-title y-dpjsh">
                                <i class="c-red vat mr5">*</i>
                                <span>店铺介绍</span>
                            </div>
                            <div class="item-after y-wykddpjs">
                                <span class="c-black">
                                    <textarea class="f14 c-green" id="introduction" name="introduction" maxlength="200" placeholder="选填，200字以内" onfocus="javascript:this.style.textAlign='left';">{{$option['introduction']}}</textarea>
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <input type="hidden" id="map_point" value="{{$option['mapPointStr']}}" />
        <input type="hidden" id="cityId" value="{{$option['cityId']}}" />
        @if(!$isData && !$isCheck)
            <p class="y-bgnone mb10"><a class="y-paybtn f16 submit_btn">提交</a></p>
        @else
            <p class="y-bgnone mb10"><a class="y-paybtn f16 @if($option['isCheck'] == -1)submit_btn @else submit_btn_dsy @endif" data-ischeck="{{$option['isCheck']}}">@if($option['isCheck'] == 0)等待核审@elseif($option['isCheck'] == 1)核审通过@else核审失败,重新提交！@endif</a></p>
        @endif
    </div>
@stop

@section($js)
    <script type="text/javascript">

        $(document).on('click','.image_upload', function () {
            var thisObj = $(this);
            var width = 640;
            if(thisObj.data('field') == 'logoInput'){
                width = 320;
            }
            $(this).fanweImage({
                width:width,
                height:320,
                callback:function(url, target) {
                    thisObj.get(0).src = url;
                    $("#"+thisObj.data('field')).val(url);
                }
            });
        });
    </script>
    <script type="text/javascript">
        var seller_type = "{{ $option['sellerType'] ? $option['sellerType'] : 2 }}"; //个人加盟 商家加盟 , 默认商家加盟
        var store_type = "{{ $option['storeType'] == '0' ? 0 : 1 }}"; //是否是全国店：1全国店 0周边店

        //全国店，周边店部分显示不显示
        if(store_type == 1)
        {
            $("li.gos").addClass("none");
            $("li.refundAddress").removeClass("none");
        }
        else
        {
            $("li.refundAddress").addClass("none");
            $("li.gos").removeClass("none");
        }

        $.getData = function(){
            var obj = new Object();
            // obj.sellerType = $(".seller_type .on").data('type');
            obj.sellerType = seller_type;
            obj.storeType = store_type;
            obj.logo = $("#logoInput").val();
            obj.name = $("#name").val();
            obj.mobile = $("#mobile").val();
            obj.address = $("#address span").text();
            obj.addressDetail = $("#address_detail").val();
            obj.idcardSn = $("#idcardSn").val();
            obj.contacts = $("#contacts").val();
            // obj.serviceFee = $("#serviceFee").val();
            // obj.serviceFee = $("#serviceFee").val();
            obj.serviceTel = $("#serviceTel").val();
            obj.mapPointStr = $("#map_point").val();
            obj.mapPosStr = $("#mapPosStr").val();
            obj.mapPoint = $("#mapPoint").val();
            obj.idcardNegativeImg = $("#idcardNegativeImgInput").val();
            obj.idcardPositiveImg = $("#idcardPositiveImgInput").val();
            obj.businessLicenceImg = $("#businessLicenceImgInput").val();
            obj.introduction = $("#introduction").val();
            obj.refundAddress = $("#refundAddress").val();
            obj.cityId = $("#cityId").val();
            // console.log(obj);
            if (obj.sellerType == 'undefined') {
                obj.sellerType = 1;
            };
            if (obj.storeType == 'undefined') {
                obj.storeType = 0;
            };

            return obj;
        }

        // $(document).on("touchend",".y-sfzdt",function(){
        //     $(".y-sfzall").removeClass('none').show();
        //     return false;
        // })
        // $(document).on("touchend",".y-sfzdalw",function(){
        //     $(".y-sfzall").hide();
        //     return false;
        // })

        // $(document).on("touchend",".y-del",function(){
        //     $(this).parent().remove();
        //     if(!$(".y-wykd ul li p b").hasClass("y-sfz")){
        //         $(".y-wykd ul li p").append('身份证正反面必选<input type="file" class="y-file">');
        //     }
        // })
        $(document).off('touchend', '.y-wykdshlx');
        $(document).on("touchend",".y-wykdshlx",function(){
            if($(".y-sjjm").hasClass("on")){
                $(".y-sjjm").removeClass("on");
                $(".y-sjlx").addClass('none').hide();
            }else{
                $(".y-sjlx").removeClass('none').show();
                $(".y-sjjm").addClass("on");
            }
        })
        /*$(document).on("touchend",".y-wykdshlx",function(){
         $(".y-sjlx").removeClass('none').show();
         $(".y-sjjm").addClass("on");
         })*/
        $(document).off('touchend', '.y-sjlx li');
        $(document).on("touchend",".y-sjlx li",function(){
            $(this).addClass("on").siblings().removeClass("on");
            if($(this).data('type') == 1){
                $(".curent_type").text('个人加盟');
            } else {
                $(".curent_type").text('商家加盟');
            }
        });
        $(document).off('click', '.business_type');
        $(document).on("click",".business_type",function(){
            var data = $.getData();
            $.post("{{u('Seller/saveRegData')}}", data, function(res){
                $.href("{{u('Seller/cate',['isdata'=>$isData])}}");
            }, 'json');
        });

        $(document).off('click', '#checkAddress');
        $(document).on("click","#checkAddress", function(){
            var data = $.getData();
            $.post("{{u('Seller/saveRegData')}}", data, function(res){
                $.router.load("{{u('Seller/mappoint',['isdata'=>$isData])}}", true);
            }, 'json');

        });

        $(document).off('touchend', '.submit_btn_dsy');
        $(document).on("touchend",".submit_btn_dsy",function(){
            $.toast($(this).text());
            return false;
        });

        $(document).off('touchend', '.submit_btn');
        $(document).on("touchend",".submit_btn",function(){
            $.showPreloader('数据提交中...');
            var data = $.getData();
            $.post("{{u('Seller/doreg')}}", data, function(res){
                $.hidePreloader();
                if(res.status == true){
                    $.toast('开店申请提交成功,请等待审核');
                    //$.router.load("{{u('Index/index')}}", true);
                    location.href="{{u('UserCenter/index')}}";
                } else {
                    $.alert(res.msg);
                }
            }, 'json');
        });

        $(function(){

            $(document).off('click', '#mapPos');
            $(document).on("click","#mapPos",function(){
                var data = $.getData();
                $.post("{{u('Seller/saveRegData')}}", data, function(res){
                    $.router.load("{{u('Seller/map',['isdata'=>$isData])}}", true);
                }, 'json');
            })
        })


        $(document).off('touchend', '.y-league');
        $(document).on('touchend','.y-league', function () {
            $(".y-league .y-down").addClass("y-none").siblings("i").removeClass("y-none");
            var buttons1 = [
                {
                    text: '请选择',
                    label: true
                },
                {
                    text: '商家加盟',
                    onClick: function() {
                        $(".y-league span").text("商家加盟");
                        seller_type = 2;
                    }
                },
                {
                    text: '个人加盟',
                    onClick: function() {
                        $(".y-league span").text("个人加盟");
                        seller_type = 1;
                    }
                }
            ];
            var buttons2 = [
                {
                    text: '取消',
                    bg: 'danger'
                }
            ];
            var groups = [buttons1, buttons2];
            $.actions(groups);

            $(document).off('touchend', '.actions-modal-button');
            $(document).on("touchend",".actions-modal-button",function(){
                $(".y-league .y-up").addClass("y-none").siblings("i").removeClass("y-none");
            });
        });

        $(document).off('touchend', '.y-store');
        $(document).on('touchend','.y-store', function () {
            $(".y-store .y-down").addClass("y-none").siblings("i").removeClass("y-none");
            var buttons1 = [
                {
                    text: '请选择',
                    label: true
                },
                {
                    text: '全国店',
                    onClick: function() {
                        $(".y-store span").text("全国店");
                        $("li.gos").addClass('none');
                        $("li.refundAddress").removeClass('none');
                        store_type = 1;
                        if($(".business_type .udb_text").text() != "未选择"){
                            $(".business_type .udb_text").html("重新选择");
                        }
                        $.post("{{u('Seller/saveCate')}}", {cateIds:0}, function(res){}, 'json');
                    }
                },
                {
                    text: '周边店',
                    onClick: function() {
                        $(".y-store span").text("周边店");
                        $("li.gos").removeClass('none');
                        $("li.refundAddress").addClass("none");
                        store_type = 0;
                        if($(".business_type .udb_text").text() != "未选择"){
                            $(".business_type .udb_text").html("重新选择");
                        }
                        $.post("{{u('Seller/saveCate')}}", {cateIds:0}, function(res){}, 'json');
                    }
                }
            ];
            var buttons2 = [
                {
                    text: '取消',
                    bg: 'danger'
                }
            ];
            var groups = [buttons1, buttons2];
            $.actions(groups);

            $(document).off('touchend', '.actions-modal-button');
            $(document).on("touchend",".actions-modal-button",function(){
                $(".y-league .y-up").addClass("y-none").siblings("i").removeClass("y-none");
            });
        });

        //部分IOS返回刷新
        if($.device['os'] == 'ios')
        {
            $(".isExternal").addClass('external');
        }

        var winH = $(".page-group").height(),conth = $(window).height();
        $("input").click(function(){
            if($.device['os'] == 'ios'){
            }else{
                $(".content").scrollTop($(this).offset().top+$(".content").scrollTop()-50);
            }
        });
        $("textarea").click(function(){
            if($.device['os'] == 'ios'){
            }else{
                $(".content").css("padding-bottom",winH/2);
                $(".content").scrollTop($(this).offset().top+$(".content").scrollTop()-50);
                winH = $(".page-group").height();
            }
        });

        $(window).resize(function(){
            if(conth == $(window).height()){
                $("textarea").blur();
                $("input").blur();
            }
        });

        $("textarea").blur(function(){
            $(".content").css("padding-bottom",0);
            winH = $(".page-group").height();
        });
    </script>
@stop