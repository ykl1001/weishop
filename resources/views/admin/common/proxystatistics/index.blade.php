@extends('admin._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
        <yz:list>
            <search url="{{ $url }}" method="GET">
                <row>
                    <item label="统计年份">
                    <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select> 
                    </item> 
                    <item label="月份">
                    <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select> 
                    </item> 
                    <div id="staffname-form-item" class="u-fitem clearfix">
                        <span class="f-tt">代理账户:</span>
                        <div class="f-boxr">
                              <input type="text" name="name" id="name" class="u-ipttext" value="{{$args['name']}}" />
                        </div>
                    </div> 
                    <btn type="search"></btn>
                </row>
            </search>
            <btns>
                <linkbtn label="导出到EXCEL" type="export">
                    <attrs>
                        <url>{{ u('ProxyStatistics/export', Input::all() ) }}</url>
                    </attrs>
                </linkbtn>
            </btns>
            <table>
                <thead>
                    <tr>
                      <td rowspan="2" >代理名</td> 
                      <td rowspan="2" style="width: 65px;">本月营业额</td>
                      <td rowspan="2" style="width: 65px;">有效订单数</td>
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
                    <!--tr>
                      <td>合计</td>
                      <td>{{number_format($sum['totalPayfee'], 2)}}</td>
                      <td>{{ $sum['totalNum'] }}</td>
                      <td>{{number_format($sum['totalOnline'], 2)}}</td>
                      <td>{{number_format($sum['totalCash'], 2)}}</td>
                          <td>{{number_format($sum['totalIntegralFee'], 2)}}</td>
                      <td>{{number_format($sum['totalDiscountFee'], 2)}}</td>
                      <td>{{number_format($sum['totalDrawnfee'], 2)}}</td>
                      <td>{{ number_format($sum['totalPayfee']/$sum['totalNum'], 2) }}</td>
                      <td></td>
                    </tr -->   
                    @foreach ($lists as $l)
                    <tr>
                      <td>{{$l['name'].'('.$l['level'].'级代理)'}}</td> 
                      <td>{{number_format($l['totalPayfee'], 2)}}</td>
                      <td>{{$l['totalNum']}}</td>
                      <td>{{number_format($l['totalOnline'], 2)}}</td>
                      <td>{{number_format($l['totalCash'], 2)}}</td>
                        <!-- <td>{{number_format($l['totalIntegralFee'], 2)}}</td>
                        <td>{{number_format($l['totalDiscountFee'], 2)}}</td> -->
                      <td>{{number_format($l['activityNewMoney']+$l['systemFullSubsidy']+$l['totalDiscountFee']+$l['totalIntegralFee'], 2) }}</td>
                      <td>{{number_format($l['totalDrawnfee'], 2)}}</td>
                      <td>{{number_format($l['sellerFullSubsidy']+$l['activityGoodsMoney'], 2)}}</td>
                      <td>{{number_format($l['totalPayfee']/$l['totalNum'], 2)}}</td>  
                      <td style="cursor: pointer;"><a href="{{ u('ProxyStatistics/sellerLists', ['proxyId'=>$l['id'], 'month'=>$args['month'],'year'=>$args['year']]) }}" class=" blu agree" >详情</a></td>
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