@extends('admin._layouts.base')
@section('css')
    <style>
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search>
            <row>
                <item label="统计年份">
                    <yz:select name="year" css="year_choose" options="$orderyear" textfield="yearName" valuefield="yearName"  selected="$args['year']"></yz:select>
                </item>
                <item label="月份">
                    <yz:select name="month" css="month_choose" options="1,2,3,4,5,6,7,8,9,10,11,12" texts="1月,2月,3月,4月,5月,6月,7月,8月,9月,10月,11月,12月" selected="$args['month']"></yz:select>
                </item>
                <btn type="search"></btn>
				<linkbtn label="导出当前页到EXCEL" type="export" url="{{ u('OneselfBusinessStatistics/export',$args) }}"></linkbtn>
            </row>
        </search>
        <table pager="no">
    		<thead>
        		<tr>
        		  <td>日期</td>
        		  <td>营业额</td>
        		  <td>有效订单</td>
        		  <td>优惠券</td>
        		  <td>积分抵扣</td> 
        		</tr>
    		</thead>
    		
    		<tbody> 
        		<tr>
        		  <td>汇总</td>
        		  <td>{{$total['totalMoney']}}（元）</td>
        		  <td>{{$total['totalNum']}}（单）</td>
        		  <td>{{$total['totalPromotion']}}（元）</td>
        		  <td>{{$total['totalIntegral']}}（分）</td>
        		</tr>   
    		  	@foreach ($list as $val)
    		  	<tr>
    		      <td>{{$val['date']}}</td>
        		  <td>{{$val['total']}}</td>
    		      <td>{{$val['num']}}</td>
    		      <td>{{$val['discountFee']}}</td>
        		  <td>{{$val['integral']}}</td>
    		 	</tr>
    		  	@endforeach
    		</tbody>
    	</table>
    </yz:list>
    @yizan_end
@stop
@section('js')
    <script>
    </script>
@stop