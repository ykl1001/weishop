@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
 <div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">创建管理员</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
                            <yz:fitem name="id" type="hidden" val=""></yz:fitem>
                            <yz:fitem name="relation_id" type="hidden" val="{{$seller['id']}}"></yz:fitem>
                            <yz:fitem name="name" label="账号"></yz:fitem>
                            <yz:fitem label="密码">
				                <input type="text" name="pwd" class="u-ipttext">
				                <span class="hscolor">{{$data['ts']}}</span>
			                </yz:fitem>
                            <yz:fitem label="分组">
				                <yz:select name="rid" options="$role" valuefield="id" textfield="name" selected="$data['rid']"></yz:select>
			                 </yz:fitem>
						</yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop
@section('js')
@stop

