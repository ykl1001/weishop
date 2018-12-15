<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>经营分析</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel=stylesheet href="{{ asset('staff/css/base.css') }}">
    <link rel=stylesheet href="{{ asset('staff/css/jquery.mobile.custom.structure.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/jquery.mobile.icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/theme-a.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/service.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/css/morris.css') }}">
    <script src="{{ asset('staff/js/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('staff/js/jquery.mobile.custom.min.js') }}"></script>
</head>
<body>
<!-- /page -->
<div>
    <!-- /header 
    <div data-role="header" data-position="fixed" class="x-header">
        <h1>经营分析</h1>
        <a href="" data-iconpos="notext" class="x-back ui-nodisc-icon" data-shadow="false"></a>
    </div>-->
    <!-- /content -->
    <div role="main" class="ui-content">
        <ul class="x-jyul">
            <li @if($args['days'] == 1) class="on" @endif><a href="{{ u('staff/v1/order.statistics',['days'=>1,'token'=>$args['token'],'userId'=>$args['userId']]) }}" data-ajax="false">最近1天</a></li>
            <li @if($args['days'] == 3) class="on" @endif><a href="{{ u('staff/v1/order.statistics',['days'=>3,'token'=>$args['token'],'userId'=>$args['userId']]) }}" data-ajax="false">最近3天</a></li>
            <li @if($args['days'] == 7) class="on" @endif><a href="{{ u('staff/v1/order.statistics',['days'=>7,'token'=>$args['token'],'userId'=>$args['userId']]) }}" data-ajax="false">最近7天</a></li>
            <li @if($args['days'] == 30) class="on" @endif><a href="{{ u('staff/v1/order.statistics',['days'=>30,'token'=>$args['token'],'userId'=>$args['userId']]) }}" data-ajax="false">最近30天</a></li>
        </ul>
        <ul class="x-selul">
            <li class="on"><a href="#" data-ajax="false">订单金额</a></li>
            <li><a href="#" data-ajax="false">订单数量</a></li>
        </ul>
        <div class="x-showpic" style="background-color: #fff;">
            {{--<img src="{{ asset('staff/images/1.jpg') }}" />--}}
            <div id="morris-area-chart"></div>
        </div>
        <ul class="x-datalst none" id="order-num">
            <li>日期<span class="fr">订单数量</span></li>
            @foreach($data as $val)
            <li>{{ $val['date'] }}<span class="fr">{{$val['num']}}</span></li>
            @endforeach
        </ul>
        <ul class="x-datalst" id="order-money">
            <li>日期<span class="fr">订单金额</span></li>
            @foreach($data as $val)
                <li>{{ $val['date'] }}<span class="fr">{{$val['money']}}</span></li>
            @endforeach
        </ul>
    </div>
    <!-- content end -->
</div>
<script src="{{ asset('staff/js/raphael-min.js') }}"></script>
<script src="{{ asset('staff/js/morris.min.js') }}"></script>
<script type="text/javascript">
    $(function() {

        Morris.Area({
            element: 'morris-area-chart',
            data: [
                    @foreach($data as $val)
                { y: '{{ $val['date'] }}', a: '{{ $val['num'] }}', b: '{{ $val['money'] }}' },
                @endforeach
            ],
            xkey: 'y',
            ykeys: ['a', 'b'],
            labels: ['订单数量', '订单金额'],
            xLabels:"day"
        });

        $(".x-selul li").on("touchend", function(){
            $(".x-selul li").removeClass('on');
            $(this).addClass("on");
            var index = $(".x-selul li").index($(this))
            if(index == 0) {
                $("#order-num").addClass("none");
                $("#order-money").removeClass("none");
            }else{
                $("#order-num").removeClass("none");
                $("#order-money").addClass("none");
            }
        });
    });
</script>
</body>
</html>