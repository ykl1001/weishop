@if(!empty($data))
    @foreach($data as $k=>$v)
        <div class="content-block-title f12 tc">{{Time::toDate($v['createTime'],'Y年m月d日 H:i:s')}}</div>
        <div class="card">
            <div class="card-content"  onclick="$.href('{{u('MakeMoney/detail',['id' => $v['orderId']])}}')">
                <div class="card-content-inner f12 c-gray3">
                    @if($v['level'] == 1)
                        I 级合伙人：“{{$v['user']['name']}}”一笔订单
                    @elseif($v['level'] == 2)
                        II 级合伙人：“{{$v['user']['name']}}”一笔订单
                    @elseif($v['level'] == 3)
                        III 级合伙人：“{{$v['user']['name']}}”一笔订单
                    @elseif($v['level'] == 4)
                        IIII 级合伙人：“{{$v['user']['name']}}”一笔订单
                    @else
                        “{{$v['user']['name']}}”一笔订单
                    @endif
                    @if($v['status'] == 1)
                        交易完成，获得
                    @else
                        @if($v['isRefund'] == 0)
                            正在进行，您将得
                        @else
                            交易已取消，您已经失去了
                        @endif
                    @endif
                    {{$v['returnFee']}}元佣金奖励！
                </div>
                <i class="icon iconfont f14 fr" style="margin-top:-35px;margin-right:8px">&#xe602;</i>

                <div class="clear"></div>
            </div>
            <div class="card-footer">
                <span class="c-gray3 f12">
                    @if($v['status'] == 1)
                        +
                    @elseif($v['status'] == 0)
                        @if($v['isRefund'] == 1)
                            -
                        @endif
                    @endif
                    {{$v['returnFee']}}元
                </span>
                <a href="#" class="c-gray3 f12">
                    @if($v['status'] == 1)
                        已入账
                    @else
                        @if($v['isRefund'] == 0)
                            待入账
                        @else
                            平台回收
                        @endif
                    @endif
                </a>
            </div>
        </div>
    @endforeach
@endif