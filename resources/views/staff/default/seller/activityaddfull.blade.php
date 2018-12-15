@extends('staff.default._layouts.base')

@section('css')
<style type="text/css">
    .y-sptj .item-after input{line-height: 19px;height: 19px;}
</style>
@stop

@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/activity') }}','#seller_activity_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop

@section('contentcss')hasbottom @stop
@section('show_nav')@stop
@section('content')
    <div class="list-block mt10 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">开始时间</div>
                    <div class="item-after f_aaa f13"><input type="text" class="tr f12 my-input" name="startTime" placeholder="请选择" readonly><i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                </div>
            </li>
            <li class="item-content pl0">
                <div class="item-inner">
                    <div class="item-title f_5e f13">结束时间</div>
                    <div class="item-after f_aaa f13"><input type="text" class="tr f12 my-input" name="endTime" placeholder="请选择" readonly><i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block mt10 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">每人每天参与次数</div>
                    <div class="item-after f_aaa f13"><input type="text" name="joinNumber" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="tr f12" placeholder="请输入参与次数"></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block mb0 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">消费满</div>
                    <div class="item-after f_333 maxw40 f13">￥<input type="text" name="fullMoney" placeholder="请输入满足金额" class="lh22 f13" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')"></div>
                </div>
            </li>
            <li class="item-content pl0">
                <div class="item-inner">
                    <div class="item-title f_5e f13">减</div>
                    <div class="item-after f_333 maxw40 f13">￥<input type="text" name="cutMoney" placeholder="请输入优惠金额" class="lh22 f13" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')"></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="content-block-title f_red f12 mt10 wsnor lh17">客单价{{number_format($data['totalPrice'], 2)}}元，建议设置高于客单价10-20元的消费金额，提高客单价</div>
    <div class="y-ddyz"><a href="#" class="button button-fill button-danger bg_ff2d4b submit">确定</a></div>
    
@stop
@section($js)
<script type="text/javascript">
$(function(){
    $(".my-input").calendar();

    $(document).off('click', '.submit');
    $(document).on('click', '.submit', function(){
        var data = new Object();
        data.startTime = $("input[name='startTime']").val();
        data.endTime = $("input[name='endTime']").val();
        data.joinNumber = $("input[name='joinNumber']").val();
        data.fullMoney = $("input[name='fullMoney']").val();
        data.cutMoney = $("input[name='cutMoney']").val();

        $.post("{{ u('Seller/activitySaveFull') }}", data, function(result){
            if(result.code == 0)
            {
                $.alert(result.msg, function(){
                    JumpURL("{{ u('Seller/activity') }}",'#seller_activity_view',2);
                })
            }
            else
            {
                $.alert(result.msg);
            }
        })
    })
})
</script>
@stop
@section('preloader')
@stop