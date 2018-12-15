@extends('staff.default._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/activityAddSpecial') }}','#seller_activityAddSpecial_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <a class="button button-link button-nav pull-right goodsListSubmit">完成</a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom admin-order-bmanage infinite-scroll infinite-scroll-bottom pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('show_nav')@stop
@section('content')
<?php //dd($list); ?>
    @include('staff.default._layouts.refresh')
    <div class="content-block-title f14 mt10">商品列表</div>
        <div class="list-block media-list y-ulbnobor y-sylist y-addgoods">
            <ul class="lists_item_ajax" id="goodsLists">
                <!-- <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="../../images/abcd.png" width="52"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f_333 f14">清分无芯卷纸1*10</div>
                                <div class="item-after"><i class="icon iconfont f_red f20 mt10 none">&#xe638;</i><i class="icon iconfont f_ccc f20 mt10">&#xe677;</i></div>
                            </div>
                            <div class="item-subtitle">
                                <span class="f_red">￥28</span>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="../../images/abcd.png" width="52"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f_333 f14">清分无芯卷纸1*10</div>
                                <div class="item-after"><i class="icon iconfont f_red f20 mt10">&#xe638;</i><i class="icon iconfont f_red f20 mt10 none">&#xe677;</i></div>
                            </div>
                            <div class="item-subtitle">
                                <span class="f_red">￥28</span>
                            </div>
                        </div>
                    </a>
                </li> -->
                @include('staff.default.seller.activityspecialgoods_item')
            </ul>
        </div>
@stop
@section($js)
<script type="text/javascript">
    $(function(){
        $(document).off('click','.y-addgoods .item-content');
        $(document).on('click','.y-addgoods .item-content', function () {
            if (!$(this).hasClass("y-gray")) {
                var none = $(this).find(".item-after i.choose");
                if (none.hasClass("none")) {
                    none.removeClass("none").siblings("i").addClass("none");
                }else{
                    none.addClass("none").siblings("i").removeClass("none");
                }
            }
        });

        //完成
        $(document).off('click', '.goodsListSubmit');
        $(document).on('click', '.goodsListSubmit', function(){
            var ids = [];
            var i = 0;
            $("#goodsLists li.true").each(function(k,v){
                if( !$(this).find('i.choose').hasClass('none') ){
                    ids[i] = $(this).attr('data-id');
                    i++;
                }
            });

            $.post("{{ u('Seller/activitySaveSpecialGoods') }}", {'ids':ids}, function(result){
                if(result == 1)
                {
                    JumpURL("{{ u('Seller/activityAddSpecial') }}","#seller_activityAddSpecial_view",2); //回到活动编辑
                }
                
            })
        })

    })
</script>
@stop
@section('preloader')
@stop