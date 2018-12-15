@extends('admin._layouts.base')
@section('css')
<style type="text/css">
        .search-row{position: relative;}
        .search-row .tp{position: absolute;top: 7px;right:0;}
        .search-row .tanchuang{width: 440px;height: auto;border: 1px solid #d5d5d5;display: none;background: #fff;}
        .search-row .tanchuang .tccon{margin: 15px 5px;}
        .search-row .tanchuang .tccon p{margin: 10px;}
        .search-row .tanchuang .tccon .title{font-weight: bold;}
        .search-row .tp:hover .tanchuang{display: block;}
    </style>
@stop
@section('right_content')
    <!-- 搜索 -->
  <div id="checkList" class="">
      <div class="p10 f14">代理账户：{{$proxy['name']}}</div>
        <div class="u-ssct clearfix">
            <form id="yzForm" class="" name="yzForm" method="post" action="{{ u('ProxyStatistics/sellerLists') }}" target="_self">
                <div class="search-row clearfix">
                    <div class="u-fitem clearfix" id="show_year">
                        <span><h2><?php echo date('Y');?>年<?php echo date('m')?>月</h2></span>
                        <span style="cursor: pointer;" id="old_record">点击查看往期数据</span>
                    </div> 
                    <div class="u-fitem clearfix" style="display:none;" id="select_year">
                        <div class="u-fitem clearfix">
                            <select name="year" style="width:auto" class="sle year_choose">
                                <option value="-99">请选择</option>
                                @foreach($orderyear as $year)
                                <option value="{{$year['yearName']}}" @if($year['yearName'] == $args['year']) selected @endif>{{$year['yearName']}}</option>
                                @endforeach
                            </select>&nbsp;<span>年</span>
                        </div>
                        <div class="u-fitem clearfix">
                             <select name="month" style="width:auto" class="sle month_choose ">
                                <option value="-99">请选择</option>
                                <?php for($i=1;$i<=12;$i++){?>
                                <option value="<?php echo $i;?>" @if($i == $args['month']) selected @endif><?php echo $i;?></option>
                                <?php }?>
                            </select>&nbsp;<span>月</span>
                        </div>
                        <input type="hidden" name="proxyId" value="{{$args['proxyId']}}" />
                    </div>  
                  <button type="submit" class="btn mr5">搜索</button>
                    <div class="tp fr tr" style="right: 10px;"><img src="{{asset('images/wenhao.jpg')}}">
                        <div class="tanchuang tl">
                            <div class="tccon">
                                <p class="title">本月营业额</p>
                                <p>有效订单的总金额。公式：营业额=商品销售额+配送收入=在线支付（含补贴）+现金支付-佣金-商家促销-平台促销</p>
                                <p class="title">有效订单数</p>
                                <p>指已确认订单且无退款的订单数</p>
                                <p class="title">在线支付</p>
                                <p>会员通过在线支付的有效订单金额</p>
                                <p class="title">现金支付</p>
                                <p>会员通过线下付现金的方式支付的有效订单金额</p>
                                <p class="title">优惠券</p>
                                <p>指有效订单使用优惠券的金额</p>
                                <p class="title">佣金</p>
                                <p>公式：订单金额（含补贴）*佣金比率</p>
                                <p class="title">商家促销</p>
                                <p>指商家通过促销活动为用户补贴，公式：满减减免金额+特价商品折扣金额</p>
                                <p class="title">平台促销</p>
                                <p>指由平台针对指定商家为用户补贴，公式：首单减免金额+满减减免金额</p>
                                <p class="title">客单价</p>
                                <p>每笔订单的平均金额。公式：单均价=营业额+有效订单数</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
         </div>
   </div>
   
   <!-- 列表 -->
   @yizan_begin
  <yz:list>
      <btns>
          <linkbtn label="导出到EXCEL" type="export">
              <attrs>
                  <url>{{ u('ProxyStatistics/sellerexport', Input::all() ) }}</url>
              </attrs>
          </linkbtn>
      </btns>
       <table>
        <thead>
            <tr>
              <td rowspan="2">商家名</td>
              <td rowspan="2" style="width:65px;">本月营业额</td>
              <td rowspan="2" style="width:65px;">有效订单数</td>
              <td colspan="3">收入</td> 
              <td colspan="2">支出</td>
              <td rowspan="2">客单价</td> 
              <td rowspan="2">查看</td>
            </tr>
            <tr>   
              <td>在线支付</td>
              <td>现金支付</td>
              <!-- <td>积分奖金</td>
              <td>优惠券</td> -->
              <td>平台补贴</td>
              <td>佣金</td> 
              <td>商家补贴</td>  
            </tr>
        </thead>
        
        <tbody> 
            <tr>
              <td>合计</td>
              <td>{{number_format($sum['totalPayfee'], 2)}}</td>
              <td>{{ $sum['totalNum'] }}</td>
              <td>{{number_format($sum['totalOnline'], 2)}}</td>
              <td>{{number_format($sum['totalCash'], 2)}}</td>
              <!-- <td>{{number_format($sum['totalIntegralFee'], 2)}}</td>
              <td>{{number_format($sum['totalDiscountFee'], 2)}}</td> -->
              <td>{{number_format($sum['activityNewMoney']+$sum['systemFullSubsidy']+$sum['totalIntegralFee']+$sum['totalDiscountFee'], 2) }}</td>
              <td>{{number_format($sum['totalDrawnfee'], 2)}}</td>
              <td>{{number_format($sum['sellerFullSubsidy']+$sum['activityGoodsMoney'], 2)}}</td>
              <td>{{ number_format($sum['totalPayfee']/$sum['totalNum'], 2) }}</td>
              <td></td>
            </tr>   
            @foreach ($lists as $l)
            <tr>
              <td>{{$l['name']}}</td>
              <td>{{number_format($l['totalPayfee'], 2)}}</td>
              <td>{{$l['totalNum']}}</td>
              <td>{{number_format($l['totalOnline'], 2)}}</td>
              <td>{{number_format($l['totalCash'], 2)}}</td>
              <!-- <td>{{number_format($l['totalIntegralFee'], 2)}}</td>
              <td>{{number_format($l['totalDiscountFee'], 2)}}</td> -->
              <td>{{number_format($l['activityNewMoney']+$l['systemFullSubsidy']+$l['totalIntegralFee']+$l['totalDiscountFee'], 2) }}</td>
              <td>{{number_format($l['totalDrawnfee'], 2)}}</td>
              <td>{{number_format($l['sellerFullSubsidy']+$l['activityGoodsMoney'], 2)}}</td>
              <td>{{number_format($l['totalPayfee']/$l['totalNum'], 2)}}</td>  
              <td style="cursor: pointer;"><a href="{{ u('ProxyStatistics/monthAccount', ['sellerId'=>$l['id'], 'month'=>$args['month'],'year'=>$args['year']]) }}" class=" blu agree" >对账单</a></td>
          </tr>
            @endforeach
        </tbody>
      </table>
  </yz:list>
  @yizan_end
@stop

@section('js')
<script type="text/javascript"> 
$(document).on("click","#old_record",function(){
    $("#show_year").hide();
    $("#select_year").show();
});
var mh = {{$args['month']}};
$(function(){

    @if( Time::toTime((int)$args['year'] . '-' . (int)$args['month']) != Time::toTime((int)date('Y') . '-' . (int)date('m')) )
        $("#old_record").trigger('click'); 
    @endif
    $(".year_choose").change(function(){ 
        var year = $(this).val();
        var date = new Date();
        var cumonth = date.getMonth();
        var cuyear = date.getFullYear();
        var html = '<option value="-99">请选择</option>';
        if(year == cuyear){ 
            for (var i = 1; i <= cumonth+1; i++) {
                if(i == mh){ 
                    html += '<option value="'+i+'" selected>'+i+'</option>';
                } else {
                    html += '<option value="'+i+'">'+i+'</option>';
                }
            }
        } else if(year < cuyear && year > 0){
            for (var i = 1; i <= 12; i++) {
                if(i == mh){ 
                    html += '<option value="'+i+'" selected>'+i+'</option>';
                } else {
                    html += '<option value="'+i+'">'+i+'</option>';
                }
            }
        } 
        $(".month_choose").html(html); 
    }).trigger("change");
})
</script>
@stop