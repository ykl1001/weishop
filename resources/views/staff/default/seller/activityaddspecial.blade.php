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
    <div class="bar bar-footer-secondary bg_none">
        <a href="#" class="button button-fill button-danger bg_ff2d4b special_submit">确定</a>
    </div>
@stop

@section('contentcss')hasbottom @stop
@section('show_nav')@stop
@section('content')
    <div class="list-block mt10 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">开始时间</div>
                    <div class="item-after f_aaa f13"><input type="text" name="startTime" class="tr f12 my-input" placeholder="请选择" value="{{$data['startTime']}}" readonly><i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                </div>
            </li>
            <li class="item-content pl0">
                <div class="item-inner">
                    <div class="item-title f_5e f13">结束时间</div>
                    <div class="item-after f_aaa f13"><input type="text" name="endTime" class="tr f12 my-input" placeholder="请选择" value="{{$data['endTime']}}" readonly><i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block mt10 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">每人每天参与次数</div>
                    <div class="item-after f_aaa f13"><input type="text" name="joinNumber" value="{{$data['joinNumber']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="tr f12" placeholder="请输入参与次数"></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block mb0 y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f13">商品折扣</div>
                    <div class="item-after f_aaa f13"><input type="text" class="tr f12" name="sale" value="{{$data['sale']}}"  onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value>10 || this.value<0)this.value='';else this.value2=this.value;" maxlength="5" placeholder="请输入折扣率，例如8.8"></div>
                </div>
            </li>
            <li class="item-content pl0">
                <div class="item-inner" onclick="$.addGoods()">
                    <div class="item-title f_5e f13">活动商品</div>
                    <div class="item-after f13"><span class="f_red">添加商品</span><i class="icon iconfont ml5 f_aaa f14">&#xe64b;</i></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block media-list y-ulbnobor y-sylist goodslists">
        <ul>
            @foreach($goodsList as $key => $value)
                <li id="key_{{$value['id']}}" data-id="{{$value['id']}}" data-price="{{$value['price']}}">
                    <a href="#" class="item-link item-content">
                        <div class="item-media"><img src="{{$value['image']}}" width="52"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f_333 f14">{{$value['name']}}</div>
                                <div class="item-after mt10 pl20 delete">
                                    <i class="icon iconfont f_999 f20">&#xe61e;</i>
                                </div>
                            </div>
                            <div class="item-subtitle mt-10">
                                <span class="f_red">￥<span class="showSalePrice">{{ $data['sale'] ? sprintf('%.2f', ($data['sale']/10)*$value['price']) : $value['price'] }}</span></span>
                                <small><del class="f_gray">￥{{$value['price']}}</del></small>
                                <input type="hidden" name="salePrice[]" class="salePrice" value="{{ $data['sale'] ? sprintf('%.2f', ($data['sale']/10)*$value['price']) : 0 }}">
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@stop
@section($js)
<script type="text/javascript">
$(function(){
    $(".my-input").calendar();

    $.addGoods = function(){
        var data = new Object();
        data.startTime  = $("input[name='startTime']").val();
        data.endTime    = $("input[name='endTime']").val();
        data.joinNumber = $("input[name='joinNumber']").val();
        data.sale       = $("input[name='sale']").val();

        //保存当前页面信息
        $.post("{{ u('Seller/activitySaveSpecialData') }}", data, function(result){
            if(result == 1)
            {
                //跳转到添加商品页
                JumpURL("{{ u('Seller/activitySpecialGoods') }}","#seller_activitySpecialGoods_view",2);
            }

        });
        
       
    }

    $(document).off("click", ".special_submit");    
    $(document).on("click", ".special_submit", function(){
        var data = new Object();
        data.startTime  = $("input[name='startTime']").val();
        data.endTime    = $("input[name='endTime']").val();
        data.joinNumber = $("input[name='joinNumber']").val();
        data.sale       = $("input[name='sale']").val();

        $.post("{{ u('Seller/activitySaveSpecial') }}", data, function(result){
            if(result.code == 0)
            {
                $.alert(result.msg, function(){
                    JumpURL("{{ u('Seller/activity') }}","#seller_activity_view",2); //跳转到添加商品页
                })
            }
            else
            {
                $.alert(result.msg);
            }
        });
    });

    $("input[name='sale']").blur(function(){
        var sale = $(this).val();
        $("div.goodslists ul li").each(function(k, v){
            var salePrice = $(this).attr('data-price') * (sale/10);
            $(this).find(".showSalePrice").text(salePrice.toFixed(2));
            $(this).find('.salePrice').val(salePrice.toFixed(2));
        })
    });

    $(document).off("click", ".delete");    
    $(document).on("click", ".delete", function(){
        var id = $(this).parents('li').attr('data-id');
        $.confirm('确认移除该商品吗', function(){
            $.post("{{ u('Seller/activityDeleteSpecialGoods') }}", {'id':id}, function(result){
                if(result == 1)
                {
                    $("#key_"+id).remove();
                }
            });
        })
    });
})
</script>
@stop
@section('preloader')
@stop