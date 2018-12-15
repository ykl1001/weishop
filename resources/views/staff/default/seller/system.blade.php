@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('css')

@stop

@section('show_top')
    <header class="bar bar-nav">
         <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/systemgoods',["type"=>1,"tradeId"=>Input::get("tradeId")])}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">选择分类</h1>
    </header>
@stop
@section('distance')id="service-add" @stop
@section('content')
<?php
    $one = $two = true;
 ?>
<!-- 菜单列表 -->
    <div class="x-sjfltab x-goodstab clearfix">
        <div class="buttons-tab fl pr">
            @foreach($data as $key => $value)
                @if($value['id'] > 0)
                    <a href="#tab1_{{$key}}" class="tab-link button @if($one) active {{$one=false}} @endif">{{$value['name']}}</a>
                @endif
            @endforeach
        </div>
        <div class="tabs c-bgfff fl y-tabs">
            @foreach($data as $key => $value)
                @if($value['id'] > 0)
                    <div id="tab1_{{$key}}" class="tab @if($two) active {{$two=false}} @endif">
                        @foreach($value['twoLevel'] as $k2 => $v2)
                        <div>
                            <div class="content-block-title y-fltitle">{{$v2['name']}}</div>
                            <ul class="row no-gutter y-flnav c-bgfff">
                                @foreach($v2['threeLevel'] as $k3 => $v3)
                                    <li class="col-50" onclick="JumpURL('{{u('Seller/commodity',["systemTagListPid"=>$value["id"],"systemTagListId"=>$v3["id"],"type"=>1,"tradeId"=>Input::get("tradeId")])}}')">
                                        <a href="#" class="db" external="">
                                            @if(!empty($v3['img']))
                                                <span class="y-flimg">
                                                    <img src="{{ formatImage($v3['img'],80,80) }}">
                                                </span>
                                            @endif
                                            <p class="f13 mt5">{{$v3['name']}}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@stop

@section($js)
<script>
    $(function(){
        $("#{{$id_action.$ajaxurl_page}} .x-goodstab .tab").css("height",$(window).height()-44);
        $("#{{$id_action.$ajaxurl_page}} .x-goodstab .buttons-tab").css("height",$(window).height()-44);

    })
    $("#{{$id_action.$ajaxurl_page}} .content").css({"bottom":0,"overflow": "hidden"});
</script>
@stop
@section('show_nav')@stop