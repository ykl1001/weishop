@extends('staff.default._layouts.base')

@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left "href="#" onclick="JumpURL('{{u('Seller/freightList')}}','#seller_freightList_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">修改运费</h1>
        <a class="button button-link button-nav pull-right f14" href="#" id="saveFreightTmp" data-transition='slide-out'>
            完成
        </a>
    </header>
    <div class="bar bar-footer">
        <div class="y-addfreight">
            <i class="icon iconfont f_red">&#xe618;</i>
            <span class="f14">添加指定地区运费</span>
        </div>
    </div>
@stop

@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop

@section('show_nav') @stop

@section('content')
    <div class="card y-yfszcard">
        <div class="card-header f14">默认（除指定地区外）运费:</div>
        <div class="card-content">
            <div class="card-content-inner f12 f_999">
                <div class="y-lineh30">
                    <input type="text" value="{{ $list['default']['num'] ? $list['default']['num'] : 0 }}" name="defaultNum" class="y-xgyfinput ml0 precision-0 defaultNum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">件商品内，运费<input type="text" value="{{ $list['default']['money'] ? $list['default']['money'] : '0.00' }}" name="defaultMoney" class="y-xgyfinput precision-2 defaultMoney" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">元
                </div>
                <div class="y-lineh30">
                    每增加<input type="text" value="{{ $list['default']['addNum'] ? $list['default']['addNum'] : 0 }}" name="defaultAddNum" class="y-xgyfinput precision-0 defaultAddNum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">件商品，运费增加<input type="text" value="{{ $list['default']['addMoney'] ? $list['default']['addMoney'] : '0.00' }}" name="defaultAddMoney" class="y-xgyfinput precision-2 defaultAddMoney" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">元
                </div>
            </div>
        </div>
    </div>

    <!-- 数据库模版 -->
    @foreach($list['other'] as $key => $value)
    <div class="y-addregion addModel" data-modelid="{{$key}}">
        <i class="icon iconfont y-xgyfdel" data-id="{{ $value['data']['id'] }}">&#xe605;</i>
        <div class="card y-yfszcard">
            <a onclick="$.saveModel({{$key}})" class="card-header f14">
                <p>{{ !empty($value['cityName']) ? $value['cityName'] : '选择地区' }}</p>
                <i class="icon iconfont f_999">&#xe64b;</i>
            </a>
            <div class="card-content">
                <div class="card-content-inner f12 f_999 addTmp">
                    <div class="y-lineh30">
                        <input type="text" value="{{ $value['data']['num'] }}" name="otherNum" class="y-xgyfinput ml0 precision-0 otherNum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">件商品内，运费<input type="text" value="{{ $value['data']['money'] }}" name="otherMoney" class="y-xgyfinput precision-2 otherMoney" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">元
                    </div>
                    <div class="y-lineh30">
                        每增加<input type="text" value="{{ $value['data']['addNum'] }}" name="otherAddNum" class="y-xgyfinput precision-0 otherAddNum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">件商品，运费增加<input type="text" value="{{ $value['data']['addMoney']}}" name="otherAddMoney" class="y-xgyfinput precision-2 otherAddMoney" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')">元
                    </div>
                    <!-- 城市 -->
                    <div class="none cityData">
                        {!! !empty($value['city']) ? serialize($value['city']) : '' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="y-addrliwai"></div>
@stop

@section($js)
<script type="text/javascript">
$(function(){
    //模版自增ID
    var did = "{{ max(array_keys($list['other'])) ? max(array_keys($list['other'])) + 1 : 10000 }}";
    //删除模版
    $(document).off("click",".y-xgyfdel");
    $(document).on("click",".y-xgyfdel",function(){
        var id = $(this).data('id');
        if(id > 0)
        {
            $.post("{{ u('Seller/deleteFreight') }}", {'id':id}, function(res){

            })
        }
        $(this).parent().remove();
    })
    //添加
    $(document).off("click",".y-addfreight");
    $(document).on("click",".y-addfreight",function(){
        var html = '<div class="y-addregion addModel" data-modelid="'+did+'">\
                        <i class="icon iconfont y-xgyfdel">&#xe605;</i>\
                        <div class="card y-yfszcard">\
                            <a onclick="$.saveModel('+did+')" class="card-header f14">\
                                <p>选择地区</p>\
                                <i class="icon iconfont f_999">&#xe64b;</i>\
                            </a>\
                            <div class="card-content">\
                                <div class="card-content-inner f12 f_999 addTmp">\
                                    <div class="y-lineh30">\
                                        <input type="text" value="0" name="otherNum" class="y-xgyfinput ml0 precision-0 otherNum" onkeyup="this.value=this.value.replace(/\D/g,\'\')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')">件商品内，运费<input type="text" value="0.00" name="otherMoney" class="y-xgyfinput precision-2 otherMoney" onkeyup="if(isNaN(value))execCommand(\'undo\')" onafterpaste="if(isNaN(value))execCommand(\'undo\')">元\
                                    </div>\
                                    <div class="y-lineh30">\
                                        每增加<input type="text" value="0" name="otherAddNum" class="y-xgyfinput precision-0 otherAddNum" onkeyup="this.value=this.value.replace(/\D/g,\'\')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')">件商品，运费增加<input type="text" value="0.00" name="otherAddMoney" class="y-xgyfinput precision-2 otherAddMoney" onkeyup="if(isNaN(value))execCommand(\'undo\')" onafterpaste="if(isNaN(value))execCommand(\'undo\')">元\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';
        $(".y-addrliwai").append(html);
        did++;
        $(".content").scrollTop($(".y-addrliwai").height());
    })

    //0处理
    $("input.precision-0,input.precision-2").live("focus", function(){
        if( $(this).val() <= 0)
        {
            $(this).val('');
        }
    })

    //数字精度处理
    $("input.precision-0").live("blur",function(){
        var precision = parseFloat($(this).val()).toFixed(0) >= 0 ? parseFloat($(this).val()).toFixed(0) : '0';
        $(this).val(precision);
    });
    $("input.precision-2").live("blur",function(){
        var precision = parseFloat($(this).val()).toFixed(2) >= 0 ? parseFloat($(this).val()).toFixed(2) : '0.00';
        $(this).val(precision);
    });

    //完成
    $("#saveFreightTmp").click(function(){
        var data = [];
        var submit = true;
        $.showPreloader('数据保存中...');

        //默认
        data[0] = [
            0,
            $('.defaultNum').val(),
            $('.defaultMoney').val(),
            $('.defaultAddNum').val(),
            $('.defaultAddMoney').val()
        ];
        // 其他
        $("div.addTmp").each(function(k, v){
            if( $.trim($(this).find('.cityData').text()) == "" )
            {
                
                $.alert('还有模版没有选择城市哟！');
                submit = false;
            }
            else
            {
                data[k+1] = [
                    $.trim($(this).find('.cityData').text()),
                    $(this).find('.otherNum').val(),
                    $(this).find('.otherMoney').val(),
                    $(this).find('.otherAddNum').val(),
                    $(this).find('.otherAddMoney').val()
                ];
            }
            
        });
        
        if(!submit)
        {
            $.hidePreloader();
            return false;
        }

        $.post("{{ u('Seller/saveFreight') }}", {'data':data}, function(res){
            $.hidePreloader();
            if(res.code == 0)
            {
                $.alert(res.msg, function(){
                    JumpURL("{{u('Seller/freightList')}}",'#seller_freightList_view',2);
                });
            }
            else{
                $.alert(res.msg);
            }
        });
    });
        

    //选择地址 保存模版
    $.saveModel = function(did) {
        var model = [];
        $.showPreloader('正在保存模版数据...');

        $("div.addModel").each(function(k, v){
            model[k] = [
                $(this).data("modelid"),
                $(this).find('.otherNum').val(),
                $(this).find('.otherMoney').val(),
                $(this).find('.otherAddNum').val(),
                $(this).find('.otherAddMoney').val()
            ];
        });

        $.post("{{ u('Seller/saveModel') }}", {'model':model}, function(res){
            $.hidePreloader();
            JumpURL("{{u('Seller/checkLocation')}}?modelId="+did,'#seller_checkLocation_view',2);
        });
        
    }
    
})
</script>
@stop
