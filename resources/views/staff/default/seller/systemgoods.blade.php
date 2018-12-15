@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/goods',['type' => 1,'id'=>$data['tradeId'] ]) }}','#seller_goods_view',1)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <ul class="x-ltmore f12 none">
        <li><i class="icon iconfont mr020 vat">&#xe658;</i>新订单</li>
        <li><i class="icon iconfont mr020 vat">&#xe654;</i>订单管理</li>
        <li><i class="icon iconfont mr020 vat">&#xe656;</i>店铺</li>
        <li><i class="icon iconfont mr020 vat">&#xe623;</i>我的</li>
    </ul>
@stop
@section('css')
@stop

@section('content')
    <div class="y-null">
        <div>  <a href="#" onclick="JumpURL('{{u('Seller/getTagLists',['type' => $data['type'],'tradeId'=>$data['tradeId'],'tpl'=>"system" ])}}','#seller_commodity_view',2)" class="f14  y-btn mt15" external="">商品库</a></div>
        <div> <a href="#" onclick="JumpURL('{{u('Seller/addnew',['type' =>$data['type'],'tradeId'=>$data['tradeId'] ])}}','#seller_addnew_view',1)"  class="f14 c-white y-btn mt15" external="">自定义添加</a></div>
    </div>
@stop