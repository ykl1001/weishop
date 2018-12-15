@extends('proxy._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
        <yz:list>
            <search url="{{ $url }}">
                <row>
                    <item label="统计年份">
                    <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select> 
                    </item> 
                    <item label="月份">
                    <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select> 
                    </item>  
                    <btn type="search"></btn>
                </row>
            </search>
            <btns>
                <linkbtn label="导出到EXCEL" type="export">
                    <attrs>
                        <url>{{ u('PlatformStatistics/export', Input::all() ) }}</url>
                    </attrs>
                </linkbtn>
            </btns>
            <table pager="no">
                <thead> 
                    <tr>   
                      <td>日期</td>
                      <td>营业额</td>
                      <td width="80px;">有效订单数量</td>
                      <td width="90px;">退款/取消订单</td>
                      <td>在线支付</td>
                      <td>现金支付</td>   
                      <td>积分抵扣</td>   
                      <td>优惠金额</td>
                      <td>平台满减</td>
                      <td>首单减</td>
                      <td>商家补贴</td> 
                    </tr>
                </thead>
                
                <tbody> 
                    <tr>
                      <td>汇总</td> 
                      <td>{{number_format($sum['totalPayfee'], 2)}}</td>
                      <td>{{ $sum['totalNum'] }}</td>
                      <td>{{ $sum['totalCancleNum'] }}</td>
                      <td>{{number_format($sum['totalOnline'], 2)}}</td>
                      <td>{{number_format($sum['totalCash'], 2)}}</td>  
                      <td>{{number_format($sum['totalIntegralFee'], 2)}}</td>  
                      <td>{{number_format($sum['totalDiscountFee'], 2)}}</td>
                      <td>{{number_format($sum['systemFullSubsidy'], 2)}}</td>
                      <td>{{number_format($sum['activityNewMoney'], 2)}}</td>
                      <td>{{number_format($sum['sellerFullSubsidy']+$sum['activityGoodsMoney'], 2)}}</td>
                    </tr>   
                    @foreach ($lists as $list_item)
                    <tr>
                      <td>{{$list_item['daytime']}}</td> 
                      <td>{{number_format($list_item['totalPayfee'], 2)}}</td>
                      <td>{{ $list_item['totalNum'] }}</td>
                      <td>{{ $list_item['totalCancleNum'] }}</td>
                      <td>{{number_format($list_item['totalOnline'], 2)}}</td>
                      <td>{{number_format($list_item['totalCash'], 2)}}</td> 
                      <td>{{number_format($list_item['totalIntegralFee'], 2)}}</td> 
                      <td>{{number_format($list_item['totalDiscountFee'], 2)}}</td>
                      <td>{{number_format($list_item['systemFullSubsidy'], 2)}}</td>
                      <td>{{number_format($list_item['activityNewMoney'], 2)}}</td>
                      <td>{{number_format($list_item['sellerFullSubsidy']+$list_item['activityGoodsMoney'], 2)}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </yz:list> 
    @yizan_end
@stop
@section('js') 
<script type="text/javascript">  
    var mh = {{$args['month']}};
    $(function(){
        $(".year_choose").change(function(){ 
            var year = $(this).val();
            var date = new Date();
            var cumonth = date.getMonth();
            var cuyear = date.getFullYear();
            var html = '<option value="-99">请选择</option>';
            if(year == cuyear){ 
                for (var i = 1; i <= cumonth+1; i++) {
                  if(i == mh){ 
                    html += '<option value="'+i+'" selected>'+i+'月</option>';
                  } else {
                    html += '<option value="'+i+'">'+i+'月</option>';
                  }
                }
            } else if(year < cuyear && year > 0){
                for (var i = 1; i <= 12; i++) {
                  if(i == mh){ 
                    html += '<option value="'+i+'" selected>'+i+'月</option>';
                  } else {
                    html += '<option value="'+i+'">'+i+'月</option>';
                  }
                }
            } 
            $(".month_choose").html(html); 
        }).trigger("change");
    })
</script>  
@stop