@foreach($list as $k => $v)
    <div class="card m0 y-ordercont">
        <div class="card-header">
            <span class="f13">订单编号:{{$v['orderSn']}}</span>

            @if($v['status'] == 1)
                <span class="f13 c-yellow">
									已入账
								</span>
            @elseif($v['status'] == 0)
                @if($v['isRefund'] == 0)
                    <span class="f13 c-blue">
										待入账
									</span>
                @else
                    <span class="f13 c-red">
										平台退回
									</span>
                @endif
            @endif
        </div>

        <div class="card-content">
            <div class="card-content-inner y-tccenter" onclick="$.href('{{ u('MakeMoney/detail',['id' => $v['orderId'],['userId' => $args['userId']]]) }}')">
                <div class="f13">合伙人:{{$v['user']['name']}}<span class="ml10">{{$v['user']['mobile']}}</span></div>
                <div class="icon iconfont c-gray2">&#xe602;</div>
            </div>
        </div>

        <div class="card-footer">
            <div class="f12 c-gray">下单时间:{{Time::toDate($v['orderCreateTime'],'Y-m-d H:i:s')}}</div>
            <div class="f12 c-black">奖励￥{{$v['returnFee']}}元</div>
        </div>
    </div>
@endforeach