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
						<span class="ml15 fl">黄页管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
                            <yz:fitem name="mobile" label="联系方式"></yz:fitem>
                            <yz:fitem name="name" label="名称"></yz:fitem>
                            <yz:fitem name="status" label="状态">
                                <yz:radio name="status" options="0,1" texts="停用,启用" checked="$data['status']" default="1"></yz:radio>
                            </yz:fitem>
						</yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop 