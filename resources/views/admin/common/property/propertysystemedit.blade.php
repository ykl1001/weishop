@extends('admin._layouts.base')
<?php
$type = [
        ['key'=>'1','name'=>'联系物业'],
        ['key'=>'2','name'=>'手机开门'],
        ['key'=>'3','name'=>'访客通行'],
        ['key'=>'4','name'=>'公共报修'],
        ['key'=>'5','name'=>'投诉举报'],
        ['key'=>'6','name'=>'物业账单'],
        ['key'=>'7','name'=>'生活缴费']
];
$data['type'] = isset($data['type']) ? $data['type'] : 1;
?>
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="propertysystemsave">
        <input type="hidden" name="sellerId" value="{{$seller['id']}}">
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
@stop 