@extends('admin._layouts.base')
@section('css')
	<style>.spl_c_0{color:red;}.spl_c_1{color:green;}</style>
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<table>
			<columns>  
				<column label="SN" align="center" width="160">
					{{ $list_item['sn'] }}
				</column>
				<column label="商家名称" align="left">
					<div>{{ $list_item['seller']['name'] }}</div>
				</column>
				<column label="商家手机" align="" width="150">
					<div>{{ $list_item['seller']['mobile'] }}</div>
				</column>
				<column label="金额"  align="center" width="60">
					{{ $list_item['money'] }}
				</column>
				<column label="描述" align="left" width="150">
					{{ $list_item['content'] }}
				</column>
				<column label="状态"  align="center" width="50" css="spl_c_{{$list_item['status']}}">
					{{ Lang::get('admin.sellerPayType.'.$list_item['status']) }}
				</column>
				<column label="创建时间" align="left" width="120">
					{{  yzTime($list_item['createTime']) }}
				</column> 
				<column label="支付时间" align="left" width="120">
					{{  yzTime($list_item['payTime']) }}
				</column>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
