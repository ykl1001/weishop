@if($lists)
    @foreach($lists as $item)
        <div class="card y-jljl">
            <div class="card-header c-bg c-white f14"><span>{{Time::toDate($item['createTime'], 'Y-m-d')}}</span><span class="fr">购买人:{{$item['name']}}</span></div>
            <div class="card-content">
                <ul class="row">
                    <li class="col-33 tc">
                        <p class="c-gray f14">订单金额</p>
                        <p class="c-black f15">￥{{$item['totalFee']}}</p>
                    </li>
                    <li class="col-33 tc">
                        <p class="c-gray f14">比率</p>
                        <p class="c-black f15">{{$item['percent']}}%</p>
                    </li>
                    <li class="col-33 tc">
                        <p class="c-gray f14">佣金</p>
                        <p class="c-black f15">￥{{$item['returnFee']}}</p>
                    </li>
                </ul>
            </div>
        </div> 
    @endforeach
@endif