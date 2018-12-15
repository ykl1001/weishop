@extends('staff.default._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/activity') }}','#seller_activity_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{ $data['name'] ? $data['name'] : $title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom @stop
@section('show_nav')@stop
@section('content')
    <div class="list-block mt10 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">开始时间</div>
                    <div class="item-after f_333 f13">{{ Time::toDate($data['startTime'], 'Y-m-d') }}</div>
                </div>
            </li>
            <li class="item-content pl0">
                <div class="item-inner">
                    <div class="item-title f_5e f13">结束时间</div>
                    <div class="item-after f_333 f13">{{ Time::toDate($data['endTime'], 'Y-m-d') }}</div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block y-ulnobor mb10">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">每人每天参与次数</div>
                    <div class="item-after f_333 maxw40 f13">{{ !empty($data['joinNumber']) ? $data['joinNumber'] : '不限制'}}</div>
                </div>
            </li>
        </ul>
    </div>
    @if( in_array($data['type'], [4,5]) )
    <div class="list-block y-ulnobor y-sptj">
        <ul>
            @if($data['type'] == 4)
            <li class="item-content pl0"><!-- 首单立减 -->
                <div class="item-inner">
                    <div class="item-title f_5e f13">首单立减</div>
                    <div class="item-after f_333 maxw40 f13">￥{{ $data['cutMoney'] }}</div>
                </div>
            </li>
            @elseif($data['type'] == 5)
            <li class="item-content"><!-- 满 -->
                <div class="item-inner">
                    <div class="item-title f_5e f13">消费满</div>
                    <div class="item-after f_333 maxw40 f13">￥{{ $data['fullMoney'] }}</div>
                </div>
            </li>
            <li class="item-content pl0"><!-- 减 -->
                <div class="item-inner">
                    <div class="item-title f_5e f13">减</div>
                    <div class="item-after f_333 maxw40 f13">￥{{ $data['cutMoney'] }}</div>
                </div>
            </li>
            @endif
        </ul>
    </div>
    @elseif( in_array($data['type'], [6]) )
    <!-- 特价 -->
    <div class="list-block media-list y-ulbnobor y-sylist">
        <ul>
            @foreach($data['activityGoods'] as $key => $value)
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="{{$goodsList[$key]['image']}}" width="52"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f_333 f14">{{$goodsList[$key]['name']}}</div>
                            </div>
                            <div class="item-subtitle">
                                <span class="f_red">￥{{$value['salePrice']}}</span>
                                <del><small><span class="f_gray">￥{{$value['price']}}</span></small></del>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    @endif
    @if($data['isSystem'] != 1)
    <div class="y-ddyz"><a href="#" class="button button-fill button-danger bg_ff2d4b y-zfbtn">作废</a></div>
    @endif
@stop
@section($js)
<script type="text/javascript">
$(function(){
    //作废
    $(".y-zfbtn").click(function(){
        var status = "{{$data['timeStatus']}}";
        var id = "{{$data['id']}}";

        if(status == 1)
        {
            //进行中，结束
            var statusStr = "确认结束当前活动吗？";
        }
        else
        {
            //未开始，已结束，删除
            var statusStr = "确认删除当前活动吗？";
        }
        
        $.confirm(statusStr, function(){
            $.post("{{ u('Seller/cancellation') }}", {'id':id},function(res){
                $.alert(res.msg);

                if(res.code == 0)
                {
                    setTimeout(function(){
                        if(status == 1)
                        {
                            window.location.reload();
                        }
                        else
                        {
                            window.location.href = "{{ u('Seller/activity') }}";
                        }
                    },2000);
                    
                }
            });
        });

    });
    
})
</script>
@stop
@section('preloader')
@stop