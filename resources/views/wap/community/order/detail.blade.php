@extends('wap.community._layouts.base')

@section('show_top')
    <?php
    $time = time();
    ?>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{ !empty($nav_back_url) ? $nav_back_url : 'javascript:$.back()'}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right y-call-{{$time}}">
            <span class="icon iconfont c-gray">&#xe670;</span>
        </a>
        <h1 class="title f16">订单详情</h1>
    </header>
    <style>
        /*取消原因*/
        .y-cancelreason{margin: -.5rem;}
        .y-cancelreason li{}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;}
        .y-orderewm .y-qgdshoptcmain{width: 60%;padding-bottom: 40px;}
    </style>
@stop
@section('content')
    <!-- 有按钮显示导航 没有则隐藏 -->
    @if($data['isCanDelete'] || $data['isCanRate'] || ($data['isCanCancel'] && !$data['isCanPay']) || ($data['isCanCancel'] && $data['isCanPay']) || $data['isCanPay'] || $data['isCanConfirm'] || $data['isCanLogistics'])
        <nav class="bar bar-tab" style="{{$show}}">
            <div class="y-xddxqbtn">
                @if($data['isCanDelete'])
                    <!-- <a href="#" class="ui-btn fl delorder">删除</a> -->
                    <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" data-no-cache="true">去逛逛</a>
                @endif
                @if($data['isCanRate'])
                    <!-- <a href="{{ u('Order/comment',['orderId' => $data['id'],'tid'=>Input::get('tid')]) }}" class="y-ddbtnblue" data-no-cache="true">评价</a> -->
                    <a href="{{ u('Order/comment',['orderId' => $data['id'],'tid'=>Input::get('tid')]) }}" class="y-ddbtnblue external" data-no-cache="true">评价赚积分</a>

                    <a href="{{ u('Goods/index', ['id'=>$data['sellerId'],'type'=>$data['orderType']]) }}" class="fr external" data-no-cache="true">去逛逛</a>
                @endif
                @if($data['isCanCancel'])
                    <a href="#" class=" detail_cancelorder_{{$time}} y-ddbtnblue">取消订单</a>
                @endif
                @if($data['isCanPay'] && $data['activityGoodsIsChange'] == 1)
                    <a href="{{ u('Order/cashierdesk',['orderId'=>$data['id']]) }}" data-no-cache="true">去支付</a>
                @endif
                @if($data['isNewCanRefund'])
                    <a href="javascript:$.href('{{u('Logistics/ckservice',['id'=>$data['id']])}}');" class="y-viewlogistics" external>申请退款</a>
                @endif
                @if($data['isCanLogistics'])
                    <a href="javascript:$.href('{{u('Order/logistics',['id'=>$data['id']])}}');" class="y-viewlogistics" external>查看物流</a>
                @endif
                @if($data['isCanConfirm'])
                    <a href="#" class="y-bgff2d4b detail_confirmorder_{{$time}}">@if($data['isAll'] == 1)确认收货@else确认完成@endif</a>
                @endif
            </div>
        </nav>
    @endif
    <div class="content" id="{{$time}}">
        <div class="list-block media-list y-notwobor">
            <ul>
                <li class="item-content">
                    <div class="item-media"><i class="icon iconfont f30 c-gray">&#xe676;</i></div>
                    <div class="item-inner pr10">
                        <div class="item-title-row @if($data['orderType']) pt4 @endif">
                            <div class="item-title f16">{{$data['orderNewStatusStr']['title']}}</div>
                            @if($data['orderNewStatusStr']['time'] > 0)
                                <div class="item-after f14 c-gray">{{ Time::toDate($data['orderNewStatusStr']['time'],"m月d日 H:i") }}</div>
                            @endif
                        </div>
                        <div class="item-subtitle f14 c-gray" style="white-space:normal">{{$data['orderNewStatusStr']['tag']}}</div>
                    </div>
                </li>
            </ul>
        </div>
        @if($data['seller']['storeType'] == 1)
            <!-- 上门 -->
            <div class="list-block">
                <ul>
                    <li class="item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="f14 w100">
                                <span class="fl">@if($data['orderType'] == 2)服务@else收货@endif地址</span>
                                <div class="y-xddxqcont c-gray">
                                    <p><span class="mr10">{{$data['name']}}</span><span>{{$data['mobile']}}</span></p>
                                    <p>{{$data['address']}}</p>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        @endif
        <div class="list-block media-list">
            <ul>
                @if($data['refundCount'] > 0)
                    <li>
                        <a class="item-content p0 external" href="{{ u('Order/refundview',['orderId'=>$data['id']]) }}">
                            <div class="item-inner pr10 pl10">
                                <div class="item-title-row">
                                    <div class="item-title c-blue">查看退款详情</div>
                                    <div class="item-after icon iconfont">&#xe602;</div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
                @if(!empty($data['authCode']) > 0)
                    <li class="item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row f14">
                                <div class="item-title" style="line-height: 30px;">消费码：{{ $data['authCode'] }}

                                    @if($data['orderStatusStr'] == '交易关闭')
                                        (已失效)
                                    @elseif($data['authCodeUse'] == -1)
                                        (已使用)
                                    @elseif($data['authCodeUse'] == 1)
                                        (未使用)
                                    @endif



                                </div>

                                <div class="item-after" id="y-orderewmdiv">
                                    <img src="{{ u('Order/cancode',['val'=>$data['authCode']])}}" width="30" height="30">

                                    {{--<img src="http://i0.sinaimg.cn/dy/c/2013-11-02/1383328221_ruzXP6.jpg" width="30" height="30">--}}
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row f14">
                                <div class="item-title">到店时间：{{ Time::toDate($data['appTime'],"Y-m-d H:i") }}</div>
                            </div>
                        </div>
                    </li>
                @else
                    @if($data['seller']['storeType'] == 0)
                        <li class="item-content p0">
                            <div class="item-inner pr10 pl10">
                                <div class="f14 w100">
                                    <span class="fl">@if($data['orderType'] == 2)服务时间@else预计到达时间@endif：</span>
                                    <div class="y-xddxqcont c-gray">{{$data['appTime']}}</div>
                                </div>
                            </div>
                        </li>
                        <li class="item-content p0">
                            <div class="item-inner pr10 pl10">
                                <div class="f14 w100">
                                    <span class="fl">@if($data['orderType'] == 2)服务@else收货@endif地址</span>
                                    <div class="y-xddxqcont c-gray">
                                        <p><span class="mr10">{{$data['name']}}</span><span>{{$data['mobile']}}</span></p>
                                        <p>{{$data['address']}}</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif
                @endif
                @if($data['seller']['storeType'] == 0)
                    @if(!empty($data['staff']))
                        @if($data['status'] == ORDER_STATUS_CALL_SYSTEM_SEND || $data['status'] == ORDER_STATUS_SYSTEM_SEND)
                            <li class="item-content p0">
                                <div class="item-inner pr10 pl10">
                                    <div class="f14 w100">
                                        <span class="fl">@if(in_array($data['sendWay'],[2,3]))服务@else配送@endif人员：</span>
                                        <div class="y-xddxqcont c-gray">待平台指派</div>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li class="item-content p0">
                                <div class="item-inner pr10 pl10">
                                    <div class="f14 w100">
                                         <span class="fl">
											@if($data['orderType'] == 1)
												@if(in_array($data['sendWay'],[2,3]))
												服务@else
												配送@endif
											@else
												服务@endif人员：</span>
                                        <div class="y-xddxqcont c-gray">{{$data['staff']['name']}}</div>
                                    </div>
                                </div>
                            </li>
                            <li class="item-content p0">
                                <div class="item-inner pr10 pl10">
                                    <div class="f14 w100">
                                        <span class="fl">联系电话：</span>
                                        <div class="y-xddxqcont c-gray">
                                            <p>
                                                <span>{{$data['staff']['mobile']}}</span>
                                                <a class="fr icon iconfont" href="tel:{{$data['staff']['mobile']}}">&#xe671;</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @endif
                @endif
            </ul>
        </div>

        <div class="card y-card mb10">
            <a class="card-header" href="#" @if($data['sellerId'] > 0) onclick="$.href('{{ u('Seller/detail',['id'=>$data['sellerId'],'urltype'=>2]) }}')" @else onclick="$.toast('商家已关闭或不存在')" @endif>
                <div>
                    <span class="c-black f14"><i class="icon iconfont c-gray2 mr5 vat">&#xe632;</i>{{$data['sellerName'] or '商家已关闭或不存在'}}</span>
                </div>
                <i class="icon iconfont c-gray vat">&#xe602;</i>
            </a>
            <div class="card-content">
                <div class="list-block media-list f14 y-xddxqlist">
                    <ul>
                        @if($data['seller']['storeType'] == 0)
                            <li class="item-content">
                                <div class="item-inner pr10">
                                    @foreach($data['cartSellers'] as $val)
                                        <div class="item-title-row">
                                            <div class="item-title">{{$val['goodsName']}}</div>
                                            <span class="mr15 c-gray">x{{$val['num']}}</span>
                                            @if($val['salePrice'] > 0)
                                                <span class="y-delgrid mr15 c-gray">￥{{ number_format($val['price']*$val['num'], 2) }}</span> <!-- 原价 -->
                                                <div class="item-after"><span>￥{{ number_format($val['salePrice'], 2) }}</span></div> <!-- 成交价 -->
                                            @else
                                                <span class="y-delgrid mr15 c-gray"></span> <!-- 原价 -->
                                                <div class="item-after"><span>￥{{ number_format($val['price']*$val['num'], 2) }}</span></div> <!-- 成交价 -->
                                            @endif
                                        </div>
                                        @if($val['goodsNorms'])
                                            <div class="item-title f12 c-gray">{{str_replace(':','-',$val['goodsNorms'])}}</div>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                        @else
                            @foreach($data['goods'] as $goods)
                                <li onclick="$.href('{{ u('Goods/detail',['goodsId'=>$goods['goodsId']]) }}')">
                                    <div class="item-content">
                                        <div class="item-media">
                                            <a href="#">
                                                <img src="{{formatImage($goods['goodsImages'],200,200)}}" width="45.5">
                                            </a>
                                        </div>
                                        <div class="item-inner f12">
                                            <div class="item-title-row">
                                                <a href="">
                                                    <div class="item-title">{{$goods['goodsName']}}</div>
                                                </a>
                                                <div class="item-after">
                                                    @if($goods['salePrice'] <= 0)
                                                        <p>￥{{$goods['price']}}</p>
                                                    @else
                                                        <p>￥{{ $goods['salePrice'] }}</p>
                                                    @endif
                                                    @if($goods['salePrice'] > 0)
                                                        <del class="c-gray">￥{{$goods['price']}}</del>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($goods['goodsNorms'])
                                                <div class="item-title-row c-gray">
                                                    <div class="item-title">{{str_replace(':','-',$goods['goodsNorms'])}}</div>
                                                    <div class="item-after c-gray">x{{$goods['num']}}</div>
                                                </div>
                                            @else
                                                <div class="item-title-row c-gray">
                                                    <div class="item-title"></div>
                                                    <div class="item-after c-gray">x{{$goods['num']}}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                        @if($data['orderType'] == 1 && empty($data['authCode']))
                            <li class="item-content">
                                <div class="item-inner pr10">
                                    <div class="item-title-row">
                                        <div class="item-title">配送费</div>
                                        <div class="item-after">￥{{ $data['freight'] ? number_format($data['freight'], 2) : '0.00'}}</div>
                                    </div>
                                </div>
                            </li>
                        @endif
                        <li class="item-content">
                            <div class="item-inner pr10">
                                @if($data['discountFee'] > 0)
                                    <div class="item-title-row">
                                        <div class="item-title">优惠券</div>
                                        <div class="item-after">-￥{{ number_format($data['discountFee'], 2) }}</div>
                                    </div>
                                @endif
                                <div class="item-title-row">
                                    <div class="item-title">积分抵扣</div>
                                    <div class="item-after">-￥{{ $data['integralFee'] ? number_format($data['integralFee'], 2) : '0.00'}}</div>
                                </div>
                                @if($data['activityNewMoney'] > 0)
                                    <div class="item-title-row">
                                        <div class="item-title">首单优惠</div>
                                        <div class="item-after">-￥{{ number_format($data['activityNewMoney'], 2) }}</div>
                                    </div>
                                @endif
                                @if($data['activityFullMoney'] > 0)
                                    <div class="item-title-row">
                                        <div class="item-title">满减优惠</div>
                                        <div class="item-after">-￥{{ number_format($data['activityFullMoney'], 2) }}</div>
                                    </div>
                                @endif
                                @if($data['activityGoodsMoney'] > 0)
                                    <div class="item-title-row">
                                        <div class="item-title">特价优惠</div>
                                        <div class="item-after">-￥{{ number_format($data['activityGoodsMoney'], 2) }}</div>
                                    </div>
                                @endif
                            </div>
                        </li>
                        <li class="item-content">
                            <div class="item-inner pr10">
                                <div class="item-title-row">
                                    <div class="item-title"></div>
                                    <div class="item-after f12 c-gray"><span>总计：</span><span class="c-red">￥{{ number_format($data['payFee'], 2) }}</span></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="list-block">
            <ul>
                <li class="item-content p0">
                    <div class="item-inner pr10 pl10">
                        <div class="f14 w100">
                            <span class="fl" style="letter-spacing:5px">订单号：</span>
                            <div class="y-xddxqcont c-gray">{{$data['sn']}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content p0">
                    <div class="item-inner pr10 pl10">
                        <div class="f14 w100">
                            <span class="fl">备注信息：</span>
                            <div class="y-xddxqcont c-gray">{{ !empty($data['buyRemark']) ? $data['buyRemark'] : '无'}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content p0">
                    <div class="item-inner pr10 pl10">
                        <div class="f14 w100">
                            <span class="fl">支付方式：</span>
                            <div class="y-xddxqcont c-gray">{{$data['payType']}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content p0">
                    <div class="item-inner pr10 pl10">
                        <div class="f14 w100">
                            <span class="fl">下单时间：</span>
                            <div class="y-xddxqcont c-gray">{{$data['createTime']}}</div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) < $activity['sharePromotionNum'])
            <!-- 分享按钮 -->
            <div class="y-ddfxbtn"><img src="{{asset('wap/community/newclient/images/share.png')}}"></div>
            <!-- 分享到微信好友或朋友圈 -->
            <div class="f-bgtk sha-frame none">
                <div class="x-closebg"></div>
                <img src="{{ asset('wap/images/share2.png') }}" class="x-sharepic">
            </div>
            <!-- 分享优惠券弹框-->
            <div class="f-bgtk size-frame none">
                <div class="x-closebg"></div>
                <div class="x-probox pb0 c-bgfff">
                    <p class="f14 c-black p10">通过社交软件分享<br>好友领取优惠券并注册即成为你的合伙人</p>
                    <ul class="y-sharecoupontc clearfix">
                        <li class="weixinalert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg1.png")}}"><p class="c-gray f12">微信好友</p></a></li>
                        <li class="weixinalert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg2.png")}}"><p class="c-gray f12">朋友圈</p></a></li>
                        <li class="weiboalert" ><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg3.png")}}"><p class="c-gray f12">微博</p></a></li>
                        <li class="qqalert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg4.png")}}"><p class="c-gray f12">QQ</p></a></li>
                        <li  class="zonealert"><a href="#"><img src="{{ asset("wap/community/newclient/images/yhqimg5.png")}}"><p class="c-gray f12">QQ空间</p></a></li>
                        <li  class="copy_btn pr " data-clipboard-action="copy" data-clipboard-target="#copy_contents">
                            <a href="#">
                                <input type="text" readOnly id="copy_contents" value="{!! $link_url !!}" style="opacity: 0;position: absolute;top: 0;"/>
                                <img src="{{ asset('wap/community/newclient/images/yhqimg6.png')}}">
                                <p class="c-gray f12">复制链接</p>
                            </a>
                        </li>
                    </ul>
                    <div class="y-sharecouponbtn">取消</div>
                </div>
            </div>
            <script type="text/javascript" src="{{ asset('wap/community/newclient/js/jweixin-1.0.0.js') }}"></script>
            @endif
                    <!-- 弹窗 -->
            <div class="y-qgdshoptc y-orderewm none">
                <div class="y-qgdshoptcbox">
                    <div class="y-tcbg"></div>
                    <div class="y-qgdshoptcmain">
                        <div class="y-qgdshoptcewm"><img src="{{ u('Order/cancode',['val'=>$data['authCode']])}}"></div>
                        <p class="f12 c-gray">{{$data['authCode']}}
                            @if($data['orderStatusStr'] == '交易关闭')
                                (已失效)
                            @elseif($data['authCodeUse'] == -1)
                                (已使用)
                            @elseif($data['authCodeUse'] == 1)
                                (未使用)
                            @endif</p>
                    </div>
                </div>
            </div>
    </div>

    <script type="text/tpl" id="cancehtml">
        <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">不想买了</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
            <li><span id="cancelreason2">信息填写错误，重拍</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
            <li><span id="cancelreason3">等很久了，还未接单</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li class="y-otherrea">
                <span id="cancelreason4">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="4">
                <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
            </li>
        </ul>
    </script>
