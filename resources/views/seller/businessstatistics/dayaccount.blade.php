@extends('seller._layouts.base')
@section('css')
<style type="text/css">  
    .btn-gray{margin-left: 10px; margin-bottom: 10px;}
</style>
@stop
@section('content') 
<div class="ma">
    <div class="" style="margin-top:0px;">
        <div class="x-bbmain">
            <div class="x-pjgltt">单日明细</div>
            @yizan_begin
                <yz:list> 
                    <search method="get">
                        <div class="search-row clearfix"> 
                            <div class="u-fitem clearfix" > 
                                <span >账单日期：{{$args['day']}}</span>
                            </div> 
                            <div class="u-fitem clearfix" > 
                                <span >有效订单数：{{$sum['totalNum']}}</span>
                            </div> 
                            <div class="u-fitem clearfix" > 
                                <span >已入账总额：<b style="color: red">￥{{number_format($sum['totalSellerFee'], 2)}}</b></span>
                            </div>  
                        </div>
                        <row>
                            <item name="sn" label="订单号"></item>
                            <item label="状态">
                                <yz:select name="status" options="0,1,2,3" texts="全部,已完成,已取消,未完成" selected="$args['status']"></yz:select> 
                            </item>
                            <input type="hidden" name="day" value="{{$args['day']}}" />
                            <btn type="search" css="btn-gray"></btn>
                        </row>
                    </search>
                    <btns>
                        <linkbtn label="导出到EXCEL" type="export">
                            <attrs>
                                <url>{{ u('BusinessStatistics/dayaccountexport', Input::all() ) }}</url>
                            </attrs>
                        </linkbtn>
                    </btns>
                    <table>
                        <thead>
                            <tr>
                              <td rowspan="2" width="180">订单号</td> 
                              <td colspan="3">收入</td> 
                              <td colspan="3">支出</td>
                              <td rowspan="2">入账金额</td> 
                              <td rowspan="2">状态</td>
                            </tr>
                            <tr>   
                              <td>在线支付</td>
                              <td>现金支付</td>
                              <td>平台补贴</td>
                              <td>佣金</td>
                              <td>商家补贴</td>
                              <td>配送服务费</td>
                            </tr>
                        </thead>
                        
                        <tbody>  
                            @foreach ($lists as $list_item) 
                            <tr>
                              <td>@if($list_item['orderType'] == 1)<a href="{{u('Order/detail', ['orderId'=>$list_item['id']])}}">{{$list_item['sn']}}@else<a href="{{u('ServiceOrder/detail', ['orderId'=>$list_item['id']])}}">{{$list_item['sn']}}@endif</a></td>
                              <td>@if(!$list_item['isCashOnDelivery']){{$list_item['payFee']}}@else 0 @endif</td>
                              <td>@if($list_item['isCashOnDelivery']){{$list_item['payFee']}}@else 0 @endif</td>
                              <!-- <td>{{$list_item['discountFee'] > $list_item['totalFee']  ? $list_item['totalFee'] : $list_item['discountFee']}}</td> -->
                              <td>{{ number_format($list_item['activityNewMoney']+$list_item['systemFullSubsidy']+$list_item['discountFee']+$list_item['integralFee'], 2) }}</td>
                              <td>{{$list_item['drawnFee']}}</td>
                              <td>{{ number_format($list_item['sellerFullSubsidy']+$list_item['activityGoodsMoney'], 2) }}</td>
                              <td>{{$list_item['sendFee']}}</td>
                              <td>@if($list_item['isCashOnDelivery'])-{{$list_item['drawnFee']}}@else{{$list_item['sellerFee']-$list_item['sendFee']}}@endif</td>
                              <td>{{$list_item['orderStatus']}}</td> 
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </yz:list>
            @yizan_end
        </div>
    </div>
</div>
@stop
@section('js')
<script type="text/javascript">
    $(function(){ 
    });
</script>
@stop

   
