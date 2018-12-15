@extends('proxy._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="doRepwd">
			<yz:fitem name="oldPwd" label="原密码" type="password"></yz:fitem>
			<yz:fitem name="newPwd" label="新密码" type="password"></yz:fitem>
			<yz:fitem name="reNewPwd" label="重复新密码" type="password"></yz:fitem>
		</yz:form>
	@yizan_end
@stop

@section('js')
@stop
