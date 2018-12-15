@if(!empty($list))
    @foreach($list as $v)
        <?php
        $udbshow = false;
        if( $v['orders']['status'] ==  ORDER_STATUS_AFFIRM_SELLER ) {
            $v['args'] = u('Order/logistics',['id'=>$v['orderId']]);
            $udbshow = true;
        }
        ?>
        <div class="y-dptitle f12"><span>{{Time::toDate($v['sendTime'],'m-d')}}</span></div>
        <div class="card y-ordernews">
            <div class="card-header" onclick="$.href('{{$v['args']}}')"><span class="f15 c-green1">{{$v['title']}}</span><i class="icon iconfont f14 c-gray" >&#xe602;</i></div>
            <div class="card-content">
                <div class="list-block media-list">
                    <ul>
                        <li>
                            <a onclick="@if($v['orders'])$.href('{{u('Order/detail',['id'=>$v['orders']['id'],'udbType'=>1])}}')@endif" href="#" class="item-link item-content">
                                <div class="item-media">@if($v['orders'])<img src="{{ formatImage($v['orders']['goods']['goodsImages'],100,100,2) }}" width="60">@endif</div>
                                <div class="item-inner f12 pr10">
                                    @if($udbshow)
                                        <div class="item-text f13">{{$v['orders']['goods']['goodsName']}}</div>
                                    @else
                                        <div class="item-text f13">{{$v['content']}}</div>
                                    @endif
                                    @if(!in_array($v['orders']['isAll'],[3,4,5]) && $v['refundType'] == 1)
                                        @if($v['refundExpressNumber'])
                                            <div class="item-subtitle c-gray">运单编号:{{$v['userDisposeNumber']}}</div>
                                        @endif
                                    @else
                                        @if($v['expressNumber'])
                                            <div class="item-subtitle c-gray">运单编号:{{$v['expressNumber']}}</div>
                                        @endif
                                    @endif
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
@endif