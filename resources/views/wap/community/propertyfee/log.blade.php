@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading back" onclick="javascript:$.back();" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">物业费缴费记录</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        @if(empty($list))
            <div class="card-content">
                <div class="tc mt20 mb20 pt10">
                    <img src="{{asset('wap/community/newclient/images/nothing.png')}}" width="108">
                    <p class="c-gray5 f13">暂无记录</p>
                </div>
            </div>
        @else
            @foreach($list as $k=>$item)
                <div class="card y-shopcart">
                    <div class="card-header">
                        <span class="c-black f14">{{ $k }}</span>
                    </div>
                    <div class="card-content">
                        <div class="list-block media-list y-syt lastbor">
                            <ul>
                                @foreach($item as $k2=>$item2)
                                <li class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row">
                                            <div class="item-title f13 c-black">{{$item2['roomfee']['payitem']['name']}}</div>
                                            <div class="item-after f13 mt10 c-red">￥{{$item2['fee']}}</div>
                                        </div>
                                        <div class="item-title-row mt-10">
                                            <div class="item-title f12 c-gray">账单日期：{{ Time::toDate($item2['beginTime'],'Y-m-d') }}至{{ Time::toDate($item2['endTime'],'Y-m-d') }}</div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@stop

@section($js)

@stop