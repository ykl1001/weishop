@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav y-barnav">
        <a class="button button-link button-nav pull-left external"  href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else {{u('GoodsCart/index')}} @endif"  data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">确认订单</h1>
    </header>
@stop
@section('css')
    <style type="text/css">
        .y-addrafter{width:100%;}
        .y-addrafter span{width: 73%;text-overflow: ellipsis;-webkit-line-clamp: 2;-webkit-box-orient: vertical;display: -webkit-inline-box;}
    </style>
@stop
@section('content')
    <script type="text/javascript">
        //BACK_URL = "{!! u('GoodsCart/index') !!}";
    </script>
    <?php
    //店铺类型
    $storeType = $data[0]['seller']['storeType'];

    // dd($fee,$data);
    $typename = $data[0]['type'] == 1 ? '配送方式' : '服务时间设置';
    $orderType = $data[0]['type'];

    $tab1 = $tab2 = true;

    $putoff = 1;

    //原价总价
    $oldPriceTotal = 0;
    //优惠减少总价
    $cutDownTotal = 0;

    //配送方式
    if($data[0]['sellerId'] != ONESELF_SELLER_ID){
        if($orderType == 1)
        {
            $sendway = [
                    1 => '送货上门',
                    2 => '到店消费',
                    3 => '到店自提',
            ];

            //配送时间
            $sendwayStr = [
                    1 => '配送时间',
                    2 => '到店时间',
                    3 => '自提时间',
            ];
            $sellerWayArr = explode(',', $data[0]['seller']['sendWay']); //配送方式:1商家配送,2到店消费,3到店自提
        }
        else
        {
            $sendway = [
                    1 => '上门服务',
                    2 => '到店消费',
            ];

            //配送时间
            $sendwayStr = [
                    1 => '上门时间',
                    2 => '到店时间',
            ];
            $sellerWayArr = explode(',', $data[0]['seller']['serviceWay']); //配送方式:1上门服务,2到店消费
        }


        foreach ($sellerWayArr as $key => $value) {
            $sendwayArr[$value] = $sendway[$value];
        }
    }else{
        if($orderType == 1){
            $sendway = [
                    1 => '员工配送'
            ];
            //配送时间
            $sendwayStr = [
                    1 => '配送时间'
            ];
            $sellerWayArr = explode(',', $data[0]['seller']['sendWay']); //配送方式:1商家配送,2到店消费,3到店自提
        }else{
            $sendway = [
                    1 => '上门服务'
            ];
            //上门时间
            $sendwayStr = [
                    1 => '服务时间'
            ];
            $sellerWayArr = explode(',', $data[0]['seller']['serviceMode']); //配送方式:1商家配送,2到店消费,3到店自提
        }
        foreach ($sellerWayArr as $key => $value) {
            $sendwayArr[$value] = $sendway[$value];
        }
    }

    //选择优惠券后的时间处理
    $inputAppTIme = Input::get('appTime');
    $hasAppTime = explode(" ", $inputAppTIme);

    //订单类型
    if($orderType == 1)
    {
        $orderTypeStr = '立即送出';
    }
    else
    {
        $orderTypeStr = '立即服务';
    }

    ?>

    <!-- 特别提醒开始 -->
    <!-- 如果后期有页面调整或再次套页面的时候一定要把class="content" 后面的style="bottom: 2.2rem;"加上，切记不要写在样式表里否则在XX分钟下单会出现不显示的情况 切记一定是行内样式 拜托了！ -->
    <!-- 特别提醒结束 -->
    <div class="content" style="bottom: 2.2rem;"> <!-- 套页面看注释 -->
        @if(!empty($address))
            @if($address['id'] > 0)
                <div class="card y-card active mt0 " @if(($sendType == 1 && $storeType != 1) || (empty($sendType) && $storeType == 1))  @else  style="display: none;"  @endif
                    @if($storeType == 1)
                        onclick="$.href('{!! u('UserCenter/address',['cartIds' => 10, 'arg'=>$cartIds, 'addressId'=>$address['id']]) !!}')"
                     @else
                        onclick="$.href('{!! u('UserCenter/address',['cartIds' => 10, 'arg'=>$cartIds, 'addressId'=>$address['id'], 'sellerId'=>$data[0]['seller']['id']]) !!}')"
                    @endif>
                    <php>
                        $name = mb_substr($address['name'], 0, 5, "utf-8") . (mb_strlen($address['name'], 'UTF8') > 5 ? "……" : "");
                    </php>
                    <div class="card-content">
                        <div class="fl ml10 mt15">送至：</div>
                        <div class="card-content-inner y-gwcaddr">
                            <p class="clearfix"><span class="mr10">{{ $name }}</span><span>{{ $address['mobile'] }}</span></p>
                            <p class="mt5">{{ $address['address'] }}</p>
                        </div>
                    </div>
                    <input type="hidden" name="addressId" id="addressId" value="{{ $address['id'] }}">
                    <input type="hidden" name="mobile" id="mobile" value="{{ $address['mobile'] }}">
                    <input type="hidden" name="mapPointStr" value="{{ $address['mapPointStr'] }}">
                </div>
            @else
                @if($storeType == 1)
                    <!-- 没有地址的情况下，全国店选择地址 -->
                    <div class="card y-card active mt0 " onclick="$.href('{!! u('UserCenter/address',['cartIds' => 10, 'arg'=>$cartIds]) !!}')">
                        <div class="card-content">
                            <div class="card-content-inner">
                                请选择收货地址<i class="icon iconfont fr c-gray">&#xe602;</i>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- 没有地址的情况下，周边店填写地址 -->
                    <div class="list-block y-xqrddaddr @if($sendType != 1) none @endif ">
                        <ul>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label f14">收货地址：</div>
                                        <div class="item-input f14 mt0 mb0">
                                            {{$address['address']}}
                                            <a @if($storeType == 1)
                                                onclick="$.href('{!! u('UserCenter/address',['cartIds' => 10, 'arg'=>$cartIds, 'addressId'=>$address['id']]) !!}')"
                                                @else
                                                onclick="$.href('{!! u('UserCenter/address',['cartIds' => 10, 'arg'=>$cartIds, 'addressId'=>$address['id'], 'sellerId'=>$data[0]['seller']['id']]) !!}')"
                                                @endif
                                                class="fr">
                                                更换地址
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label f14"></div>
                                    </div>
                                </div>
                                <div class="y-item-input f14">
                                    <input type="text" name="doorplate" id="doorplate" placeholder="详细地址（如门牌号等）" maxlength="50" value="{{ $userAddInfo['doorplate'] }}">
                                </div>
                            </li>
                            <li>
                                <div class="item-content mt10">
                                    <div class="item-inner">
                                        <div class="item-title label f14">联系人：</div>
                                    </div>
                                </div>
                                <div class="y-item-input f14">
                                    <input type="text" name="name" id="name" maxlength="8"  placeholder="您的姓名" value="{{ $userAddInfo['name'] }}">
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label f14">联系电话：</div>
                                    </div>
                                </div>
                                <div class="y-item-input f14">
                                    <input type="text" name="mobile" maxlength="11" id="mobile" onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="请输入联系电话" value="{{ $userAddInfo['mobile'] }}">
                                </div>
                            </li>
                            <li>
                                <div class="item-content save_address " data-val="0">
                                    <div class="item-inner">
                                        <p class="f14 tr w100">保存为常用地址<i class="icon iconfont ml10">&#xe612;</i></p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <input type="hidden" name="detailAddress" value="{{ $address['address'] }}">
                        <input type="hidden" name="mapPointStr" value="{{ $address['mapPointStr'] }}">
                        <input type="hidden" name="cityId" value="{{ $address['cityId'] }}">
                    </div>
                @endif
            @endif
        @else
            <?php
            $cartIds = $cartIds ? $cartIds : 1;
            ?>
            <div class="c-bgfff pt15 pb15 pl10 pr10 mb10" onclick="$.href('{!! u('UserCenter/address',['cartIds' => $cartIds , 'arg' => $cartIds,'newadd'=>1]) !!}')">
                <div class="f12">
                    <span>添加地址</span>
                    <i class="icon iconfont fr c-gray">&#xe602;</i>
                </div>
            </div>
        @endif

        @if($is_share_alert_show_data['downloadAddress'])
            <div class="list-block media-list y-iteminnerp mt10 tc showAppXz none">
                <ul>
                    <li class="pt10 pb10"   style="border: 2px solid red ;border-radius:10px;">
                        <a href="{{ $is_share_alert_show_data['downloadAddress'] }}">下载APP可使用{{$wap_promotion}}/生活抵用10%</a>
                    </li>
                </ul>
            </div>
            @endif


                    <!--  全国店不显示 周边店显示 -->
            @if($storeType == 0)
                <div class="list-block media-list y-iteminnerp">
                    <ul>
                        @if($orderType == 1)
                            <li>
                                <a href="#" class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">配送方式</div>
                                            <!-- 默认第一个元素值 -->
                                            <div class="item-after create-actions"><span class="sendway"> @if($sendStr) {{$sendStr}}  @else {{current($sendwayArr)}} @endif</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                                            <!-- 默认第一个元素key -->
                                            <input type="hidden" id="sendWay" @if($sendType) value="{{$sendType}}"  @else value="{{key($sendwayArr)}}" @endif>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @else
                                    <!-- @if($data[0]['sellerId'] == ONESELF_SELLER_ID) -->
                            <!-- @endif -->
                            <li>
                                <a href="#" class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">服务方式</div>
                                            <!-- 默认第一个元素值 -->
                                            <div class="item-after create-actions"><span class="sendway">@if($sendStr) {{$sendStr}}  @else {{current($sendwayArr)}} @endif</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                                            <!-- 默认第一个元素key -->
                                            <input type="hidden" id="sendWay" @if($sendType) value="{{$sendType}}"  @else value="{{key($sendwayArr)}}" @endif>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-inner">
                                    <div class="item-title-row f14">
                                        <div class="item-title sendwayStr"> @if($orderType == 1){{$sendwayStr[key($sendwayArr)]}}@else服务时间@endif </div>
                                        <div class="item-after y-dytime">
                                            <span class="c-red" id="beginTime">

                                               

                                                        @if( !empty($inputAppTIme) )
                                                    {{Input::get('appTime')}}
                                                @else
                                                    @if( Input::get('appTime') === '0' )
                                                        {{$orderTypeStr}}
                                                    @else
                                                        请选择时间
                                                    @endif
                                                @endif

                                                  



                                            </span>
                                            <i class="icon iconfont c-gray4">&#xe602;</i>
                                        </div>
                                        <input type="hidden" id="appTime"  value="{{ Input::get('appTime') }}">
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <ul class="y-paylst mb10" @if($fee['payFee'] < 0.001) style="display:none;" @endif>
                    <li class="on" data-code="1">
                        <div class="y-payf f14 ml0">在线支付</div>
                        <i class="icon iconfont">&#xe612;</i>
                    </li>
                    @if($fee['isCashOnDelivery'] == 1 && $data[0]['sellerId'] != ONESELF_SELLER_ID )
                        <li data-code="0">
                            <div class="y-payf f14 ml0">货到付款</div>
                            <i class="icon iconfont">&#xe612;</i>
                        </li>
                    @endif
                </ul>
            @endif
            <div class="list-block media-list y-iteminnerp" id="promotion-integral">

                <ul>
                    @if($fee['isShowPromotion'] == 1)
                        @if(IS_OPEN_FX)
                            @if($data[0]['shareUserId'] <= 0)
                                <li>
                                    <a href="javascript:$.usepromotion({{$fee['promotionMaxMoney']}})" class="item-link item-content pageloading" id="maxmoney" data-maxmoney="{{$fee['promotionMaxMoney']}}">
                                        <div class="item-inner">
                                            <div class="item-title-row f14">
                                                <div class="item-title">优&nbsp;惠&nbsp;券</div>
                                                <div>
                                                    @if($fee['discountFee'] > 0)
                                                        <!-- <div class="item-after c-red fl">-{{ $fee['discountFee'] }}</div> -->
                                                        <div class="item-after c-red fl promotion-text">-￥{{ $fee['discountFee'] or '0.00'}}</div>
                                                    @elseif($fee['promotionCount'] > 0)
                                                        <div class="item-after c-black fl promotion-text">可选择优惠券</div>
                                                    @else
                                                        <div class="item-after c-black fl promotion-text">无可用优惠券</div>
                                                    @endif
                                                    <i class="icon iconfont">&#xe602;</i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li>
                                <a href="javascript:$.usepromotion({{$fee['promotionMaxMoney']}})" class="item-link item-content pageloading" id="maxmoney" data-maxmoney="{{$fee['promotionMaxMoney']}}">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">优&nbsp;惠&nbsp;券</div>
                                            <div>
                                                @if($fee['discountFee'] > 0)
                                                    <!-- <div class="item-after c-red fl">-{{ $fee['discountFee'] }}</div> -->
                                                    <div class="item-after c-red fl promotion-text">-￥{{ $fee['discountFee'] or '0.00'}}</div>
                                                @elseif($fee['promotionCount'] > 0)
                                                    <div class="item-after c-black fl promotion-text">可选择优惠券</div>
                                                @else
                                                    <div class="item-after c-black fl promotion-text">无可用优惠券</div>
                                                @endif
                                                <i class="icon iconfont">&#xe602;</i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endif
                    @if($fee_off &&  ( $integralOpenType == $storeType || $integralOpenType == 2))
                        @if(IS_OPEN_FX)
                            @if($data[0]['shareUserId'] <= 0)
                                <li>
                                    <a href="#" class="item-link item-content">
                                        <div class="item-inner">
                                            <div class="item-title-row f14">
                                                <div class="item-title">{{$wap_integral}}抵扣</div>
                                                <div class="item-after">
                                                    @if($fee['integral'] > 0)
                                                        <span class="@if($fee['discountFee'] == 0) c-red @else c-black @endif integral cash-text">可用{{ $fee['integral'] }}{{$wap_integral}}抵用{{ $fee['cashMoney'] }}元</span>
                                                        @if($fee['discountFee'] == 0)
                                                            <i class="icon iconfont ml10 c-red y-redcircle active y-roll">&#xe612;</i>
                                                        @else
                                                            <i class="icon iconfont ml10 c-red y-redcircle y-roll">&#xe612;</i>
                                                        @endif
                                                    @else
                                                        <span class="c-gray">无可用{{$wap_integral}}</span>
                                                        <i class="icon iconfont ml10 c-red y-redcircle">&#xe612;</i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li>
                                <a href="#" class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">{{$wap_integral}}抵扣</div>
                                            <div class="item-after">
                                                @if($fee['integral'] > 0)
                                                    <span class="@if($fee['discountFee'] == 0) c-red @else c-black @endif integral cash-text">可用{{ $fee['integral'] }}{{$wap_integral}}抵用{{ $fee['cashMoney'] }}元</span>
                                                    @if($fee['discountFee'] == 0)
                                                        <i class="icon iconfont ml10 c-red y-redcircle active y-roll">&#xe612;</i>
                                                    @else
                                                        <i class="icon iconfont ml10 c-red y-redcircle y-roll">&#xe612;</i>
                                                    @endif
                                                @else
                                                    <span class="c-gray">无可用{{$wap_integral}}</span>
                                                    <i class="icon iconfont ml10 c-red y-redcircle">&#xe612;</i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>

            <div class="list-block media-list y-iteminnerp">
                <div class="list-block media-list y-drddlist">
                    <ul>
                        <li>
                            @if($data[0]['seller']['storeType'] == 1)
                                <a href="{{ u('Seller/detail', ['id'=>$data[0]['seller']['id']]) }}" class="item-link item-content">
                            @else
                                <a href="{{ u('Goods/index', ['id'=>$data[0]['seller']['id']]) }}" class="item-link item-content">
                            @endif
                                <div class="item-inner">
                                    <div class="item-title bold"><i class="icon iconfont vat mr5">&#xe632;</i>{{$data[0]['seller']['name']}}</div>
                                </div>
                            </a>
                        </li>
                        @foreach($data as $val)
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-inner">
                                        <?php
                                        if(!empty($val['stockGoods']))
                                        {
                                            $val['price'] = $val['stockGoods']['price'];
                                        }
                                        $oldPriceTotal += $val['num'] * $val['price'];
                                        ?>
                                        <div class="item-title-row f14">
                                            <div class="item">{{$val['goods']['name']}}</div>
                                            <div class="item-after c-black"><span class="c-gray">x{{$val['num']}}</span><span class="pl20">￥{{$val['price']*$val['num']}}</span></div>
                                        </div>
                                        @if($val['stockGoods'])
                                            <div class="item-title f12 c-gray">{{str_replace(':','-',$val['stockGoods']['skuName'])}}</div>
                                        @endif
                                </div>
                            </a>
                        </li>
                        @endforeach

                        @if($orderType == 1)
                            <?php
                            if( in_array(key($sendwayArr), [2,3]) ){
                                $oldPriceTotal;
                            }else{
                                $oldPriceTotal += $fee['freight'];
                            }
                            ?>
                            <li id="freight" class="@if( in_array(key($sendwayArr), [2,3]) ) none @endif">
                                <a href="#" class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">配送费</div>
                                            <div class="item-after c-black"><span>￥{{ $fee['freight'] or '0.00'}}</span></div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @else
                            <?php
                            $oldPriceTotal += $fee['freight'];
                            ?>
                            <li id="freight" class="@if( in_array(key($sendwayArr), [2,3]) ) none @endif">
                                <a href="#" class="item-link item-content">
                                    <div class="item-inner">
                                        <div class="item-title-row f14">
                                            <div class="item-title">配送费</div>
                                            <div class="item-after c-black"><span>￥{{ $fee['freight'] or '0.00'}}</span></div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif

                        <li id="fee-content">
                            <a href="#" class="item-link item-content">
                                <div class="item-inner">
                                    @if($fee['firstOrderCutMoney'] > 0)
                                        <?php
                                        $cutDownTotal += $fee['firstOrderCutMoney'];
                                        ?>
                                        <div class="item-title-row f14" id="firstOrderCutMoney">
                                            <div class="item-title">首单优惠</div>
                                            <div class="item-after c-black"><span>-￥{{ $fee['firstOrderCutMoney'] or '0.00'}}</span></div>
                                        </div>
                                    @endif
                                    @if($fee['fullOrderCutMoney'] > 0)
                                        <?php
                                        $cutDownTotal += $fee['fullOrderCutMoney'];
                                        ?>
                                        <div class="item-title-row f14" id="fullOrderCutMoney">
                                            <div class="item-title">满减优惠</div>
                                            <div class="item-after c-black"><span>-￥{{ $fee['fullOrderCutMoney'] or '0.00'}}</span></div>
                                        </div>
                                    @endif
                                    @if($fee['specialOrderCutMoney'] > 0)
                                        <?php
                                        $cutDownTotal += $fee['specialOrderCutMoney'];
                                        ?>
                                        <div class="item-title-row f14" id="specialOrderCutMoney">
                                            <div class="item-title">特价优惠</div>
                                            <div class="item-after c-black"><span>-￥{{ $fee['specialOrderCutMoney'] or '0.00'}}</span></div>
                                        </div>
                                    @endif
                                    @if($fee['cashMoney'] > 0)
                                        <?php
                                        if(Input::get('price') > 0){
                                            $cutDownTotal += $fee['cashMoney'];
                                        }else{
                                            if($fee['discountFee'] > 0){

                                            }else{
                                                $cutDownTotal += $fee['cashMoney'];
                                            }
                                        }
                                        ?>
                                        <div class="item-title-row f14" id="cashMoney">
                                            <div class="item-title">{{$wap_integral}}抵扣</div>
                                            @if($fee['discountFee'] == 0)
                                                <div class="item-after c-black"><span id="cash-money">-￥{{ $fee['cashMoney'] or '0.00'}}</span></div>
                                            @else
                                                <div class="item-after c-black"><span id="cash-money">-￥0</span></div>
                                            @endif
                                        </div>
                                    @endif
                                    @if($fee['discountFee'] > 0)
                                        <?php
                                        if(Input::get('price') > 0){
                                            if($fee['cashMoney'] > 0){

                                            }else{
                                                $cutDownTotal += $fee['discountFee'];
                                            }
                                        }else{
                                            $cutDownTotal += $fee['discountFee'];
                                        }
                                        ?>
                                        <div class="item-title-row f14" id="discountFee">
                                            <div class="item-title">{{$wap_promotion}}</div>
                                            <div class="item-after c-black"><span id="discount-fee">-￥{{ $fee['discountFee'] or '0.00'}}</span></div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
                                <div class="item-inner">
                                    <div class="item-title-row f12">
                                        <div class="item-title c-gray">原价<span class="c-black" id="oldPriceTotal">￥{{$oldPriceTotal or '0.00'}}</span> - 共优惠<span class="c-black" id="cutDownTotal">￥{{$cutDownTotal or '0.00'}}</span></div>
                                        <div class="item-after c-black">总计
                                            :<span id="total-money">￥<?php
                                                $newtoTalFee =  $fee['totalFee'] - $fee['freight'] > 0 ? $fee['totalFee'] - $fee['freight'] :0;
                                                ?>
                                                @if( in_array(key($sendwayArr), [2,3]) )
                                                    {{$newtoTalFee}}
                                                @else
                                                    {{ $fee['totalFee'] or '0'}}
                                                @endif</span></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="list-block media-list y-qrddqt">
                <ul>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-title-row f14">
                                    <div class="item-title">备注</div>
                                </div>
                                <div class="item-title-row f14">
                                    <input type="text" name="buyRemark" id="buyRemark" placeholder="请填写备注信息(非必填)" class="y-qrddinput">
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="content-block-title f14 c-gray">
                    <i class="icon iconfont vat">&#xe646;</i>
                    @if($storeType == 1)
                        请在{{ $time }}之前完成支付。
                    @else
                        请在下单后{{ $time }}分钟内完成支付。
                    @endif
                </div>
            </div>

            <!-- 全部筛选 -->
            <div class="x-sjfltab pf y-time none">
                <div class="mask pa"></div>
                <div class="y-bottom">
                    <div class="buttons-tab fl pr">
                        <div class="y-noscroll">
                            @foreach($sellerAllowTime as $key => $value)
                                @if(count($value['list']) > 0)
                                    <a href="#tab1_{{$key}}" class="tab-link button @if($tab1) active @endif timestampDay" data-day="{{$value['time']}}">{{$value['dayName']}}</a>
                                    <?php $tab1 = false ?>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="tabs c-bgfff fl app-time-day">
                        @foreach($sellerAllowTime as $key => $value)
                            @if(count($value['list']) > 0)
                                <div id="tab1_{{$key}}" class="tab p-tab @if($tab2) active @endif">
                                    <div class="list-block x-sortlst f14">
                                        <ul>
                                            @foreach($value['list'] as $k => $v)
                                                @if($value['time'] == Time::toDate(UTC_TIME, 'Y-m-d'))
                                                    @if( $putoff == 1 )
                                                        <!-- 当天，有立即送出 -->
                                                        <li class="item-content timestampTime isNow active @if(in_array(key($sendwayArr),[2,3])) none @endif" data-time="0" data-one="{{$v}}">
                                                            <div class="item-inner">
                                                                <div class="item-title">{{$orderTypeStr}}<p><small>（大约{{$v}}到）</small></p></div>
                                                                <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                            </div>
                                                        </li>
                                                    @else
                                                        @if($value['timestamp'][$k] < $sellerAllowTime[0]['timestamp'][0])
                                                            <!-- 商家配送的时候这个时间段小于立即送出，不显示；但是到店需要显示这段时间 -->
                                                            <li class="item-content isNextNow timestampTime @if(!in_array(key($sendwayArr),[2,3])) none @endif" data-time="{{$v}}">
                                                                <div class="item-inner">
                                                                    <div class="item-title">{{$v}}</div>
                                                                    <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                                </div>
                                                            </li>
                                                            @else
                                                                    <!-- 商家配送和到店均显示 -->
                                                            <li class="item-content timestampTime" data-time="{{$v}}">
                                                                <div class="item-inner">
                                                                    <div class="item-title">{{$v}}</div>
                                                                    <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    <?php $putoff++; ?>
                                                    @else
                                                            <!-- 非当天 -->
                                                    <li class="item-content timestampTime" data-time="{{$v}}">
                                                        <div class="item-inner">
                                                            <div class="item-title">{{$v}}</div>
                                                            <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                                        </div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <?php $tab2 = false ?>
                            @endif
                        @endforeach
                    </div>
                    <div class="row c-bgfff tc">
                        <div class="col-100 f16">取消</div>
                    </div>
                </div>
            </div>
    </div>
    <div class="bar bar-footer">
        <div class="f12 y-zfmoney">
            <span class="c-gray">已优惠<span id="cutDownTotal-2">￥{{$cutDownTotal or '0'}}</span></span>
            <span>还需付<span class="c-red f15" id="total-money-2">￥
                    <?php
                    $newtoTalFee =  $fee['totalFee'] - $fee['freight'] > 0 ? $fee['totalFee'] - $fee['freight'] :0;
                    ?>
                    @if( in_array(key($sendwayArr), [2,3]) )
                        {{$newtoTalFee}}
                    @else
                        {{ $fee['totalFee'] or '0'}}
                    @endif</span></span>
        </div>
        <a class="x-menuok c-bg c-white f16 fr" id="x-fwcansels">确认下单</a>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        @if($storeType != 1)
        var sellerType = "{{$storeType}}";
        var send_type = "{{$sendType ? $sendType : key($sendwayArr) }}";
        var appTime = "{{Input::get('appTime')}}";
        var str= "{{$sendStr}}";
        if(!str){
            str = "{{current($sendwayArr)}}";
        }
        var time,val;

        if(send_type == 1 && appTime == 0){
            time =  $('.isNow').data('one');
            val = '{{$orderTypeStr}}'+' | 预计'+time;
            appTime = 0;
        }else if(send_type == 1 && appTime != 0){
            val = appTime;
        }else if(send_type != 1 && appTime != 0){
            val = appTime;

        }else if(send_type != 1 && appTime == 0){
            time =  $('.isNow').data('one');
            // val = '{{$orderTypeStr}}'+' | 预计'+time;
            val = '请选择时间';
            appTime = 0;
        }else{
            val = '请选择时间';
        }


        if(send_type == 1){
            $("#beginTime").text(val);
            $("#appTime").val(appTime);
            $('.sendway').text(str);
            $('#sendWay').val(send_type);
            $('#freight').removeClass('none');
            $('.y-xqrddaddr').removeClass('none');
            // $('.y-card').removeClass('none');
            $('.y-card').css('display','block');//显示地址
        }else{
            $('.y-xqrddaddr').addClass('none');
            $('#freight').addClass('none');
            $('.y-card').css('display','none');//显示地址
            $('.sendway').text(str);
            $('#sendWay').val(send_type);
            $("#beginTime").text(val);
        }
        @endif



            if (window.App) {
            // $(".showAppXz").addClass("none");
        }
        var timeName = $(".sendwayStr").text();
        var hasAppTimeYear = "{{$hasAppTime[0]}}";
        var hasAppTimeDay = "{{$hasAppTime[1]}}";
        var labelHref = "";

        //跳转优惠券后返回之后的时间处理
        if(hasAppTimeYear)
        {
            $("div.y-noscroll a").each(function(k, v){
                if($(this).data('day') == hasAppTimeYear)
                {
                    $("div.y-noscroll a").removeClass('active');
                    $(this).addClass('active');
                    labelHref = $(this).attr('href');
                }
            });
            if(hasAppTimeDay)
            {
                $("div.app-time-day div.p-tab").removeClass('active');
                $(labelHref).addClass('active');

                $(labelHref+" ul li").each(function(k, v){
                    if($(this).data('time') == hasAppTimeDay)
                    {
                        $(labelHref+" ul li").removeClass('active');
                        $(this).addClass('active');
                    }
                });
            }
        }



        $(function(){

            var userInfo = new Object();
            $("#doorplate").keyup(function(){
                var doorplate = $.trim($("#doorplate").val());
                userInfo.doorplate = doorplate;
                $.post("{{ u('Order/saveUserAddressInfo') }}",userInfo,function(res){},"json");
            });

            $("#name").keyup(function(){
                var name = $.trim($("#name").val());
                userInfo.name = name;
                $.post("{{ u('Order/saveUserAddressInfo') }}",userInfo,function(res){},"json");
            });

            $("#mobile").keyup(function(){
                var mobile = $.trim($("#mobile").val());
                userInfo.mobile = mobile;
                $.post("{{ u('Order/saveUserAddressInfo') }}",userInfo,function(res){},"json");
            });
        })


        var payment = "1";
        var isUseIntegral = "1";
        var isSaveAddress = 0;
        $(document).on("click",".y-paylst li",function(){
            $(this).addClass("on").siblings().removeClass("on");
            payment = $(this).data("code");
            $.orderCompute(2);
        });
        $(document).on("focus","#buyRemark",function(){
            setTimeout(function(){
                $(".content").scrollTop(1000);
            },500);
        });
        $(document).on("click","#buyRemark",function(){
            setTimeout(function(){
                $(".content").scrollTop(1000);
            },500);
        });
        $(".save_address").click(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                isSaveAddress = 0;
            } else {
                $(this).addClass('active');
                isSaveAddress = 1;
            }
        });


        $(document).on("click","#x-fwcansels",function(){
            var addressId     = $("input[name=addressId]").val();
            var freType       = $(".sendway").text();  //配送方式文字
            var appTime       = $("#appTime").val();
            var orderType     = "{{$orderType}}";
            var invoiceRemark = $("input[name=invoiceRemark]").val();
            var buyRemark     = $("input[name=buyRemark]").val();
            var giftRemark    = $("input[name=giftRemark]").val();
            var id            = "{{ $cartIds }}";
            var promotionSnId = "{{ $promotion['id'] }}";
            var sendWay       = $("#sendWay").val();  //配送方式编号
            var detailAddress = $("input[name=detailAddress]").val();
            var mapPoint      = $("input[name=mapPointStr]").val();
            var cityId        = $("input[name=cityId]").val();
            var doorplate     = $("input[name=doorplate]").val();
            var name          = $("input[name=name]").val();
            var mobile        = $("input[name=mobile]").val();
            var reg = /^1\d{10}$/;
            var storeType     = "{{ $storeType }}";



            if(sendWay == 1 && !reg.test(mobile) && storeType != 1){
                $.toast("请输入正确的手机号码");
                return false;
            }


//            if($('.y-roll').hasClass("active")){
//            }
            if(!$('.y-roll').hasClass("active")){
                isUseIntegral = 0;
            }else{
                promotionSnId = 0;

            }


            var obj ={
                addressId: addressId,
                freType:freType,
                appTime:appTime,
                orderType:orderType,
                invoiceTitle:invoiceRemark,
                buyRemark:buyRemark,
                giftContent:giftRemark,
                cartIds:id,
                payment:payment,
                promotionSnId:promotionSnId,
                sendWay:sendWay,
                isUseIntegral:isUseIntegral,
                detailAddress:detailAddress,
                mapPoint:mapPoint,
                cityId:cityId,
                doorplate:doorplate,
                name:name,
                mobile:mobile,
                isSaveAddress:isSaveAddress,
                storeType:storeType
            };

            if(appTime == ""){
                $.alert("请选择"+timeName);
                return false;
            }


            $.showPreloader('正在创建订单...');
            $.post("{{ u('Order/toOrder') }}",obj,function(res){
                $.hidePreloader();
                if(res.code == 0) {
                    $(".x-tksure").addClass("none");
                    if (res.data.payStatus == "{{ ORDER_PAY_STATUS_YES }}" || payment == 0) {
                        $.alert(res.msg);
                        $.router.load("{{ u('Order/detail',array('id'=>ids)) }}".replace("ids", res.data.id), true);
                    }else{
                        $.alert(res.msg + "进入第三方支付");
                        // window.location.href = "{{ u('Order/orderpay',array('orderId'=>ids)) }}".replace("ids", res.data.id);
                        //$.router.load("{{ u('Order/cashierdesk',array('orderId'=>ids)) }}".replace("ids", res.data.id), true);
                        $.href("{{ u('Order/cashierdesk',array('orderId'=>ids)) }}".replace("ids", res.data.id));
                    }
                }else{
                    $.alert(res.msg);
                }
            },"json")

        });
        $(document).on("touchend",".y-qrddqt li p span",function(){
            if($(this).parents("li").hasClass("on")){
                $(this).parents("li").removeClass("on");
            }else{
                $(this).parents("li").addClass("on");
            }
        });


        $(document).on("click", ".y-roll", function(){
            $.orderCompute();
        });
        $(document).on("click", ".y-roll2", function(){
            $.orderCompute(2);
        });
        $(document).on("click", ".y-bottom .tabs .list-block li", function(){
            $(this).addClass("active").siblings().removeClass("active");

            var day = $('.timestampDay.active').data('day');
            var time = $('.p-tab.active .timestampTime.active').data('time');
            var times =  $('.isNow').data('one');
            if(time==0){
                // $("#beginTime").text('{{$orderTypeStr}}');
                $("#beginTime").text('{{$orderTypeStr}}'+' | 预计'+times);
                $("#appTime").val(0);
            }else{
                $("#beginTime").text(day+' '+time);
                $("#appTime").val(day+' '+time);
            }


            $(".y-bottom .row").parents(".y-time").addClass("none");
            FANWE.JS_BACK_HANDLER = null;
        });
        $(document).on("click", ".y-bottom .row", function(){
            $(this).parents(".y-time").addClass("none");
            FANWE.JS_BACK_HANDLER = null;
        });
        $(document).on("click", ".y-time .mask", function(){
            $(this).parents(".y-time").addClass("none");
            FANWE.JS_BACK_HANDLER = null;
        });
        $(document).on("click", ".y-dytime", function(){
            FANWE.JS_BACK_HANDLER = function() {
                $(".y-time").addClass("none");
                return true;
            }
            $(".y-time").removeClass("none");
        });

        /* 配送方式 */
        $(document).off('click','.create-actions');
        $(document).on('click','.create-actions', function () {

            FANWE.JS_BACK_HANDLER = function() {
                $.closeModal();
                return true;
            }
            var buttons1 = [
                {
                    text: '请选择配送方式',
                    label: true
                },
                @foreach($sendwayArr as $key => $value)
                {
                    text: '{{$value}}',
                    bold: true,
                    color: 'danger',
                    onClick: function() {
                        $(".sendway").text('{{$value}}');
                        $("#sendWay").val('{{$key}}');
                        $('.sendwayStr').text("{{ $sendwayStr[$key] }}");
                        timeName = "{{ $sendwayStr[$key] }}";
                        @if(in_array($key, [2,3]))
                        $(".isNow").addClass("none");
                        $(".isNextNow").removeClass("none");
                        $("#beginTime").text('请选择时间');
                        $("#appTime").val('');
                        $("#freight").addClass('none'); //不显示运费
                        $('.y-xqrddaddr').addClass('none');//不显示地址
                        // $('.y-card').addClass('none');
                        $('.y-card').css('display','none');//显示地址



                        // $("#firstOrderCutMoney").addClass('none'); //不显示首单
                        // $("#fullOrderCutMoney").addClass('none'); //不显示满减
                        // $("#specialOrderCutMoney").addClass('none'); //不显示特价
                        @else
                            $('.isNow').removeClass("none");

                        $('.isNextNow').addClass("none");
                        $("#freight").removeClass('none'); //显示运费
                        var times =  $('.isNow').data('one');
                        $('.y-xqrddaddr').removeClass('none');//显示地址
                        //  $('.y-card').removeClass('none');//显示地址
                        $('.y-card').css('display','block');//显示地址



                        $("#beginTime").text('{{$orderTypeStr}}'+' | 预计'+times);

                        // $("#firstOrderCutMoney").removeClass('none'); //显示首单
                        // $("#fullOrderCutMoney").removeClass('none'); //显示满减
                        // $("#specialOrderCutMoney").removeClass('none'); //显示特价
                        @endif
                        $('.list-block .timestampTime').removeClass('active');
                        $.orderCompute();
                        FANWE.JS_BACK_HANDLER = null;
                    }
                },
                @endforeach
              ];
            var buttons2 = [
                {
                    text: '取消',
                    bg: 'danger',
                    onClick: function() {
                        FANWE.JS_BACK_HANDLER = null;
                    }
                }
            ];
            var groups = [buttons1, buttons2];
            $.actions(groups);
        });

        /*选择优惠卷*/
        $.usepromotion = function(MaxMoney) {
            var storeType = "{{$storeType}}";
            var _appTime = $("#appTime").val();

            if(storeType == 1){
                url = "{{ u('Coupon/usepromotion') }}?cartIds={{$cartIds}}&addressId={{Input::get('addressId')}}&sellerId={{$fee['sellerId']}}&money="+MaxMoney+"&appTime="+_appTime;


            }else{
                var _sendWay = $('.sendway').text();
                var _sendType = $('#sendWay').val();
                url = "{{ u('Coupon/usepromotion') }}?cartIds={{$cartIds}}&addressId={{Input::get('addressId')}}&sellerId={{$fee['sellerId']}}&money="+MaxMoney+"&appTime="+_appTime+"&sendWay="+_sendWay+"&sendType="+_sendType;

            }

            $.router.load(url, true);
        }

        /**
         * 计算价格
         */
        $.orderCompute = function(type) {
            var goodsFee = Number("{{ $fee['goodsFee'] }}"); //商品金额
            var payMoney = Number("{{ $fee['payFee'] }}"); //支付金额
            var totalMoney = Number("{{ $fee['totalFee'] }}");  //合计
            var cashMoney = Number("{{ $fee['cashMoney'] }}");  //积分抵扣

            var firstOrderCutMoney = Number("{{ $fee['firstOrderCutMoney'] }}");
            var fullOrderCutMoney = Number("{{ $fee['fullOrderCutMoney'] }}");
            var specialOrderCutMoney = Number("{{ $fee['specialOrderCutMoney'] }}");

            var discountFee = Number("{{ $fee['discountFee'] }}");

            var freight = Number("{{ $fee['freight'] }}"); //配送费
            var sendWay = $("#sendWay").val();  //配送方式编号

            var storeType = "{{ $data[0]['seller']['storeType'] }}"; //店铺类型

            if(discountFee > 0){
                var isUseIntegral = 0;
            }else{
                var isUseIntegral = 1;
            }
            //全国店
            if(storeType == 1)
            {
                goodsFee += freight;

                if(type == 2){ //不变就算钱
                    //线上支付
                    if(!$('.y-roll').hasClass("active")){
                        //不使用积分
                        isUseIntegral = 0;
                        $('.y-roll').removeClass("active");
                        $("#cash-money").html("-￥0");
                        $('.y-roll2').addClass("active");
                        $("#discount-fee").html("-￥"+discountFee);
                        if((payMoney + cashMoney) > 0.001){
                            $(".y-paylst").css("display", "block");
                        }
                        cashMoney = 0;
                        if(discountFee > 0){
                            $(".promotion-text").removeClass('c-black').addClass('c-red').html('-￥'+discountFee);
                        }else{
                            $(".promotion-text").addClass('c-black').removeClass('c-red').html('未选择优惠券');
                        }
                        $(".cash-text").removeClass('c-red');
                    }else{
                        //使用积分
                        isUseIntegral = 1;
                        $('.y-roll').addClass("active");
                        $("#cash-money").html("-￥" + cashMoney);
                        $('.y-roll2').removeClass("active");
                        $("#discount-fee").html("-￥0");
                        goodsFee = goodsFee - cashMoney;
                        if(payMoney < 0.001){
                            $(".y-paylst").css("display", "none");
                        }
                        discountFee = 0;
                        $(".promotion-text").removeClass('c-red').html('未选择优惠券');
                        $(".cash-text").addClass('c-red');
                    }
                }else{ //变了换了
                    //线上支付
                    if($('.y-roll').hasClass("active")){
                        //不使用积分
                        isUseIntegral = 0;
                        $('.y-roll').removeClass("active");
                        $("#cash-money").html("-￥0");
                        $('.y-roll2').addClass("active");
                        $("#discount-fee").html("-￥"+discountFee);
                        if((payMoney + cashMoney) > 0.001){
                            $(".y-paylst").css("display", "block");
                        }
                        cashMoney = 0;
                        if(discountFee > 0){
                            $(".promotion-text").removeClass('c-black').addClass('c-red').html('-￥'+discountFee);
                        }else{
                            $(".promotion-text").addClass('c-black').removeClass('c-red').html('未选择优惠券');
                        }
                        $(".cash-text").removeClass('c-red');
                    }else{
                        //使用积分
                        isUseIntegral = 1;
                        $('.y-roll').addClass("active");
                        $("#cash-money").html("-￥" + cashMoney);
                        $('.y-roll2').removeClass("active");
                        $("#discount-fee").html("-￥0");
                        goodsFee = goodsFee - cashMoney;
                        if(payMoney < 0.001){
                            $(".y-paylst").css("display", "none");
                        }
                        discountFee = 0;
                        $(".promotion-text").removeClass('c-red').html('未选择优惠券');
                        $(".cash-text").addClass('c-red');
                    }
                }


                goodsFee = goodsFee - discountFee - firstOrderCutMoney - fullOrderCutMoney - specialOrderCutMoney;

                goodsFee = goodsFee >= 0 ? goodsFee : 0;

                var cutDownTotal = Number("{{ $fee['goodsFee'] }}") + Number("{{ $fee['freight'] }}") - Number(goodsFee);
                $("#cutDownTotal,#cutDownTotal-2").html("￥" + Number(cutDownTotal.toFixed(2)));
                $("#total-money,#total-money-2,#pay-money").html("￥" + Number(goodsFee.toFixed(2)));
                // if(isUseIntegral == 0){
                // var m = Number($("#maxmoney").attr('data-maxmoney'))+cashMoney;
                // $("#maxmoney").attr('href',"javascript:$.usepromotion("+m.toFixed(2)+")");
                // }else{
                // $("#maxmoney").attr('href',"javascript:$.usepromotion("+$("#maxmoney").attr('data-maxmoney')+")");
                // }

            }
            //周边店
            else
            {
                //商家配送 + 运费
                if(sendWay == 1)
                {
                    goodsFee += freight;
                }

                //计算新的抵扣积分
                var discount_goodsFee = goodsFee- firstOrderCutMoney - fullOrderCutMoney - specialOrderCutMoney;  //纳入计算积分的金额
                $.post("{{ u('order/recountCashMoney') }}", {'payFee':discount_goodsFee}, function(result){
                    $("span.integral").text("可用"+result.integral+"积分抵用"+result.cashMoney+"元");
                    cashMoney = Number(result.cashMoney);

                    if(payment == 0)
                    {
                        //线下支付 不享受优惠
                        // $("#cash-money").html("-￥0");
                        // $("#discount-fee").html("-￥0");
                        $("#promotion-integral,#cashMoney,#discountFee").addClass('none'); //不显示优惠券 积分
                        $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney,#fee-content").addClass('none');  //不显示首单满减特价
                        $("#x-fwcansels").html("确认下单");
                    }
                    else
                    {
                        if(type == 2){ //不变就算钱
                            //线上支付
                            if(!$('.y-roll').hasClass("active")){
                                //不使用积分
                                isUseIntegral = 0;
                                $('.y-roll').removeClass("active");
                                $("#cash-money").html("-￥0");
                                $('.y-roll2').addClass("active");
                                $("#discount-fee").html("-￥"+discountFee);
                                if((payMoney + cashMoney) > 0.001){
                                    $(".y-paylst").css("display", "block");
                                }
                                cashMoney = 0;
                                if(discountFee > 0){
                                    $(".promotion-text").removeClass('c-black').addClass('c-red').html('-￥'+discountFee);
                                }else{
                                    $(".promotion-text").addClass('c-black').removeClass('c-red').html('未选择优惠券');
                                }
                                $(".cash-text").removeClass('c-red');
                            }else{
                                //使用积分
                                isUseIntegral = 1;
                                $('.y-roll').addClass("active");
                                $("#cash-money").html("-￥" + cashMoney);
                                $('.y-roll2').removeClass("active");
                                $("#discount-fee").html("-￥0");
                                goodsFee = goodsFee - cashMoney;
                                if(payMoney < 0.001){
                                    $(".y-paylst").css("display", "none");
                                }
                                discountFee = 0;
                                $(".promotion-text").removeClass('c-red').html('未选择优惠券');
                                $(".cash-text").addClass('c-red');
                            }
                        }else{ //变了换了
                            //线上支付
                            if($('.y-roll').hasClass("active")){
                                //不使用积分
                                isUseIntegral = 0;
                                $('.y-roll').removeClass("active");
                                $("#cash-money").html("-￥0");
                                $('.y-roll2').addClass("active");
                                $("#discount-fee").html("-￥"+discountFee);
                                if((payMoney + cashMoney) > 0.001){
                                    $(".y-paylst").css("display", "block");
                                }
                                cashMoney = 0;
                                if(discountFee > 0){
                                    $(".promotion-text").removeClass('c-black').addClass('c-red').html('-￥'+discountFee);
                                }else{
                                    $(".promotion-text").addClass('c-black').removeClass('c-red').html('未选择优惠券');
                                }
                                $(".cash-text").removeClass('c-red');
                            }else{
                                //使用积分
                                isUseIntegral = 1;
                                $('.y-roll').addClass("active");
                                $("#cash-money").html("-￥" + cashMoney);
                                $('.y-roll2').removeClass("active");
                                $("#discount-fee").html("-￥0");
                                goodsFee = goodsFee - cashMoney;
                                if(payMoney < 0.001){
                                    $(".y-paylst").css("display", "none");
                                }
                                discountFee = 0;
                                $(".promotion-text").removeClass('c-red').html('未选择优惠券');
                                $(".cash-text").addClass('c-red');
                            }
                        }


                        goodsFee = goodsFee - discountFee - firstOrderCutMoney - fullOrderCutMoney - specialOrderCutMoney;
                        $("#promotion-integral,#cashMoney,#discountFee").removeClass('none');  //显示优惠券 积分
                        $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney,#fee-content").removeClass('none');  //显示首单满减特价
                    }

                    goodsFee = goodsFee >= 0 ? goodsFee : 0;

                    //商家配送 + 运费
                    if(sendWay == 1)
                    {
                        var cutDownTotal = Number("{{ $fee['goodsFee'] }}") + Number("{{ $fee['freight'] }}") - Number(goodsFee);
                        var oldPriceTotal = Number("{{ $fee['goodsFee'] }}") + Number("{{ $fee['freight'] }}");
                        $("#oldPriceTotal").html("￥" + Number(oldPriceTotal));
                    }else{
                        var cutDownTotal = Number("{{ $fee['goodsFee'] }}") - Number(goodsFee);
                        $("#oldPriceTotal").html("￥" + Number("{{ $fee['goodsFee'] }}"));
                    }

                    $("#cutDownTotal,#cutDownTotal-2").html("￥" + Number(cutDownTotal.toFixed(2)));
                    $("#total-money,#total-money-2,#pay-money").html("￥" + Number(goodsFee.toFixed(2)));

                    // if(isUseIntegral == 0){
                    // var m = Number($("#maxmoney").attr('data-maxmoney'))+cashMoney;
                    // $("#maxmoney").attr('href',"javascript:$.usepromotion("+m.toFixed(2)+")");
                    // }else{
                    // $("#maxmoney").attr('href',"javascript:$.usepromotion("+$("#maxmoney").attr('data-maxmoney')+")");
                    // }
                });
            }

            $.hideli();
        }


        //防止 首单优惠 满减优惠 特价优惠 积分抵扣 优惠券不存在时 出现空白
        $.hideli = function() {
            if( $("li#fee-content div.item-title-row").length > 0 )
            {
                $("li#fee-content").removeClass("none");
            }
            else
            {
                $("li#fee-content").addClass("none");
            }
        }
        $.hideli();
    </script>
@stop