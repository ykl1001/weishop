@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" onclick="javascript:$.back();" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">生活缴费</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>

        <div class="list-block media-list y-sylist lastbor">
            <ul>
                @foreach($list['orderList'] as $k=>$v)
                    <li class="item-content @if($k != 0) mt10 @endif">
                        <div class="item-inner f14">
                            <div class="item-title-row">
                                <div class="item-title f14 c-black">{{$v['extend']['unitname']}}</div>
                                <div class="item-after f14 c-red">-{{$v['money']}}</div>
                            </div>
                            <div class="item-title-row">
                                <div class="item-title f12 c-gray">缴费号{{$v['extend']['account']}}</div>
                                <div class="item-after f12 c-gray">{{ Time::toDate($v['createTime'],'Y-m-d') }}
                                    @if($v['isPay'] == 0 || $v['isPay'] == 1)
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="f12" style="color: #f00b0d">充值中</a>
                                    @elseif($v['isPay'] == 2)
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="f12" style="color: #f00b0d">充值成功</a>
                                    @elseif($v['isPay'] == -1)
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a class="f12" style="color: #f00b0d">充值失败</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
@stop

@section($js)

@stop