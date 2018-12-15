@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="@if(!empty($nav_back_url)){{$nav_back_url}} @else{{u("Property/index")}}@endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">小区身份认证</h1>
    </header>
    @if($data['status'] != 1)
        <nav class="bar bar-tab y-ddxqbtnh">
            <div class="y-ddxqbtn2">
                <input type="hidden" name="villagesid" value="{{$data['id']}}">
                <a href="#" class="ui-btn fr" id="submit">提交</a>
            </div>
        </nav>
    @endif
@stop

@section('content')
    <!-- new -->
    <div class="content mb20" id=''>
        <div class="content-block-title">选择您的住所</div>

        <div class="list-block f14 y-bjshaddr bfh0 x-identity">
            <ul>
                <li class="item-content">
                    <div class="item-media">小区名称：</div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input type="text" placeholder="请输入小区名称" value="{{$data['name']}}">
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media"><span class="tr w100">楼宇：</span></div>
                    <div class="item-inner">
                        <div class="item-title">
                            <div class="toselect">
                                <span class="mr10 lou"></span>
                                <i class="icon iconfont down">&#xe623;</i>
                                <i class="icon iconfont up none">&#xe624;</i>
                                <div class="list-block x-reoption w100 pf f14 none">
                                    <ul class="pl0 y-option" id="buildingid">
                                        @foreach($list as $v)
                                            <li class="item-content" data-id="{{$v['id']}}">
                                                <div class="item-inner">
                                                    <div class="item-title">{{$v['name']}}</div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                         </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media tr"><span class="tr w100">房间：</span></div>
                    <div class="item-inner">
                        <div class="item-title">
                            <div class="toselect x-unitsel">
                                <span class="mr10 room"></span>
                                <i class="icon iconfont down">&#xe623;</i>
                                <i class="icon iconfont up none">&#xe624;</i>
                                <div class="list-block x-reoption w100 pf f14 none">
                                    <ul class="pl0" id="roomid">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media">
                        联系电话：
                    </div>
                    <div class="item-inner">
                        <div class="item-title">
                            <input type="text" placeholder="请输入联系电话" id="usertel" value="{{$usertel}}" maxlength="11">
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="content-block-title">完善您的身份信息</div>
        <ul class="row y-identity tc">
            <li class="col-33 @if($data['type'] == 0) active @endif" data-val="0"><a href="">我是业主</a></li>
            <li class="col-33 @if($data['type'] == 1) active @endif" data-val="1"><a href="">我是租客</a></li>
            <li class="col-33 @if($data['type'] == 2) active @endif" data-val="2"><a href="">我是业主家属</a></li>
        </ul>
        <div class="list-block mt10">
            <ul>
                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon iconfont f24 c-green2">&#xe66c;</i></div>
                        <div class="item-inner">
                            <div class="item-title label f14 y-w25">您的姓名</div>
                            <div class="item-input">
                                <input type="text" class="f14" placeholder="请填写您的真实姓名" name="username" value="{{$data['owner']}}">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="content-block-title">身份认证通过，即可开通物业版块</div>
    </div>
@stop