@stop

@section($js)
    @include('wap.community.order.orderjs')
    <script type="text/tpl" id="x-tkmodaltext">
        <div class="x-tkmodaltext">
            <p class="f18 x-tktitle mb5">恭喜获得<span class="c-red">{{ $activity['sharePromotionNum'] }}</span>张优惠券</p>
            <p class="f12 tc">分享优惠券给好友</p>
            <p class="f12 tc">可用于抵扣在线支付金额!</p>
        </div>
    </script>
    <script type="text/tpl" id="x-tkmodaltitle">
        <img src="{{ asset('wap/community/newclient/images/couponspic.png') }}" class="x-yhqtktop"><i class="icon iconfont c-white x-over">&#xe604;</i>
    </script>
    <script type="text/javascript" src="{{ asset('wap/community/newclient/js/dist/clipboard.min.js') }}"></script><!-- 复制 -->
    <script type="text/javascript">
        var is_topay_order = true;
        $(function(){
            $(document).off('click','.x-over');
            $(document).on('click','.x-over', function () {
                $(".modal").removeClass("modal-in").addClass("modal-out").remove();
                $(".modal-overlay").removeClass("modal-overlay-visible");
                $.notshowurl();
            });

            $(document).off('click','.topay_order');
            $(document).on('click','.topay_order',function(){
                if(is_topay_order){
                    is_topay_order = false;
                    window.location.href = "{{ u('Order/cashierdesk',['orderId'=>$data['id']]) }}";
                }
            });
            var orderId = "{{$data['id']}}";
            $(document).on('click','.y-ddfxbtn', function () {
                if (window.App){
                    var custom_type = [
                        "CUSTOM_WX",
                        "CUSTOM_WXF",
                        "CUSTOM_SINA",
                        "CUSTOM_QQ",
                        "CUSTOM_QZ",
                        "CUSTOM_CU"
                    ];
                    var share_data = {
                        share_content:'{!! $activity['detail'] !!}',
                        share_imageUrl:"{!! $activity['image'] !!}",
                        share_url:'{!! $link_url !!}',
                        share_key:1,
                        share_title:'{{$activity['title']}}' ,
                        custom_type: custom_type,
                        share_imageArr:[],
                    };
                    window.App.sdk_share(JSON.stringify(share_data));
                }else{
                    $(".size-frame").removeClass("none");
                }
            });
            $(document).on('touchend','.delorder', function () {
                $.confirm('确认删除订单吗？', '操作提示', function () {
                    $.delOrders(orderId);
                });
            }).on('touchend','.detail_confirmorder_{{$time}}', function () {
                $.confirm('确认完成订单吗？', '操作提示', function () {
                    $.confirmOrder(orderId)
                });
            }).on('touchend','.detail_cancelorder_{{$time}}', function () {
                var con = $("#cancelorder").val();
                var status = "{{ (int)$data['isContactCancel'] }}";
                if (status == "1") {
                    $.alert("商家已接单,如需取消订单请电话联系{{ $data['seller']['name'] }}:{{ $data['seller']['serviceTel'] or $data['seller']['mobile'] }}","tel:{{ $data['seller']['serviceTel'] or $data['seller']['mobile'] }}","提示");
                }else{
                    var textcancel = $("#cancehtml").html();

                    $.modal({
                        title:  '取消原因',
                        text: textcancel,
                        buttons: [
                            {text: '取消'},
                            {
                                text: '确定',
                                bold:true,
                                onClick: function() {
                                    var cancelradioval = $('.y-cancelreason input[name="reason"]:checked ').val();
                                    if(cancelradioval == 4){
                                        var cancelRemark = $("#cancelreasontext").val();
                                        cancelRemark = (cancelRemark == "") ? $("#cancelreason"+cancelradioval).html() : cancelRemark;
                                    }else{
                                        var cancelRemark = $("#cancelreason"+cancelradioval).html();
                                    }
                                    $.cancelOrder(orderId, cancelRemark);
                                }
                            }
                        ]
                    })
                }
            }).on('touchend','.pay_frames_tyle', function () {
                $(".pay_frames").removeClass('none').show();
            });

            //取消原因—其他原因
            $(document).on("click",".y-cancelreason li input",function(){
                $(".y-otherreasons").addClass("none");
            }).on("click",".y-cancelreason li.y-otherrea input",function(){
                $(".y-otherreasons").removeClass("none");
            })

            var payment = "{{$default_payment}}";

            $(document).on("touchend",".y-paylst li", function(){
                $(this).addClass("on").siblings().removeClass("on");
                payment = $(this).data("code");
            });

            $(document).on("touchend","#x-fwcansels",function(){
                if(payment == 'weixinJs'){
                    window.location.href = "{{ u('Order/wxpay',array('id'=>$data['id'])) }}&payment="+payment;
                }else{
                    window.location.href = "{{ u('Order/pay',array('id'=>$data['id'])) }}&payment="+payment;
                }
            });

            $(document).on("touchend",".reminderorder",function(){
                var orderId = "{{$data['id']}}";
                $.post("{{u('Order/urge')}}",{'id':orderId},function(result){
                    if(result.code == 0){
                        $('.x-bgtk').removeClass('none').show().find('.ts').text('催单成功');
                        $('.x-bgtk1').css({
                            position:'absolute',
                            left: ($(window).width() - $('.x-bgtk1').outerWidth())/2,
                            top: ($(window).height() - $('.x-bgtk1').outerHeight())/2 + $(document).scrollTop()
                        });
                        setTimeout(function(){
                            $('.x-bgtk').fadeOut('2000',function(){
                                $('.x-bgtk').addClass('none');
                            });
                        },'1000');
                    } else {
                        $.alert(result.msg);
                    }
                },'json');
            });

            @if(!empty($activity))
                @if($data['promotionIsShow'] != 1)
                $.notshowurl = function(){
                    $.post("{{ u('Order/notshow') }}",{orderId:orderId},function(result){
                    },'json');
                }
                $.alert($("#x-tkmodaltext").html(), $("#x-tkmodaltitle").html(), function () {
                    $.notshowurl();
                    if (window.App){
                        var banner = {!!json_encode($data['banner'])!!};
                        var custom_type = [
                            "CUSTOM_WX",
                            "CUSTOM_WXF",
                            "CUSTOM_SINA",
                            "CUSTOM_QQ",
                            "CUSTOM_QZ",
                            "CUSTOM_CU"
                        ];
                        banner = banner ? banner : [{!!json_encode($invitation['shareLogo'])!!}];
						var share_data = {
								share_content:'{!! $activity['detail'] !!}',
								share_imageUrl:"{!! $activity['image'] !!}",
								share_url:'{!! $link_url !!}',
								share_key:1,
								share_title:'{{$activity['title']}}' ,
								custom_type: custom_type,								
								share_imageArr:banner,
							};
                    window.App.sdk_share(JSON.stringify(share_data));
                }else{
                    clipCopy();
                    $(".size-frame").removeClass("none");
                }
            });

                @if(!empty($activity) && $activity['promotion'][0]['num'] > 0 && count($activity['logs']) >= $activity['sharePromotionNum'] && $data['promotionIsShow'] == 0)
                $.notshowurl();
                @endif

            @else
                $(document).on('click','.x-closebg,.y-sharecouponbtn', function () {
                    $(this).parents(".size-frame").addClass("none");
                    $(".modal").removeClass("modal-in").addClass("modal-out").remove();
                    $(".modal-overlay").removeClass("modal-overlay-visible");
                });
        @endif

        $(document).on('click','.x-closebg,.y-sharecouponbtn', function () {
            $(this).parents(".size-frame").addClass("none");
            $(".modal").removeClass("modal-in").addClass("modal-out").remove();
            $(".modal-overlay").removeClass("modal-overlay-visible");
        });
        $(document).on("click",".page-current .weiXinF",function(){
            show_weix_alert();
        });

        //分享到QQ空间
        $(document).off('click','.zonealert');
        $(document).on('click','.zonealert',function(){
            if("{{$bln}}" == 1){
                show_weix_alert();
            }else{
                zoneShare("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['content'] !!}",'{{$site_config['site_title']}}',"{!! $share['logo'] !!}");
            }
        });
        //分享到QQ
        $(document).off('click','.qqalert');
        $(document).on('click','.qqalert',function(){
            if("{{$bln}}" == 1){
                show_weix_alert();
            }else{
                zoneShare("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['content'] !!}",'{{$site_config['site_title']}}',"{!! $share['logo'] !!}",1);
            }
        });
        //分享到新浪微博
        $(document).off('click','.weiboalert');
        $(document).on('click','.weiboalert', function () {
            weiboShare("{!! $share['url'] !!}&shareSellerId={{$seller['id']}}&shareUserId={{$loginUserId}}","{{$share['title']}}","{!! $share['logo'] !!}");
        });

        $(document).on("click",".weixinalert",function(){
            $('.sha-frame').removeClass('none');
            $('.size-frame').addClass('none');
        })
        //xx以后不让他显示
        $(document).on("click",".sha-frame",function(){
            $(this).addClass('none');
        });
        //微信分享配置文件
        wx.config({
            debug: false, // 调试模式
            appId: "{{$weixin['appId']}}", // 公众号的唯一标识
            timestamp: "{{$weixin['timestamp']}}", // 生成签名的时间戳
            nonceStr: "{{$weixin['noncestr']}}", // 生成签名的随机串
            signature: "{{$weixin['signature']}}",// 签名
            jsApiList: ['checkJsApi','onMenuShareAppMessage','onMenuShareTimeline','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 需要使用的JS接口列表
        });
        wx.ready(function () {
            // 在这里调用 API
            wx.onMenuShareAppMessage({
                title: "{{$activity['title']}}", // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: "{!! $link_url !!}", // 分享链接
                imgUrl: "{{$activity['image']}}", // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareTimeline({
                title: '{{$activity['title']}}', // 分享标题
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQZone({
                title: '{{$activity['title']}}', // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareWeibo({
                title: '{{$activity['title']}}', // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareQQ({
                title: '{{$activity['title']}}', // 分享标题
                desc: "{!! $activity['detail'] !!}", // 分享描述
                link: '{!! $link_url !!}', // 分享链接
                imgUrl: '{{$activity['image']}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                    alert('分享成功');
                    location.reload();
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
        @endif
        $(document).on("click","#y-orderewmdiv",function(){
                    $('.y-qgdshoptc').removeClass("none");
                })
        $(document).on("click",".y-tcbg",function(){
            $('.y-qgdshoptc').addClass("none");
        })
    });
    </script>
    <script type="text/javascript">
        BACK_URL = "{!! u('Order/index') !!}";
        js_back = FANWE.JS_BACK;
        // 拨打电话
        $(function(){
            $(document).on('click','.y-call-{{$time}}', function () {
                $.alert('<div class="list-block media-list y-xddxqtc">\
                            <ul>\
                                <li>\
                                    <a href="" class="item-link item-content p0">\
                                        <div class="item-inner pr10">\
                                            <div class="item-title f16 w100">拨打电话</div>\
                                        </div>\
                                    </a>\
                                </li>\
                                <li>\
                                    <a href="tel:{{ $data["sellerTel"] }}" class="item-link item-content p0">\
                                        <div class="item-inner">\
                                            <div class="item-title f12 w100  tl" style="text-align:center">\
                                                <span>沟通-联系商家：</span>\
                                                <span class="c-gray">{{ $data["sellerTel"] }}</span>\
                                            </div>\
                                        </div>\
                                    </a>\
                                </li>\
                                <li>\
                                    <a href="tel:{{ $site_config["wap_service_tel"] }}" class="item-link item-content p0">\
                                        <div class="item-inner" >\
                                            <div class="item-title f12 w100  tl" style="text-align:center">\
                                                <span>投诉-联系客服：</span>\
                                                <span class="c-gray">{{ $site_config["wap_service_tel"] }}</span>\
                                            </div>\
                                        </div>\
                                    </a>\
                                </li>\
                            </ul>\
                        </div>', '', function () {
                });
                $(".modal-buttons .modal-button-bold").html("<span class='c-gray'>取消</span>");
                return false;
            });
        });
    </script>
@stop