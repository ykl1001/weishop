@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">我要报修</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <!-- 报修内容 -->
        <div class="card x-posting x-repair">
            <div class="card-header ml0 pl10 f14">
                <div class="fl wa">小区名称：</div>
                <div>{{$data['name']}}</div>
            </div>
            <div class="card-header f14">
                <div class="fl wa">故障类型：</div>
                <div class="toselect">
                    <span class="mr10">选择类型</span>
                    <i class="icon iconfont down c-gray2">&#xe623;</i>
                    <i class="icon iconfont up none c-gray2">&#xe624;</i>
                    <div class="list-block x-reoption w100 pf f14 none">
                        <ul>
                            @foreach($list as $item)
                                <li class="item-content" data-id="{{$item['id']}}">
                                    <div class="item-inner">
                                        <div class="item-title">{{$item['name']}}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-header ml0 pl10 f14">
                <div class="fl wa">维修时间：</div>
                <div class="y-dytime">
                    <span class="mr10" id="beginTime">立即维修</span>
                    <i class="icon iconfont down c-gray2">&#xe623;</i>
                </div>
                <input type="hidden" id="appTime" value="{{$apitime[0]['time']}} {{$apitime[0]['list'][0]}}">
            </div>

            <div class="card-content f14">
                <div class="fl wa">故障描述：</div>
                <div class="postr"><textarea placeholder="请填写故障内容" class="w100" id="content"></textarea></div>
            </div>
        </div>
        <!-- 添加图片 -->
        <div class="card x-postdelst m0">
            <div class="card-content x-pjpic">
                <div class="card-content-inner oh">
                    <ul class="x-postpic clearfix">
                        @for($i = 1; $i <= 4; $i++)
                            <form>
                                <label id="imglabel-{{$i}}" class="img-up-lb" for="image-form-{{$i}}">
                                <li id="image-form-{{$i}}-li">
                                    <img data-num="{{$i}}" class="image_upload" src="{{asset('wap/community/client/images/addpic.png')}}" id="img{{$i}}" class="upimage">
                                    <i class="delete none showdelete{{$i}}" data-index="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                </li>
                                </label>
                                <div style="display:none"><input type="text" name="images" id="upimage_{{$i}}"></div> 
                            </form>
                        @endfor

                    </ul>
                </div>
            </div>
        </div>
        <div class="y-ddxqbtn2">
            <a href="javascript:repairSave();" class="ui-btn fr" id="submit">我要报修</a>
        </div>

        <?php
            $tab1 = $tab2 = true;
        ?>
        <!-- 全部筛选 -->
        <div class="x-sjfltab pf y-time none">
            <div class="mask pa"></div>
            <div class="y-bottom">
                <div class="buttons-tab fl pr">
                    <div class="y-noscroll">
                        @foreach($apitime as $key => $value)
                            @if(count($value['list']) > 0)
                                <a href="#tab1_{{$key}}" class="tab-link button @if($tab1) active @endif timestampDay" data-day="{{$value['time']}}">{{$value['dayName']}}</a>
                                <?php $tab1 = false ?>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="tabs c-bgfff fl app-time-day">
                    @foreach($apitime as $key => $value)
                        @if(count($value['list']) > 0)
                            <div id="tab1_{{$key}}" class="tab p-tab @if($tab2) active @endif">
                                <div class="list-block x-sortlst f14">
                                    <ul>
                                        @foreach($value['list'] as $k => $v)
                                            @if($value['time'] == Time::toDate(UTC_TIME, 'Y-m-d'))
                                                @if( $putoff == 1 )
                                                    {{--<!-- 当天，有立即送出 -->--}}
                                                    {{--<li class="item-content timestampTime isNow active @if(in_array(key($sendwayArr),[2,3])) none @endif" data-time="0">--}}
                                                        {{--<div class="item-inner">--}}
                                                            {{--<div class="item-title">{{$orderTypeStr}}<p><small>（大约{{$v}}到）</small></p></div>--}}
                                                            {{--<i class="icon iconfont c-yellow2 f20">&#xe610;</i>--}}
                                                        {{--</div>--}}
                                                    {{--</li>--}}
                                                @else
                                                    @if($value['timestamp'][$k] < $sellerAllowTime[0]['timestamp'][0])
                                                        <!-- 商家配送的时候这个时间段小于立即送出，不显示；但是到店需要显示这段时间 -->
                                                        <li class="item-content isNextNow timestampTime @if(!in_array(key($sendwayArr),[2,3])) none @endif" data-time="{{$v}}">
                                                            <div class="item-inner">
                                                                <div class="item-title">{{$v}}</div>
                                                                <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                            </div>
                                                        </li>
                                                        @else
                                                        <!-- 商家配送和到店均显示 -->
                                                        <li class="item-content timestampTime" data-time="{{$v}}">
                                                            <div class="item-inner">
                                                                <div class="item-title">{{$v}}</div>
                                                                <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endif
                                                <?php $putoff++; ?>
                                                @else
                                                <!-- 非当天 -->
                                                <li class="item-content timestampTime" data-time="{{$v}}">
                                                    <div class="item-inner">
                                                        <div class="item-title">{{$v}}</div>
                                                        <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                    </div>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <?php $tab2 = false ?>
                        @endif
                    @endforeach
                </div>
                <div class="row c-bgfff tc">
                    <div class="col-100 f16">取消</div>
                </div>
            </div>
        </div>

    </div>
@stop

@section($js)

<script type="text/javascript">
    $(document).on('click','.image_upload', function () {
        var thisObj = $(this); 
        $(this).fanweImage({
            width:320, 
            height:320, 
            callback:function(url, target) {
                thisObj.get(0).src = url;
                $("#upimage_"+thisObj.data('num')).val(url); 
                $(".showdelete"+thisObj.data('num')).removeClass("none");
            }
        });
    });  
</script>


<script type="text/javascript">
    var districtId = "{{ Input::get('districtId') }}";
    var typeId = '';

    $(function() {
        $(document).on("click", ".y-bottom .tabs .list-block li", function(){
            $(this).addClass("active").siblings().removeClass("active");
            var day = $('.timestampDay.active').data('day');
            var time = $('.p-tab.active .timestampTime.active').data('time');
            if(time==0){
                $("#beginTime").text('{{$orderTypeStr}}');
                $("#appTime").val(0);
            }else{
                $("#beginTime").text(day+' '+time);
                $("#appTime").val(day+' '+time);
            }
            $(".y-bottom .row").parents(".y-time").addClass("none");
            FANWE.JS_BACK_HANDLER = null;
        });
        $(document).on("click", ".y-time .mask", function(){
            $(this).parents(".y-time").addClass("none");
            FANWE.JS_BACK_HANDLER = null;
        });
        $(document).on("click", ".y-dytime", function(){
            FANWE.JS_BACK_HANDLER = function() {
                $(".y-time").addClass("none");
                return true;
            }
            $(".y-time").removeClass("none");
        });

        //照片删除
        $(document).on("click",".x-postpic .delete",function(){
            $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
            $(this).addClass("none");
            $(this).parents("li").find("input").val("");
            return false;
        });

        // 故障类型option
        $(".x-reoption").css("min-height",$(window).height()-140);
        //下拉列表框
        $(document).on("click",".toselect",function(){
            if($(this).find(".x-reoption").hasClass("none")){
                $(this).find(".x-reoption").removeClass("none");
                $(".toselect .up").removeClass("none");
                $(".toselect .down").addClass("none");
            }else{
                $(this).find(".x-reoption").addClass("none");
                $(".toselect .up").addClass("none");
                $(".toselect .down").removeClass("none");
            }
            return false;
        });
        $(document).on("click",".x-reoption li",function(){
            var text = $(this).find(".item-title").text();
            $(this).parents(".x-reoption").parents(".toselect").find("span").text(text);
            $(this).parents(".x-reoption").addClass("none");
            $(".toselect .up").addClass("none");
            $(".toselect .down").removeClass("none");

            typeId = $(this).attr("data-id");
            return false;
        });

        // $(document).bind("click",function(e){
        //     var target = $(e.target);
        //     if(target.closest(".x-reoption li").length == 0){
        //         $(".x-reoption").addClass("none");
        //         $(".toselect .up").addClass("none");
        //         $(".toselect .down").removeClass("none");
        //         return false;
        //     }
        // })
    });

    function repairSave() {
        var images = new Array();
        $("input[name=images]").each(function(index,val){
            if($(this).val() != "" ){
                images.push($(this).val());
            }
        })
        var content = $("#content").val();
        var apiTime = $("#appTime").val();

        var data = {
            content: content,
            images: images,
            typeId: typeId,
            districtId: districtId,
            apiTime : apiTime
        };

        if(data.apiTime == ""){
            $.alert("请选择维修时间");
            return false;
        }
        if(data.typeId == ""){
            $.alert("请选择故障类型");
            return false;
        }
        // console.log(data);
        // return false;
        $.post("{{ u('Repair/save') }}", data, function(res){
            if(res.code == 0) {
                $.toast(res.msg);
                $.router.load("{!! u('Repair/index',['districtId'=>Input::get('districtId')]) !!}", true);
            }else if(res.code == '99996'){
                $.router.load("{{ u('User/login') }}", true);
            }else{
                $.alert(res.msg);
            }
        },"json");
    }
</script>
@stop