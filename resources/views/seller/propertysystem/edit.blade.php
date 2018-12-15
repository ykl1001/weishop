@extends('seller._layouts.base')

<?php
$type = [
        ['key'=>'1','name'=>'联系物业'],
        ['key'=>'2','name'=>'手机开门'],
        //['key'=>'3','name'=>'访客通行'],
        ['key'=>'4','name'=>'公共报修'],
        //['key'=>'5','name'=>'投诉举报'],
        ['key'=>'6','name'=>'物业账单'],
        ['key'=>'7','name'=>'生活缴费']
];
$data['type'] = isset($data['type']) ? $data['type'] : 1;
?>

@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">物业配置管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser pt20">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
                            <yz:fitem name="name" label="名称"></yz:fitem>
                            <yz:fitem name="type" label="链接类型">
                                <yz:select name="type" css="type" options="$type" textfield="name" valuefield="key" selected="$data['type']"></yz:select>
                            </yz:fitem>
                            <div id="sort-form-item" class="u-fitem clearfix ">
                                <span class="f-tt">
                                     排序:
                                </span>
                                <div class="f-boxr">
                                    <input type="text" name="sort" id="sort" class="u-ipttext my" style="width:80px;" value="{{$data['sort']}}">
                                </div>
                            </div>
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