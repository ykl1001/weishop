@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">积分商城</h1>
    </header>
@stop
@section('css')
@stop

@section('content')
    <div class="content">
        <div class="list-block media-list y-sylist">
            <ul>
                <li>
                    <a href="{{u('Integral/detail',['id'=>$data['goodsId']])}}" class="item-link item-content">
                        <div class="item-media"><img src="{{$data['images']}}" width="70"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f14">{{$data['goodsName']}}</div>
                                <div class="item-after mt20"><i class="icon iconfont c-gray2">&#xe602;</i></div>
                            </div>
                            <div class="item-subtitle mt10 f14">
                                <span class="c-red">{{$data['integral']}}积分</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="list-block media-list">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">兑换时间</div>
                                <div class="item-after f12">{{$data['createTime']}}</div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">联系人</div>
                                <div class="item-after f12">{{$data['name']}}</div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">联系电话</div>
                                <div class="item-after f12">{{$data['mobile']}}</div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">收货地址</div>
                                <div class="item-after f12">{{$data['address']}}</div>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">备注</div>
                                <div class="item-after f12">{{$data['buyRemark']  or '无'}}</div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
@stop