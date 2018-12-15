@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav c-bg">
        <a class="button button-link button-nav pull-left c-white" href="#" data-transition='slide-out'>
            消息
        </a>
        <a class="button button-link button-nav pull-right  c-white" href="{{u('UserCenter/config')}}" data-popup=".popup-about" external>
            <span class="icon iconfont va-2 mr5">&#xe64a;</span>设置
        </a>
    </header>
@stop

@section('content')
    @include('wap.community._layouts.bottom')
    <div class="content" id=''>
        <div class="list-block media-list nobor y-information">
            <ul>
                <li>
                    <a href="{{u('Forum/index')}}" class="item-link item-content mb10">
                        <div class="item-media"><img src="{{ asset('wap/community/newclient/images/xx1.png') }}" width="50"></div>
                        <div class="item-inner f12 pr10">
                            <div class="item-title-row mt10">
                                <div class="item-title f16">生活圈</div>
                                <div class="item-after">
                                    @if($list['messageNum'] > 0)  <span class="y-noreadtips"> {{$list['messageNum']}}</span> @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{u('UserCenter/orderchange')}}" class="item-link item-content">
                        <div class="item-media pr"><img src="{{ asset('wap/community/newclient/images/xx2.png') }}" width="50">
                            @if($list['orderCount'] > 0)
                                <span class="y-prompt c-bg c-white">{{$list['orderCount']}}</span>
                            @endif
                        </div>
                        <div class="item-inner f12 pr10">
                            <div class="item-title-row @if(empty($list['orderInfo'])) mt10 @endif">
                                <div class="item-title f16">订单状态变更消息</div>
                                <div class="item-after c-gray">{{Time::toDate($list['orderInfo']['sendTime'],'Y-m-d')}}</div>
                            </div>
                            <div class="item-title f14 c-gray mt5">{{$list['orderInfo']['title']}}</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{u('UserCenter/msgshow',['sellerId'=>0])}}" class="item-link item-content">
                        <div class="item-media pr"><img src="{{ asset('wap/community/newclient/images/xx3.png') }}" width="50">
                            @if($list['systemCount'] > 0)
                                <span class="y-prompt c-bg c-white">{{$list['systemCount']}}</span>
                            @endif
                        </div>
                        <div class="item-inner f12 pr10">
                            <div class="item-title-row  @if(empty($list['systemInfo'])) mt10 @endif">
                                <div class="item-title f16">系统消息</div>
                                <div class="item-after c-gray">{{Time::toDate($list['systemInfo']['sendTime'],'Y-m-d')}}</div>
                            </div>
                            <div class="item-title f14 c-gray mt5">{{$list['systemInfo']['title']}}</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{u('UserCenter/wealth',['sellerId'=>0])}}" class="item-link item-content">
                        <div class="item-media pr">
							<img src="{{ asset('wap/community/newclient/images/xx5.png') }}" width="50">
                            @if($list['financeCount'] > 0)
                                <span class="y-prompt c-bg c-white">{{$list['financeCount']}}</span>
                            @endif
						</div>
                        <div class="item-inner f12 pr10">
                            <div class="item-title-row">
                                <div class="item-title f16">财富消息</div>
                                @if($list['financeInfo']['updateTime'])
                                    <div class="item-after c-gray">{{Time::toDate($list['financeInfo']['updateTime'],'Y-m-d')}}</div>
                                @else
                                    <div class="item-after c-gray">{{Time::toDate($list['financeInfo']['createTime'],'Y-m-d')}}</div>
                                @endif
                            </div>
                            <div class="item-title f14 c-gray mt5">
                                @if($list['financeInfo'])
                                    @if($list['financeInfo']['level'] == 1)
                                        您的I级合伙人：“{{$list['financeInfo']['user']['name']}}”一笔订单
                                    @elseif($list['financeInfo']['level'] == 2)
                                        您的II级合伙人：“{{$list['financeInfo']['user']['name']}}”一笔订单
                                    @elseif($list['financeInfo']['level'] == 3)
                                        您的III级合伙人：“{{$list['financeInfo']['user']['name']}}”一笔订单
                                    @elseif($list['financeInfo']['level'] == 4)
                                        您的IIII级合伙人：“{{$list['financeInfo']['user']['name']}}”一笔订单
                                    @else
                                        “{{$list['financeInfo']['user']['name']}}”一笔订单
                                    @endif

                                    @if($list['financeInfo']['status'] == 1)
                                        交易完成，获得
                                    @else
                                        @if($list['financeInfo']['isRefund'] == 0)
                                            正在进行，您将得
                                        @else
                                            交易已取消，您已经失去了
                                        @endif
                                    @endif
                                    {{$list['financeInfo']['returnFee']}}元佣金奖励！
                                @else
                                    赶紧推荐你的小伙伴，获取佣金吧！
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="{{u('UserCenter/team',['sellerId'=>0])}}" class="item-link item-content">
                        <div class="item-media pr"><img src="{{ asset('wap/community/newclient/images/xx6.png') }}" width="50">
                            @if($list['teamCount'] > 0)
                                <span class="y-prompt c-bg c-white">{{$list['teamCount']}}</span>
                            @endif
                        </div>
                        <div class="item-inner f12 pr10">
                            <div class="item-title-row">
                                <div class="item-title f16">团队消息</div>
                                <div class="item-after c-gray">{{Time::toDate($list['teamInfo']['sendTime'],'Y-m-d')}}</div>
                            </div>
                            <div class="item-title f14 c-gray mt5">{{$list['teamInfo']['content'] or "赶紧邀请小伙伴加入团队!"}}</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

    </div>
@stop
@section($js)
    <script type="text/javascript">
        var BACK_URL = "{{u('Index/index')}}";
        $(function(){
            $(".showmore").click(function(){
                if($(".x-ltmore").hasClass("none")){
                    $(".x-ltmore").removeClass("none");
                }else{
                    $(".x-ltmore").addClass("none");
                }
            });
        });
    </script>
@stop