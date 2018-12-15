@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<search url="{{ $searchUrl }}">
				<row>
                    <item name="userName" label="会员电话"></item>
					<item label="类型">
						<yz:select name="type" options="1,2" texts="会员邀请,商家邀请" selected="$search_args['type']"></yz:select> 
					</item>
                    <item name="invitationName" label="推荐人电话"></item>
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<thead>
	        		<tr> 
	        		  <td rowspan="2">会员名</td>
	        		  <td rowspan="2" style="width: 85px;">累计返现佣金</td>
	        		  <td rowspan="2" style="width: 115px;">累计返现订单金额</td>
	        		  <td colspan="3">推荐人</td>  
	        		  <td rowspan="2">查看</td>
	        		</tr>
	        		<tr>   
	        		  <td>一级</td>
	        		  <td>二级</td>
	                  <td>三级</td> 
	        		</tr>
	    		</thead>
	    		
	    		<tbody>  
	    		  	@foreach ($lists as $l)
	    		  	<tr>
	    		      <td>{{$l['name']}}</td>
	        		  <td>{{number_format($l['totalReturnFee'], 2)}}</td>
	        		  <td>{{number_format($l['totalSellFee'], 2)}}</td>
	        		  <td>{{$l['invitationName1']}}</td> 
	        		  <td>{{$l['invitationName2']}}</td> 
	        		  <td>{{$l['invitationName3']}}</td> 
	        		  <td style="cursor: pointer;"><a href="{{ u('InvitationUser/invitationList', ['invitationId'=>$l['id']]) }}" target="_new">查看</a></td>
	    		 	</tr>
	    		  	@endforeach
	    		</tbody> 
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
