@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	.ts1{color: #ccc;margin-left: 5px;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save">
			<yz:fitem name="name" label="名称" attr="maxlength='20'"></yz:fitem>
			<yz:fitem name="icon" label="图标" type="image" append="1">
				<div><small class='cred pl10 gray'>建议尺寸：512px*512px，支持JPG/PNG格式</small></div>
			</yz:fitem>
			<yz:fitem label="排序">
				<input type="text" name="sort" class="u-ipttext" defalut="100" value="{{ $data['sort'] }}"onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">
			</yz:fitem>
		</yz:form>
	@yizan_end
@stop
