@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloadingt pageloading" onclick="javascript:$.href('{{$backurl}}');" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">物业门禁</h1>
        @if($data['countDistrict'] > 1)
            <a class="button button-link button-nav pull-right open-popup toedit pageloading changeTo" href="#" data-popup=".popup-about">切换</a>
        @endif
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 未开通物业提示 -->
        @if(!$data)
            <div class="x-null pa w100 tc">
                <i class="icon iconfont">&#xe645;</i>
                <p class="f12 c-gray mt10">您需要先选择小区才可以申请物业</p>
                <a class="f14 c-white x-btn db pageloading" href="{{ u('District/index')}}">马上去选择</a>
            </div>
        @else
            @if($data['isProperty'])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">很抱歉，{{$data['district']['name']}}未开通物业版块</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('District/index')}}">重新选择小区</a>
                </div>
            @endif

            @if($data['isVerify'])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">您未进行身份验证</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('District/userapply',['districtId'=>$data['districtId']])}}" data-no-cache="true">去验证</a>
                </div>
            @endif

            @if($data['isCheck'])
                <div class="x-null pa w100 tc">
                    <i class="icon iconfont">&#xe645;</i>
                    <p class="f12 c-gray mt10">您的身份信息已提交审核，请耐心等待</p>
                    <a class="f14 c-white x-btn db pageloading" href="{{ u('Index/index')}}">去首页逛逛</a>
                </div>
                @endif

                @if(!$data['isProperty'] && !$data['isVerify'] && !$data['isCheck'])
                        <!-- 业主信息 -->
                <div class="list-block media-list x-property bfh0 mb0">
                    <ul>
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-media">
                                    <img src="@if(!empty($user['avatar'])) {{formatImage($user['avatar'],64,64)}} @else {{  asset('wap/community/client/images/wdtx-wzc.png') }} @endif">
                                </div>
                                <div class="item-inner"  onclick="$.href('{!! u('Property/detail', ['id'=>$data['id'],'districtId'=>$data['districtId']]) !!}')">
                                    <div class="item-title-row">
                                        <div class="item-title c-gray f14">业主：<span class="c-white">{{$data['name']}}</span></div>
                                    </div>
                                    <div class="item-subtitle c-gray f14">单元：<span class="c-white">{{$data['build']['name']}}#{{$data['room']['roomNum']}}</span>
                                        <i class="icon iconfont fr">&#xe602;</i>
                                    </div>
                                    <div class="item-text c-gray ha f14">电话：<span class="c-white">{{$data['mobile']}}</span></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                @if(count($list)>0)
                    <ul class="x-shakeswitch y-paylst f12">
                        <li>
                            <div class="y-doorn y-payf y-sytpay"> <p>是否开启摇一摇开门</p> </div>
                            <label class="label-switch x-sliderbtn fr mr10 mt5">
                                <input id="shakeswitch" type="checkbox" class="shake_check" @if($data['shakeswitch']) checked="checked" @endif >
                                <div class="checkbox doorcheck"></div>
                            </label>
                        </li>
                    </ul>
                    <ul class="x-doorsList y-paylst f12">
                        @foreach($list as $door)
                            <li>
                                <div class="y-doorn y-payf y-sytpay"> <p>{{$door['doorname']}}</p> </div>
                                <label class="label-switch x-sliderbtn fr mr10 mt5">
                                    <input type="checkbox" class="door_check" data-id = "{{$door['doorid']}}" >
                                    <div class="checkbox doorcheck"></div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif
        @endif
    </div>
@stop

@section($js)
    <style type="text/css">
        .x-doorsList{
            width: 100%;
            overflow: hidden;
            margin: 0.5rem 0 0 0;
        }
        .y-doorn{
            line-height: 2rem;
            margin-left: .5rem;
            vertical-align: -.5rem;
            overflow: hidden;
            max-width: 66%;
            white-space: nowrap;
            text-overflow: ellipsis;
            font-size: .8rem;
            line-height: 1.95rem;
            -webkit-align-items: center;
            align-items: center;
            display: inline-flex;
        }
        .x-doorsList .x-sliderbtn.label-switch input[type=checkbox]:checked+.checkbox{
            background:#4cd964 !important;
        }

    </style>
    <script type="text/javascript">
        window.opendoorpage = true;//允许当前页面摇一摇开门
        $(function() {
            var districtId = "{{$data['districtId']}}";

            //切换
            $(document).on("touchend",".changeTo",function(){
                $.router.load("{{ u('District/index')}}", true);
            })
            //摇一摇开关
            $("#shakeswitch").change(function(){
                $.showIndicator();
                var property_user_url = "{{ u('Property/shakeswitch') }}";
                if($(this).is(":checked")){
                    $.post(property_user_url,{districtId:districtId,status:'on'},function(result){
                        if(result.code==0){
                            if(window.App){
                                var result = getDoorKeys();
                                window.App.doorkeys(result.responseText);//更新钥匙
                            }
                        }
                        $.hideIndicator();
                    });

                }else{
                    $.post(property_user_url,{districtId:districtId,status:'off'},function(result){
                        if(result.code==0){
                            if(window.App){
                                var result = getDoorKeys();
                                window.App.doorkeys(result.responseText);//更新钥匙
                            }
                        }
                        $.hideIndicator();
                    });
                }
            });

            $('.door_check').change(function(){
                if($(this).is(":checked")){
                    $('.door_check').removeClass('on');
                    $(this).addClass('on');
                    var doorId = $(this).data('id');
                    $('.door_check').each(function(){
                        if(!$(this).hasClass('on')){
                            $(this).removeAttr('checked');
                            $(this).prop('checked',false);
                        }
                    });
                    if(window.App){
                        $.showIndicator();
                        /* var result = getDoorKey(doorId);
                         window.App.opendoor(result.responseText);*/ //开门
                        /*
                         $.ajax(doorKeys_url,{doorId:doorid}, function(res){
                         window.App.opendoor("'" + res + "'");//开门
                         $.hideIndicator();
                         },'text');
                         */
                        var ddata = {doorId:doorId};
                        $.ajax({url: doorKeys_url,data:ddata, async: true, dataType: "text",success:function(res){
                            $.hideIndicator();
                            window.App.opendoor(res);//开门
                        }});
                    }
                }else{
                    //$('.door_check').prop('checked',false);
                    return false;
                }
            });
            if(window.App){
                var result = getDoorKeys();
                window.App.doorkeys(result.responseText);
            }
        })

        function js_openlog(code,districtId,doorId,buildId,roomId){
            $.post("{{ u('Unlock/openDoorLog') }}", {'errorCode':code,'districtId': districtId,'doorId': doorId,'buildId': buildId,'roomId':roomId}, function (res) {}, "json");
        }
    </script>
@stop