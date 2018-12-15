@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav y-barnav">
    <a class="button button-link button-nav pull-left" onclick="$.href('{{u('GoodsCart/index')}}')"  data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <h1 class="title f16">确认订单</h1>
</header>
@stop
@section('css')
@stop
@section('content')
<?php  
        // dd($data);
        $typename = $data[0]['type'] == 1 ? '配送方式' : '服务时间设置';
        $orderType = $data[0]['type'];

        $tab1 = $tab2 = true;

        $putoff = 1;

        //配送方式
        if($data[0]['sellerId'] != ONESELF_SELLER_ID){
            if($orderType == 1)
            {
                $sendway = [
                    1 => '商家配送',
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
?>
<!-- new -->
<div class="bar bar-footer">
    <span class="f14 c-gray y-lineh">应付款:<span class="c-red f18" id="pay-money">￥{{ $fee['payFee'] }}</span></span>
    <a class="x-menuok c-bg c-white f16 fr" id="x-fwcansels">@if($fee['payFee'] > 0)提交订单@else确认下单@endif</a>
</div>
<div class="content">
    @if(!empty($address))
        @if($address['id'] > 0)
        <div class="card y-card active mt0">
            <php>
                $name = mb_substr($address['name'], 0, 5, "utf-8") . (mb_strlen($address['name'], 'UTF8') > 5 ? "……" : "");
            </php>
            <div class="card-content">
                <div class="fl ml10 mt15">送至：</div>
                <div class="card-content-inner y-gwcaddr">
                    <p><span class="mr10">{{ $name }}</span><span>{{ $address['mobile'] }}</span></p>
                    <p class="mt5">{{ $address['address'] }}</p>
                </div>
            </div>
            <input type="hidden" name="addressId" id="addressId" value="{{ $address['id'] }}">
            <input type="hidden" name="mobile" id="mobile" value="{{ $address['mobile'] }}">
        </div>
        @else
        <div class="list-block y-xqrddaddr">
            <ul>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label f14">收货地址：</div>
                            <div class="item-input f14">
                                {{$address['address']}}
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
    @else
        <div class="c-bgfff pt15 pb15 pl10 pr10 mb10" onclick="$.href('{!! u('UserCenter/address',['cartIds' => $cartIds]) !!}')">
            <div class="f12">
                <span>添加地址</span>
                <i class="icon iconfont fr c-gray">&#xe602;</i>
            </div>
        </div>
    @endif

    
    <!-- @if($orderType == 1) -->
        <!-- 横排显示 -->
        <!-- <div class="list-block media-list mt10 y-qrdd">
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">
                            @foreach($data as $val)
                                <?php $seller = $val['seller']; 
                                  if(!empty($val['norms'])){
                                    $price += $val['norms']['price'] * $val['num'];
                                  }else{
                                    $price += $val['price'] * $val['num'];
                                  }
                                  $num += $val['num'];
                                  //$id += $val['id'].",";
                                ?>
                                <span><img src="{{formatImage($val['goods']['logo'],100,100)}}"></span>
                            @endforeach
                        </div>
                        <div class="item-after c-gray f12 mt10">共<span class="c-red">{{ $num }}</span>件</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        </div> -->
    <!-- @else -->
        <!-- 竖排显示 -->
       <!--  <div class="list-block media-list y-sylist">
            <ul>
                @foreach($data as $val)
                <?php 
                    if($seller == ''){
                        $seller = $val['seller'];
                    }
                ?>
                <li>
                    <?php                    
                      $price += $val['price'] * $val['num'];
                    ?>
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="{{formatImage($val['goods']['logo'],100,100)}}" width="55"></div>
                        <div class="item-inner">
                            <div class="item-subtitle y-sytitle mt10">{{ $val['goods']['name'] }}<span class="f12">X{{ $val['num'] }}</span></div>
                            <div class="item-subtitle mb10 mt10 y-f14">
                                <span class="c-red">￥{{ $val['price'] }}</span>
                            </div>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div> -->
    <!-- @endif -->

    <div class="list-block media-list y-iteminnerp">
        <ul>
            @if($orderType == 1)
            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title">配送方式</div>
                    <!-- 默认第一个元素值 -->
                    <div class="item-after create-actions"><span class="sendway">{{current($sendwayArr)}}</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                    <!-- 默认第一个元素key -->
                    <input type="hidden" id="sendWay" value="{{key($sendwayArr)}}">
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
                                <div class="item-after create-actions"><span class="sendway">{{current($sendwayArr)}}</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                                <!-- 默认第一个元素key -->
                                <input type="hidden" id="sendWay" value="{{key($sendwayArr)}}">
                            </div>
                        </div>
                    </a>
                </li>
            @endif
            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title sendwayStr">@if($orderType == 1){{$sendwayStr[key($sendwayArr)]}}@else服务时间@endif</div>
                    <div class="item-after y-dytime"><span class="c-red" id="beginTime">请选择时间</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                    <input type="hidden" id="appTime" value="">
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
        @if($fee['isCashOnDelivery'] == 1)
        <li data-code="0">
            <div class="y-payf f14 ml0">货到付款</div>
            <i class="icon iconfont">&#xe612;</i>
        </li>
        @endif
    </ul>

    <div class="list-block media-list y-iteminnerp" id="promotion-integral">
        <ul>
            @if($fee['isShowPromotion'] == 1)
                <li>
                  <a href="{{ u('Coupon/usepromotion',['cartIds' => $cartIds,'addressId'=>(int)Input::get('addressId'),'sellerId'=>$fee['sellerId'],'money'=>$fee['totalMoney']]) }}" class="item-link item-content pageloading">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">优&nbsp;惠&nbsp;券</div>
                        <div>
                            @if($fee['discountFee'] > 0)
                                <!-- <div class="item-after c-red fl">-{{ $fee['discountFee'] }}</div> -->
                                <div class="item-after c-black fl">已选择优惠券</div>
                            @elseif($fee['promotionCount'] > 0)
                                <div class="item-after c-black fl">可选择优惠券</div>
                            @else
                                <div class="item-after c-black fl">无可用优惠券</div>
                            @endif
                            <i class="icon iconfont">&#xe602;</i>
                        </div>
                      </div>
                    </div>
                  </a>
                </li>
            @endif
            @if($fee_off)
            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title">积分抵扣</div>
                    <div class="item-after">
                        @if($fee['integral'] > 0)
                        <span class="c-red integral">可用{{ $fee['integral'] }}积分抵用{{ $fee['cashMoney'] }}元</span>
                        <i class="icon iconfont ml10 c-red y-redcircle active y-roll">&#xe612;</i>
                        @else
                        <span class="c-gray">无可用积分</span>
                        <i class="icon iconfont ml10 c-red y-redcircle">&#xe612;</i>
                        @endif
                    </div>
                  </div>
                </div>
              </a>
            </li>
            @endif
        </ul>
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
    </div>

    <div class="list-block media-list y-iteminnerp">
        @if($orderType == 1)
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">商品价格</div>
                        <div class="item-after c-black">￥{{ $fee['goodsFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                <li id="freight" class="@if( in_array(key($sendwayArr), [2,3]) ) none @endif">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">运&nbsp;费</div>
                        <!-- <div class="item-after c-black">@if(empty($fee['freightMsg'])) ￥{{ $fee['freight'] }} @else {{$fee['freightMsg']}} @endif</div> -->
                        <div class="item-after c-black">￥{{ $fee['freight'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @if($fee['firstOrderCutMoney'] > 0)
                <li id="firstOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">首单优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['firstOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['fullOrderCutMoney'] > 0)
                <li id="fullOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">满减优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['fullOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['specialOrderCutMoney'] > 0)
                <li id="specialOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">特价优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['specialOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['discountFee'] > 0)
                <li id="discountFee">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">优惠券</div>
                        <div class="item-after c-red" id="discount-fee">-￥{{ $fee['discountFee'] or '0.00'}}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['cashMoney'] > 0)
                <li id="cashMoney">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">积分抵扣</div>
                        <div class="item-after c-red" id="cash-money">-￥{{ $fee['cashMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">合&nbsp;计</div>
                        <div class="item-after c-red" id="total-money">￥{{ $fee['totalFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        @else
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">服务费用</div>
                        <div class="item-after c-black">￥{{ $fee['goodsFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @if($fee['firstOrderCutMoney'] > 0)
                <li id="firstOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">首单优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['firstOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['fullOrderCutMoney'] > 0)
                <li id="fullOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">满减优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['fullOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['specialOrderCutMoney'] > 0)
                <li id="specialOrderCutMoney" class="">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">特价优惠</div>
                        <div class="item-after c-red">-￥{{ $fee['specialOrderCutMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['discountFee'] > 0)
                <li id="discountFee">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">优惠券</div>
                        <div class="item-after c-red" id="discount-fee">-￥{{ $fee['discountFee'] or '0.00'}}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                @if($fee['cashMoney'] > 0)
                <li id="cashMoney">
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">积分抵扣</div>
                        <div class="item-after c-red" id="cash-money">-￥{{ $fee['cashMoney'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
                @endif
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">合&nbsp;计</div>
                        <div class="item-after c-red" id="total-money">￥{{ $fee['totalFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        @endif
        
        <div class="list-block media-list y-drddlist">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-title bold">{{$data[0]['seller']['name']}}</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            @foreach($data as $val)
                                <div class="item-title-row f14">
                                    <div class="item-title">{{$val['goods']['name']}}</div>
                                    <div class="item-after c-black"><span class="c-gray">x{{$val['num']}}</span><span class="pl20">￥{{$val['price']}}</span></div>
                                </div>
                            @endforeach
                        </div>
                    </a>
                </li>

                @if($orderType == 1)
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-title-row f14">
                                    <div class="item-title">运&nbsp;费</div>
                                    <div class="item-after c-black"><span>￥30</span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-title-row f14">
                                    <div class="item-title">积分抵现</div>
                                    <div class="item-after c-black"><span>-￥25</span></div>
                                </div>
                                <div class="item-title-row f14">
                                    <div class="item-title">满减优惠</div>
                                    <div class="item-after c-black"><span>-￥2</span></div>
                                </div>
                                <div class="item-title-row f14">
                                    <div class="item-title">特价优惠</div>
                                    <div class="item-after c-black"><span>-￥2</span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-title-row f14">
                                    <div class="item-title">配送费</div>
                                    <div class="item-after c-black"><span>￥30</span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="item-link item-content">
                            <div class="item-inner">
                                <div class="item-title-row f14">
                                    <div class="item-title">积分抵现</div>
                                    <div class="item-after c-black"><span>-￥25</span></div>
                                </div>
                                <div class="item-title-row f14">
                                    <div class="item-title">满减优惠</div>
                                    <div class="item-after c-black"><span>-￥2</span></div>
                                </div>
                                <div class="item-title-row f14">
                                    <div class="item-title">特价优惠</div>
                                    <div class="item-after c-black"><span>-￥2</span></div>
                                </div>
                            </div>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-title-row f12">
                                <div class="item-title c-gray">原价<span class="c-black">￥162</span> - 共优惠<span class="c-black">￥31</span></div>
                                <div class="item-after c-black">总计
                                :<span>￥30</span></div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        <div class="content-block-title f14 c-gray">
            <i class="icon iconfont vat">&#xe646;</i>
            请在下单后{{ $time }}分钟内完成支付。
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
            <div class="tabs c-bgfff fl">
                @foreach($sellerAllowTime as $key => $value)
                    @if(count($value['list']) > 0)
                        <div id="tab1_{{$key}}" class="tab p-tab @if($tab2) active @endif">
                            <div class="list-block x-sortlst f14">
                                <ul>
                                    @foreach($value['list'] as $k => $v)
                                        @if($value['time'] == Time::toDate(UTC_TIME, 'Y-m-d'))
                                                @if( $putoff == 1 )
                                                    <!-- 当天，有立即送出 -->
                                                    <li class="item-content timestampTime isNow active @if(in_array(key($sendwayArr),[2,3])) none @endif" data-time="0">
                                                        <div class="item-inner">
                                                            <div class="item-title">立即送出<p><small>（大约{{$v}}到）</small></p></div>
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
@stop

@section($js)
<script type="text/javascript">
    var timeName = $(".sendwayStr").text();
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
    $(document).on("touchend",".y-paylst li",function(){
        $(this).addClass("on").siblings().removeClass("on"); 
        payment = $(this).data("code");
        $.orderCompute();
        // var payMoney = Number("{{ $fee['payFee'] }}");
        // var totalMoney = Number("{{ $fee['totalFee'] }}");
        // var cashMoney = Number("{{ $fee['cashMoney'] }}");

        // var firstOrderCutMoney = Number("{{ $fee['firstOrderCutMoney'] }}");
        // var fullOrderCutMoney = Number("{{ $fee['fullOrderCutMoney'] }}");
        // var specialOrderCutMoney = Number("{{ $fee['specialOrderCutMoney'] }}");

        // var discountFee = Number("{{ $fee['discountFee'] }}");

        // if(payment == 0) {

        //     $("#total-money").html("￥" + (totalMoney + cashMoney + firstOrderCutMoney + fullOrderCutMoney + specialOrderCutMoney).toFixed(2));
        //     $("#pay-money").html("￥" + (payMoney + cashMoney + firstOrderCutMoney + fullOrderCutMoney + specialOrderCutMoney).toFixed(2));

        //     $("#cash-money").html("-￥0");
        //     $("#discount-fee").html("-￥0");
        //     $("#promotion-integral,#cashMoney").hide();
        //     $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney").hide();  //不显示首单满减特价
        //     $("#x-fwcansels").html("确认下单");
        // }else{
        //     if(isUseIntegral == "1"){
        //         $("#total-money").html("￥" + totalMoney);
        //         $("#pay-money").html("￥" + payMoney);
        //         $("#cash-money").html("-￥" + cashMoney);
        //     }else{
        //         $("#total-money").html("￥" + (totalMoney + cashMoney).toFixed(2));
        //         $("#pay-money").html("￥" + (payMoney + cashMoney).toFixed(2));
        //         $("#cash-money").html("-￥0");
        //     }
        //     $("#discount-fee").html("-￥" + discountFee);
        //     $("#promotion-integral,#cashMoney").show();
        //     $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney").show();  //显示首单满减特价
        //     $("#x-fwcansels").html("去支付");
        // }
    });
    $(".save_address").click(function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            isSaveAddress = 0;
        } else {
            $(this).addClass('active');
            isSaveAddress = 1;
        }
    })
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
        var mapPoint   = $("input[name=mapPointStr]").val();
        var cityId        = $("input[name=cityId]").val();
        var doorplate     = $("input[name=doorplate]").val();
        var name          = $("input[name=name]").val(); 
		var mobile        = $("input[name=mobile]").val();
		var reg = /^1\d{10}$/;
        if(!reg.test(mobile)){
            $.toast("请输入正确的手机号码");
            return false;
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
            isSaveAddress:isSaveAddress
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

    // $(document).on("click", ".y-paylst li", function(){
    //     if($(this).hasClass("on")){
    //         $(this).removeClass("on");
    //     }else{
    //         $(this).addClass("on").siblings().removeClass("on");
    //     }
    // });
    // 

    // $.orderTime = function() {
    //     var time = "{{ yztime($data[0]['serviceTime']) }}";
    //     var type = "{{ $orderType }}";
    //     if(type == 2){
    //         var before  = time.split(' ')[0];
    //         var after   = time.split(' ')[1];
    //         var year    = before.split('-')[0];
    //         var month   = before.split('-')[1];
    //         var day     = before.split('-')[2];
    //         var hours   = after.split(':')[0];
    //         var minutes = after.split(':')[1];
    //         var seconds = after.split(':')[2];
    //         $("#beginTime").datetimePicker({
    //             value: [year, month, day, hours, minutes, seconds]
    //         });
    //     }
    //     else{
    //         $("#beginTime").datetimePicker({
    //             value: ["{{Time::toDate(UTC_TIME+1800,'Y')}}", "{{Time::toDate(UTC_TIME+1800,'m')}}", "{{Time::toDate(UTC_TIME+1800,'d')}}", "{{ intval(Time::toDate(UTC_TIME+1800,'H')) }}", "{{Time::toDate(UTC_TIME+1800,'i')}}"]
    //         });
    //     }
    // }

    // $.orderTime();
    /*
    $(document).on("click", ".y-paylst li", function(){
        if($(this).hasClass("on")){
            $(this).removeClass("on");
        }else{
            $(this).addClass("on").siblings().removeClass("on");///?????????
        }
    });*/
    $(document).on("click", ".y-roll", function(){
        $.orderCompute();
        // var payMoney = Number("{{ $fee['payFee'] }}");
        // var totalMoney = Number("{{ $fee['totalFee'] }}");
        // var cashMoney = Number("{{ $fee['cashMoney'] }}");
        // if($(this).hasClass("active")){
        //     isUseIntegral = 0;
        //     $(this).removeClass("active");
        //     $("#total-money").html("￥" + (totalMoney + cashMoney).toFixed(2));
        //     $("#pay-money").html("￥" + (payMoney + cashMoney).toFixed(2));
        //     $("#cash-money").html("-￥0");
        //     if((payMoney + cashMoney) > 0.001){
        //         $(".y-paylst").css("display", "block");
        //     }
        // }else{
        //     isUseIntegral = 1;
        //     $(this).addClass("active");
        //     $("#total-money").html("￥" + totalMoney);
        //     $("#pay-money").html("￥" + payMoney);
        //     $("#cash-money").html("-￥" + cashMoney);
        //     if(payMoney < 0.001){
        //         $(".y-paylst").css("display", "none");
        //     }
        // }
    });
    $(document).on("click", ".y-bottom .tabs .list-block li", function(){
        $(this).addClass("active").siblings().removeClass("active");

        var day = $('.timestampDay.active').data('day');
        var time = $('.p-tab.active .timestampTime.active').data('time');
        if(time==0){
            $("#beginTime").text('立即送出');
            $("#appTime").val(0);
        }else{
            $("#beginTime").text(day+' '+time);
            $("#appTime").val(day+' '+time);
        }  

        $(".y-bottom .row").parents(".y-time").addClass("none");
    });
    $(document).on("click", ".y-bottom .row", function(){
        $(this).parents(".y-time").addClass("none");
    });
    $(document).on("click", ".y-time .mask", function(){
        $(this).parents(".y-time").addClass("none");
    });
    $(document).on("click", ".y-dytime", function(){
        $(".y-time").removeClass("none");
    });

    /* 配送方式 */
    $(document).off('click','.create-actions');
    $(document).on('click','.create-actions', function () {
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
                        // $("#firstOrderCutMoney").addClass('none'); //不显示首单
                        // $("#fullOrderCutMoney").addClass('none'); //不显示满减
                        // $("#specialOrderCutMoney").addClass('none'); //不显示特价
                    @else
                        $('.isNow').removeClass("none");
                        $('.isNextNow').addClass("none");
                        $("#freight").removeClass('none'); //显示运费
                        // $("#firstOrderCutMoney").removeClass('none'); //显示首单
                        // $("#fullOrderCutMoney").removeClass('none'); //显示满减
                        // $("#specialOrderCutMoney").removeClass('none'); //显示特价
                    @endif

                    $.orderCompute();
                  }
                },
            @endforeach
          ];
          var buttons2 = [
            {
              text: '取消',
              bg: 'danger'
            }
          ];
          var groups = [buttons1, buttons2];
          $.actions(groups);
    });

    /**
     * 计算价格
     */
    $.orderCompute = function() {
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

        //商家配送 + 运费
        if(sendWay == 1)
        {
            goodsFee += freight; 
        }

        //计算新的抵扣积分
        $.post("{{ u('order/recountCashMoney') }}", {'payFee':goodsFee}, function(result){
            $("span.integral").text("可用"+result.integral+"积分抵用"+result.cashMoney+"元");
            cashMoney = Number(result.cashMoney);

            if(payment == 0) 
            {
                //线下支付 不享受优惠
                // $("#cash-money").html("-￥0");
                // $("#discount-fee").html("-￥0");
                $("#promotion-integral,#cashMoney,#discountFee").hide(); //不显示优惠券 积分
                $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney").hide();  //不显示首单满减特价
                $("#x-fwcansels").html("确认下单");
            }
            else
            {
                //线上支付
                if($('.y-roll').hasClass("active")){
                    //不使用积分
                    isUseIntegral = 0;
                    $('.y-roll').removeClass("active");
                    $("#cash-money").html("-￥0");
                    if((payMoney + cashMoney) > 0.001){
                        $(".y-paylst").css("display", "block");
                    }
                }else{
                    //使用积分
                    isUseIntegral = 1;
                    $('.y-roll').addClass("active");
                    goodsFee = goodsFee - cashMoney;
                    $("#cash-money").html("-￥" + cashMoney);
                    if(payMoney < 0.001){
                        $(".y-paylst").css("display", "none");
                    }
                }

                goodsFee = goodsFee - discountFee - firstOrderCutMoney - fullOrderCutMoney - specialOrderCutMoney;
                $("#promotion-integral,#cashMoney,#discountFee").show();  //显示优惠券 积分
                $("#firstOrderCutMoney,#fullOrderCutMoney,#specialOrderCutMoney").show();  //显示首单满减特价
                // $("#x-fwcansels").html("去支付");
            }

            goodsFee = goodsFee >= 0 ? goodsFee : 0;

            $("#total-money,#pay-money").html("￥" + Number(goodsFee.toFixed(2)));
        });
    }
</script>
@stop