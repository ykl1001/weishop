@extends('wap.community._layouts.base')

@section('show_top')
<header class="bar bar-nav y-barnav">
    <a class="button button-link button-nav pull-left pageloading" href='{{u("Integral/index")}}' data-transition='slide-out'>
        <span class="icon iconfont">&#xe600;</span>返回
    </a>
    <h1 class="title f16">确认订单</h1>
</header>
@stop

@section('content')
<?php  
        $typename = $data[0]['type'] == 1 ? '配送方式' : '服务时间设置';
        $orderType = $data[0]['type'];

        $putoff = 1;

        //配送方式
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

        $sellerWayArr = explode(',', $goods['seller']['sendWay']); //配送方式:1商家配送,2到店消费,3到店自提

        foreach ($sellerWayArr as $key => $value) {
            $sendwayArr[$value] = $sendway[$value];
        }

        $goods['seller']['deliveryFee'] = $goods['isVirtual'] == 1 ? $goods['seller']['deliveryFee'] : 0;
?>
<!-- new -->
<div class="bar bar-footer">
    <span class="f14 c-gray y-lineh">应付款:<span class="c-red f18" id="pay-money">￥{{ $goods['seller']['deliveryFee'] }}</span></span>
    <a class="x-menuok c-bg c-white f16 fr" id="x-fwcansels">@if($goods['seller']['deliveryFee'] > 0)去支付@else确认下单@endif</a>
</div>
<div class="content">
        @if(!empty($address))
            <div class="card y-card active mt0" onclick="$.href('{{ u('UserCenter/address',['goodsId' => $goods['id']]) }}')">
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
            </div>
        @else
            <div class="c-bgfff pt15 pb15 pl10 pr10 mb10" onclick="$.href('{!! u('UserCenter/address',['cartIds' => $cartIds]) !!}')">
                <div class="f12">
                    <span>添加地址</span>
                    <i class="icon iconfont fr c-gray">&#xe602;</i>
                </div>
            </div>
        @endif


        <!-- 横排显示 -->
        <div class="list-block media-list mt10 y-qrdd">
            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">
                             <span><img src="{{formatImage($goods['image'],100,100)}}"></span>
                        </div>
                        <div class="item-after c-gray f12 mt10">共<span class="c-red">1</span>件</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
        </div>

    @if($goods['seller']['deliveryFee'] > 0)
        <ul class="y-paylst mb10">
            <li class="on" data-code="1">
                <div class="y-payf f14 ml0">在线支付</div>
                <i class="icon iconfont">&#xe612;</i>
            </li>
        </ul>
    @endif
        @if($goods['isVirtual'] == 1)
    <div class="list-block media-list y-iteminnerp">
        <ul>
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

            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title sendwayStr">配送时间</div>
                    <div class="item-after y-dytime"><span class="c-red" id="beginTime">请选择时间</span><i class="icon iconfont c-gray4">&#xe602;</i></div>
                    <input type="hidden" id="appTime" value="">
                  </div>
                </div>
              </a>
            </li>
        </ul>
    </div>
        @endif

    <div class="list-block media-list y-iteminnerp" id="promotion-integral">
        <ul>
            <li>
              <a href="#" class="item-link item-content">
                <div class="item-inner">
                  <div class="item-title-row f14">
                    <div class="item-title">可用积分</div>
                    <div class="item-after"><span class="c-red">{{ $userinfo['integral'] }}分</span></div>
                  </div>
                </div>
              </a>
            </li>
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

            <ul>
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">商品积分</div>
                        <div class="item-after c-black">{{ $goods['exchangeIntegral'] }}分</div>
                      </div>
                    </div>
                  </a>
                </li>
				@if($goods['isVirtual'] == 1)
                <li>
                  <a href="#" class="item-link item-content">
                    <div class="item-inner">
                      <div class="item-title-row f14">
                        <div class="item-title">运&nbsp;费</div>
                        <div class="item-after c-black">￥{{ $goods['seller']['deliveryFee'] }}</div>
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
                        <div class="item-after c-red" id="total-money">￥{{ $goods['seller']['deliveryFee'] }}</div>
                      </div>
                    </div>
                  </a>
                </li>
            </ul>
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
                        <a href="#tab1_{{$key}}" class="tab-link button @if($key == 0) active @endif timestampDay" data-day="{{$value['time']}}">{{$value['dayName']}}</a>
                    @endforeach
                </div>
            </div>
            <div class="tabs c-bgfff fl">
                @foreach($sellerAllowTime as $key => $value)
                <div id="tab1_{{$key}}" class="tab p-tab @if($key == 0) active @endif">
                    <div class="list-block x-sortlst f14">
                        <ul>
                            @if($value['time'] == Time::toDate(UTC_TIME, 'Y-m-d'))
                                <li class="item-content timestampTime isNow active @if(in_array(key($sendwayArr),[2,3])) none @endif" data-time="0">
                                    <div class="item-inner">
                                        <div class="item-title">立即送出</div>
                                        <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                    </div>
                                </li>
                            @endif
                            @foreach($value['list'] as $k => $v)
                                <!-- 排除已过去的时间 -->
                                @if($value['timestamp'][$k] > UTC_TIME)
                                    <!-- 向后延迟2个时间点 -->
                                    @if($putoff > 2 )
                                        <li class="item-content timestampTime" data-time="{{$v}}">
                                            <div class="item-inner">
                                                <div class="item-title">{{$v}}</div>
                                                <i class="icon iconfont c-yellow2 f20">&#xe610;</i>
                                            </div>
                                        </li>
                                    @endif
                                    <?php $putoff++; ?>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
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
    var payment = "1";
    $(document).on("click","#x-fwcansels",function(){
        var addressId     = $("input[name=addressId]").val();
        var freType       = $(".sendway").text();  //配送方式文字
        var appTime       = $("#appTime").val();
        var buyRemark     = $("input[name=buyRemark]").val();
        var id            = "{{ $goods['id'] }}";
        var sendWay       = $("#sendWay").val();  //配送方式编号
        var obj ={
            addressId: addressId,
            freType:freType,
            appTime:appTime,
            buyRemark:buyRemark,
            goodsId:id,
            payment:payment,
            sendWay:sendWay
        };

        @if($goods['isVirtual'] == 1)
            if(appTime == '') {
                $.alert('请选择预约时间');
                return;
            }
        @endif

        $.showPreloader('正在创建订单...');
        $.post("{{ u('Order/tointegralorder') }}",obj,function(res){
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
                    @if(in_array($key, [2,3]))
                        $(".isNow").addClass("none");
                        $("#beginTime").text('请选择时间');
                        $("#appTime").val('');
                    @else
                        $('.isNow').removeClass("none");
                    @endif
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
</script>
@stop