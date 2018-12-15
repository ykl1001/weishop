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
	<div id="checkList" class="">
        <div class="u-ssct clearfix">
            <form id="yzForm" class="" name="yzForm" method="post" action="{{ u('InvitationStatistics/index') }}" target="_self">
                <div class="search-row clearfix"> 
                    <div class="u-fitem clearfix"  id="select_year">
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
                    </div>  
                	<button type="submit" class="btn mr5">搜索</button> 
                </div>
            </form>
         </div>
   	</div>
	@yizan_begin
  	<yz:list>  
       	<table pager="no">
	        <thead> 
	            <tr>   
	              	<td>日期</td>
	              	<td>新增会员数</td>
	              	<td>订单销售总额</td>
	              	<td>分佣成本</td>
	              	<td>一级佣金</td>   
	              	<td>二级佣金</td>   
	              	<td>三级佣金</td>   
	            </tr>
	        </thead> 
	        <tbody> 
	            @foreach ($lists as $l)
	            <tr>
	               	<td>{{$l['daytime']}}</td> 
	              	<td>{{$l['newUserNum']}}</td>
	              	<td>{{number_format($l['totalFee'], 2)}}</td> 
	              	<td>{{number_format($l['totalReturnFee'], 2)}}</td>
	              	<td>{{number_format($l['level1Fee'], 2)}}</td>
	              	<td>{{number_format($l['level2Fee'], 2)}}</td>
	              	<td>{{number_format($l['level3Fee'], 2)}}</td> 	
	          	</tr>
	            @endforeach
	            <tr>
	            	<td>合计</td> 
              		<td>{{ (int)$sum['newUserNum'] }}</td>
	            	<td>{{number_format($sum['totalFee'], 2)}}</td>
	             	<td>{{number_format($sum['totalReturnFee'], 2)}}</td>
	              	<td>{{number_format($sum['level1Fee'], 2)}}</td>
	              	<td>{{number_format($sum['level2Fee'], 2)}}</td>
	              	<td>{{number_format($sum['level3Fee'], 2)}}</td>    
	            </tr>   
	        </tbody>
      	</table>
  	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">  
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
