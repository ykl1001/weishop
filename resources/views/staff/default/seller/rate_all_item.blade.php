@foreach($evaluation['eva'] as $key => $value)
        <div class="card y-shopcart mt10 mb10">
            <div class="card-header">
                <div class="y-start-c">
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <i class="icon iconfont">&#xe645;</i>
                    <div class="y-start-r" style="width:{{ $value[0]['star'] * 20 }}%;">
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                        <i class="icon iconfont">&#xe645;</i>
                    </div>
                </div>
                <div class="f_aaa f12 lh24">订单号:{{ substr($value[0]['order']['sn'], 8) }}</div> <!-- 20160211和列表一样隐藏年月日 -->
            </div>
            <div class="card-content">
                <div class="list-block media-list">
                    <ul>
                        <li class="item-content" onclick="JumpURL('{{u('Order/detail',['id'=>$value[0]['orderId'],"url_css"=>$id_action.$ajaxurl_page])}}','#order_detail_view',2)">
                            <div class="item-inner f12">
                                <div class="item-title-row">
                                    <div class="item-title">{{$value[0]['userName']}}<span class="f_aaa ml5">{{$value[0]['createTime']}}</span></div>
                                    <div class="item-after">
                                        订单详情
                                        <i class="icon iconfont f12 mt2 ml5">&#xe64b;</i>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="list-block media-list">
                    <ul>
                        @foreach($value as $k => $v)
                            <li class="each c-bgfff evaluation{{$v['id']}}_show">
                                <a href="#" class="item-link item-content y-flex-start">
                                    <div class="item-media"><img src="{{ $v['goods']['image'] }}" width="60"></div>
                                    <div class="item-inner pr10">
                                        <div class="item-title-row">
                                            <div class="item-title f16">{{$v['goods']['name']}}</div>
                                            <div class="item-after">
                                                 <div class="y-start-c">
                                                    <i class="icon iconfont f14">&#xe645;</i>
                                                    <i class="icon iconfont f14">&#xe645;</i>
                                                    <i class="icon iconfont f14">&#xe645;</i>
                                                    <i class="icon iconfont f14">&#xe645;</i>
                                                    <i class="icon iconfont f14">&#xe645;</i>
                                                    <div class="y-start-r" style="width:{{ $v['goodsStar'] * 20 }}%;">
                                                        <i class="icon iconfont f14">&#xe645;</i>
                                                        <i class="icon iconfont f14">&#xe645;</i>
                                                        <i class="icon iconfont f14">&#xe645;</i>
                                                        <i class="icon iconfont f14">&#xe645;</i>
                                                        <i class="icon iconfont f14">&#xe645;</i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-text f12 y-noellipsis mt5">{{$v['content']}}</div>
                                        <div class="y-evaluateico mt10 clearfix">
                                            @foreach($v['images'] as $i)
                                                <div><img src="{{$i}}"></div>
                                            @endforeach
                                        </div>
                                        @if($v['reply'])
                                        <div class="y-sjreply f12 mt5">商家回复：{{$v['reply']}}</div>
                                        @endif
                                        <!-- 用于回复后显示，无需刷新页面 -->
                                        <div class="y-sjreply f12 mt5 none replyShow_{{$v['id']}}"></div>
                                        @if(!$v['replyTime'])
                                        <div class="item-title-row mt10 replyNone_{{$v['id']}}">
                                            <div class="item-title"></div>
                                            @if($v['isAno'] == 2)
                                                <div class="f_aaa f12">系统自评</div>
                                            @else
                                                <div class="item-after f_red prompt-title-ok" data-id="{{$v['id']}}">回复<i class="icon iconfont f12 mt015 ml5">&#xe64b;</i></div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    
@endforeach