@section($js)
<script type="text/javascript">
var buildId = 0;
var roomId = 0;
var newroom = [];
var pageY = 0;
var pageYi = 0;
var absY = 0;
$(function() {
    //选择身份信息
    $(".y-identity li").click(function(){
        $(this).addClass("active").siblings().removeClass("active");

    })

    //选择楼栋
    $(document).off("touchstart",".toselect");
    $(document).on("touchstart",".toselect",function(e){
        pageY = e.touches[0].pageY;
    })
    $(document).off("touchend",".y-option li");
    $(document).on("touchend",".y-option li",function(e){
        pageYi = e.changedTouches[0].pageY;
        absY = Math.abs(pageYi - pageY);


        if(absY <= 20) {
            var buildingid = $(this).data('id');
            var u_id = new Array();
            $.post("{{u('District/searchrooms')}}", {'buildingid': buildingid}, function (result) {
                var html = '';
                var data = result.data;
                $.each(data, function (index, e) {
                    if (u_id.indexOf(data[index].id) == -1 && e.roomNum != null) {
                        html += '<li onclick="$.showroom(' + e.id + ')" data-id="' + e.id + '"> <div class="item-inner"><div class="item-title">' + e.roomNum + '</div></div></li>';
                        newroom[e.id] = e.roomNum;
                    }
                });
                $('#roomid').html(html);
            }, 'json');

            //记录当前新的楼宇编号
            if (buildId != buildingid) {
                buildId = buildingid;  //更新楼宇编号
                roomId = 0; //清除房间编号
                $(".x-identity .toselect span").text("请选择");
                $(".x-identity .toselect span").addClass("c-gray");
            }
        }
        return false;
    });

    //提交
    $(document).off("touchend","#submit");
    $(document).on("touchend","#submit",function(){
        // buildingid = $("#building li").data('id');
        // roomId = $("#room").data('id');
        username = $.trim($("input[name=username]").val());
        usertel = $.trim($("#usertel").val());
        villagesid = $("input[name=villagesid]").val();

        var type = 0
        $(".y-identity li").each(function(i){
            if($(this).hasClass('active')){
                type = i;
            }
        })

        var data = {
            buildingid : buildId,
            roomid : roomId,
            username : username,
            usertel : usertel,
            type : type,
            villagesid : villagesid
        };
        
        if(data['buildingid'] < 1){
            $.toast("请选楼宇");return false;
        }
        if(data['roomid'] < 1){
            $.toast("请选择房号");return false;
        }
        if(data['username'].length < 1){
            $.toast("请填写业主姓名");return false;
        }
        var reg = /^1[\d+]{10}$/;
        if(!reg.test(data['usertel'])){
            $.toast('请输入正确的手机号码');
            return false;
        }

        $.post("{{u('District/villagesauth')}}", data, function(result){
            if(result.code == 0){
                $.router.load("{!! u('Property/index') !!}?districtId=" + villagesid, true);
            } else {
                $.toast(result.msg);
            }
        },'json');
    })
        
    // 小区身份认证option
    $(".x-reoption").css("min-height",$(window).height()-140);
    if($(".x-identity .toselect span").text("") || $(".x-identity .toselect span").text("请选择")){
        $(".x-identity .toselect span").text("请选择");
        $(".x-identity .toselect span").addClass("c-gray");
    }else{
        $(".x-identity .toselect span").removeClass("c-gray");
    }
    //下拉列表框
    $(document).off("touchstart",".toselect");
    $(document).on("touchstart",".toselect",function(e){
        pageY = e.touches[0].pageY;
    })
    $(document).off("touchend",".toselect");
    $(document).on("touchend",".toselect",function(e){
        pageYi = e.changedTouches[0].pageY;
        absY = Math.abs(pageYi - pageY);
        if(absY <= 20) {
            if ($(this).find(".x-reoption").hasClass("none")) {
                $(".x-reoption").addClass("none");
                $(this).find(".x-reoption").removeClass("none");
                $(this).find(".up").removeClass("none");
                $(this).find(".down").addClass("none");
            } else {
                $(".x-reoption").addClass("none");
                $(this).find(".x-reoption").addClass("none");
                $(this).find(".up").addClass("none");
                $(this).find(".down").removeClass("none");
            }
        }
        return false;
    });
    $(document).off("touchstart",".x-reoption li");
    $(document).on("touchstart",".x-reoption li",function(e){
        pageY = e.touches[0].pageY;
    })
    $(document).off("touchend",".x-reoption li");
    $(document).on("touchend",".x-reoption li",function(e){
        pageYi = e.changedTouches[0].pageY;
        absY = Math.abs(pageYi - pageY);
        if (absY <= 20) {
            var text = $(this).find(".item-title").text();
            $(this).parents(".x-reoption").parents(".toselect").find("span").text(text);
            $(this).parents(".x-reoption").parents(".toselect").find("span").removeClass("c-gray");
            $(this).parents(".x-reoption").addClass("none");
            $(this).parents(".toselect").find(".up").addClass("none");
            $(this).parents(".toselect").find(".down").removeClass("none");
        }
        return false;
    });

    $(document).bind("touchend", "#roomid li",function(e){
        var id = $(this).attr('data-id');
        if(roomId != id){
            roomId = id;  //更新房间编号
        }

        $("#roomid").parents(".x-reoption").parents(".toselect").find("span").text(newroom[id]);
        $("#roomid").parents(".x-reoption").parents(".toselect").find("span").removeClass("c-gray");
        // $("#roomid").parent(".x-reoption").addClass("a");
        $("#roomid").parents(".toselect").find(".up").addClass("none");
        $("#roomid").parents(".toselect").find(".down").removeClass("none");
        return false;
    });

    $(document).bind("click",function(e){
        var target = $(e.target);
        if(target.closest(".x-reoption li").length == 0){
            $(".x-reoption").addClass("none");
            $(".toselect .up").addClass("none");
            $(".toselect .down").removeClass("none");
            return false;
        }
    })

})
</script>
@stop