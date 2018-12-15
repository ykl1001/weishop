@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.m-spboxlst .f-tt{width: 240px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem label="是否核审分销账户">
				<yz:select name="fx_user_check" options="1,0" texts="是,否" selected="$data['fx_user_check']"></yz:select>
			</yz:fitem>
			<yz:fitem name="fx_exchange_percent" label="佣金兑换比例" append="1">
				<span class="ml10 red">*多少分销佣金兑换1元</span>
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop