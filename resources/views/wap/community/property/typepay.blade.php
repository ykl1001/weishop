@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" onclick="$.href('{{u('Property/livipayment')}}');" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">生活缴费</h1>
    </header>
@stop

@section('content')

    <div class="content" id=''>
        @if(!empty($company))
            <div class="list-block media-list y-syt lastbor">
                <ul>
                    <li class="item-content">
                        <div class="item-inner f14">
                            <div class="item-title-row">
                                <div class="item-title f14 c-black">缴费城市</div>
                                <div class="item-after f14 c-gray2 citygo">{{$city['name']}}<i class="icon iconfont f14">&#xe602;</i></div>
                            </div>
                        </div>
                    </li>
                    <li class="item-content">
                        <div class="item-inner f14">
                            <div class="item-title-row">
                                <div class="item-title f14 c-black">缴费单位</div>
                                <div class="item-after f14 c-gray2 y-jfdw">{{ $company[0]['payUnitName'] }}</div>
                            </div>
                        </div>
                    </li>
                    <div class="list-block media-list y-syt lastbor y-jdfxlk none">
                        <ul>
                            @foreach($company as $k=>$v)
                                <li class="item-content unitname @if($k==0) on @endif" data-cityId="{{$v['cityId']}}" data-provinceId="{{$v['provinceId']}}" data-provinceName="{{$v['provinceName']}}" data-cityName="{{$v['cityName']}}" data-code="{{$v['payUnitId']}}" data-name="{{$v['payUnitName']}}" data-payProjectId="{{$v['payProjectId']}}">
                                    <div class="item-inner f14">
                                        <div class="item-title f14 c-black">{{$v['payUnitName']}}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <li class="item-content mt10">
                        <div class="item-inner f14">
                            <div class="item-title-row">
                                <div class="item-title f14 c-black">户号</div>
                                <div class="item-after f14 c-gray2"><input type="text" placeholder="请输入您的户号" class="tr" id="account"></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <p class="y-bgnone"><a href="javascript:;livequery()" class="button button-fill button-danger f16">查询</a></p>
        @else
            <div class="x-null pa w100 tc">
                <img src="{{ asset('wap/community/newclient/images/null.png') }}" width="108">
                <p class="f12 c-gray mt10">该城市暂未开通该服务</p>
            </div>
        @endif

    </div>
@stop

@section($js)
    <script>
        function livequery(){
            var account = $("#account").val();
            if(account == ""){
                $.toast("请输入户号");
                return false;
            }
            var provinceName = '';
            var cityName = '';
            var code = '';
            var unitname = '';
            var payProjectId = '';
            var cityId = '';
            var provinceId = '';
            var type = "{{$args['type']}}";
            $(".unitname").each(function(){
                if($(this).hasClass("on")){
                    provinceName = $(this).attr('data-provinceName');
                    cityName = $(this).attr('data-cityName');
                    code = $(this).attr('data-code');
                    unitname = $(this).attr('data-name');
                    payProjectId = $(this).attr('data-payProjectId');
                    provinceId = $(this).attr('data-provinceId');
                    cityId = $(this).attr('data-cityId');
                }
            });

            var data = new Object();
            data.provinceId = provinceId;
            data.cityId = cityId;
            data.code = code;
            data.payProjectId = payProjectId;

            $.post("{!! u('Property/query') !!}", data, function(result){
                if(result.data == null || result.data.productId == null){
                    $.toast("该单位暂未开通");
                    return false;
                }else{
                    $.router.load("{!! u('Property/arrearage') !!}"+"?provinceName="+provinceName+"&cardid="+result.data.productId+"&productName="+result.data.productName+"&provinceId="+provinceId+"&cityId="+cityId+"&cityName="+cityName+"&code="+code+"&unitname="+unitname+"&account="+account+"&type="+type+"&payProjectId="+payProjectId, true);
                }
            },'json');

        }

        $(document).on('click','.y-jfdw', function () {
            if ($(".y-jdfxlk").hasClass("none")) {
                $(".y-jdfxlk").removeClass("none")
            }else{
                $(".y-jdfxlk").addClass("none")
            }
        });

        $(".unitname").click(function(){
            var unitname = $(this).attr('data-name');
            $('.y-jfdw').html(unitname);
            $(".unitname").removeClass("on");
            $(this).addClass("on");
            $(".y-jdfxlk").addClass("none");
        })

        $(document).on("touchend",".citygo",function(){
            var type2 = "{{ $args['type'] }}"
            $.router.load("{{ u('Index/cityservice')}}?type=3&type2="+type2, true);
        })
    </script>
@stop