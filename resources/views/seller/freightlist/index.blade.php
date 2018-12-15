@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .fa-mobile{font-size: 25px;}
    .udb_shoe_div{
        border: 1px solid #c2c2c2;
    }
    .udb_shoe_div p{
        margin: 5px;
    }
    .w100{
        width: 100px;
    }
    .card-content{
        border-top: 1px solid #cccccc;
    }
    .udb_bnt_show{
        width: 150px;
        padding: 10px;
        background: #f9f9f9;
        border: 1px solid #cccccc;
        -moz-border-radius: 15px;      /* Gecko browsers */
        -webkit-border-radius: 15px;   /* Webkit browsers */
        border-radius:15px;            /* W3C syntax */
        cursor:pointer
    }
    .udb_bnt_city{
        margin-top: 1px;
        width: 150px;
        padding: 5px;
        background: #f9f9f9;
        border: 1px solid #cccccc;
        height: 28px;
        cursor:pointer
    }
    .udb_times{
        padding: 5px;
        border: 1px solid #cccccc;
        -moz-border-radius: 100px;      /* Gecko browsers */
        -webkit-border-radius: 100px;   /* Webkit browsers */
        border-radius:100px;            /* W3C syntax */
        cursor:pointer
    }
    .udb_times:hover{
        background: #f9f9f9;
        /*transform: rotate(80deg);*/
        /*-o-transform: rotate(80deg);*/
        /*-webkit-transform: rotate(80deg);*/
        /*-moz-transform: rotate(80deg);*/
        /*filter:progid:DXImageTransform.Microsoft.BasicImage(Rotation=2);*/
    }
    .udb_del{
        margin: 5px;
    }
    .bgf9f9f9{
        background: #f9f9f9;
    }
    .udb_c{
        overflow-x:hidden;
        overflow-y:scroll;
        max-height: 400px;
        text-align: center;
    }
    .udb_c li{
        border: 1px solid #9c9c9c;
        margin: 2px;
    }
    .udb_c li:hover{
        border: 1px solid #31b0d5 !important;
        background-color: #f9f9f9 !important;
        cursor:pointer
    }
    .udb_c_on{
        border: 1px solid #9f0000 !important;
        background-color: #f9f9f9 !important;
        color: #9f0000 !important;
        cursor:pointer
    }
    .udb_c li:last-of-type{
        /*border-bottom: 0px;*/
    }
    .udb_div{
        width: 200px;
    }
</style>
@stop
@section('content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <div class="g-fwgl">
            <p class="f-bhtt f14 clearfix" style="border-bottom:0;">
                <span class="ml15 fl">运费设置</span>
            </p>
        </div>
        <div class="udb_shoe_div">
            <div class="p10">
                <div class="card-header f14">默认（除指定地区外）运费</div>
                <div class="card-content">
                    <div class="card-content-inner f12 f_999">
                        <p>
                            <input type="text"  name="defaultNum" class="u-ipttext w100" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{ $list['default']['num'] ? $list['default']['num'] : 0 }}"/> 件商品内，运费
                            <input type="text"  name="defaultMoney" class="u-ipttext w100" value="{{ $list['default']['money'] ? $list['default']['money'] : '0.00' }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')"/> 元 ,每增加
                            <input type="text"  name="defaultAddNum" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" class="u-ipttext w100" value="{{ $list['default']['addNum'] ? $list['default']['addNum'] : 0 }}"/> 件商品，运费增加
                            <input type="text"  name="defaultAddMoney" class="u-ipttext w100" value="{{ $list['default']['addMoney'] ? $list['default']['addMoney'] : '0.00' }}" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" />  元
                        </p>
                    </div>
                </div>
                <div class="card-content udb_bnt_item @if(!$list['other'])none @endif pl20">
                    @foreach($list['other'] as $key => $value)
                        <div class="card-content-inner f12 f_999 bgf9f9f9 dsy-{{$value['data']['id']}}">
                            <div class="udb_del">
                                <div class="mb10 mt20 @if(!$value['cityName'])none @endif red p5 dsy-c-{{$value['data']['id']}}">
                                    {{$value['cityName']}}
                                </div>
                                <span class="udb_bnt_city" data-id="{{$value['data']['id']}}"><i class="fa fa-hand-o-right"></i> 选择城市</span>
                                <input type="text"  class="u-ipttext w100" name="otherNum[]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{ $value['data']['num'] }}"/> 件商品内，运费
                                <input type="text"  class="u-ipttext w100" name="otherMoney[]" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" value="{{ $value['data']['money'] }}"/> 元 ,每增加
                                <input type="text"  class="u-ipttext w100" name="otherAddNum[]" onkeyup="this.value=this.value.replace(/\D/g,'')" value="{{ $value['data']['addNum'] }}"/> 件商品，运费增加
                                <input type="text"  class="u-ipttext w100" name="otherAddMoney[]" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" value="{{ $value['data']['addMoney']}}"/>  元
                                &nbsp;&nbsp;&nbsp;<i class="mr5 fa fa-times red udb_times"></i>
                                <input type="hidden" id="ids" name="ids[]" value="{{$value['pid']}}"/>
                                <input type="hidden" name="cid[]" value="{{$value['data']['id']}}"/>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-content">
                    <div class="udb_bnt_show mt5">
                        <i class="mr5 fa fa-plus red"></i>
                        添加指定地区运费
                    </div>
                </div>
            </div>
        </div>
    </yz:form>
    @yizan_end
@stop
@section('js')
<script type="text/tpl" id="dsyHtml">
<div class="card-content-inner f12 f_999 bgf9f9f9 dsy-UDB_INDEX_KEY">
    <div class="udb_del">
        <div class="mb10 mt20 none red p5 dsy-c-UDB_INDEX_KEY"></div>
        <span class="udb_bnt_city" data-id="UDB_INDEX_KEY"><i class="fa fa-hand-o-right"></i> 选择城市</span>
        <input type="text"  class="u-ipttext w100" name="otherNum[]" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="0"/> 件商品内，运费
        <input type="text"  class="u-ipttext w100" name="otherMoney[]" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" value="0.00"/> 元 ,每增加
        <input type="text"  class="u-ipttext w100" name="otherAddNum[]" onkeyup="this.value=this.value.replace(/\D/g,'')" value="0"/> 件商品，运费增加
        <input type="text"  class="u-ipttext w100" name="otherAddMoney[]" onkeyup="if(isNaN(value))execCommand('undo')" onafterpaste="if(isNaN(value))execCommand('undo')" value="0.00"/>  元
        &nbsp;&nbsp;&nbsp;<i class="mr5 fa fa-times red udb_times"></i>
        <input type="hidden" id="ids" name="ids[]"/>
        <input type="hidden" name="cid[]" value="UDB_INDEX_KEY"/>
    </div>
</div>
</script>
<script type="text/javascript">
$(function () {

    var index_item = 150;
    var dsyId = 0;
    $(".udb_bnt_show").click(function(){
        if($(".udb_bnt_item").hasClass("none")){
            $(".udb_bnt_item").removeClass("none");
        }
        var i = index_item * Math.ceil(Math.random() * 9);
        if(!$('.card-content-inner').hasClass("dsy-"+i)){
            var html = $("#dsyHtml").html().replace('UDB_INDEX_KEY',i).replace('UDB_INDEX_KEY',i).replace('UDB_INDEX_KEY',i).replace('UDB_INDEX_KEY',i);
            $(".udb_bnt_item").append(html);
        }else{
            index_item ++;
            $(".udb_bnt_show").click();
        }
    });
    $(document).on('click', '.udb_bnt_city', function() {
           dsyId = $(this).data("id");
            $.zydialogs.open("<p style='margin: 10px'>正在努力获取城市相关数据···</p>",{
                width:300,
                title:"正在加载",
                showButton:false,
                showClose:false,
                showLoading:true
            }).setLoading();

            $.post("{{u('FreightList/region')}}",{modelId:dsyId},function(res){
                $.zydialogs.close();
                var text = [];
                var ids = [];
                var dialog = $.zydialogs.open(res, {
                    boxid:'SET_GROUP_WEEBOX',
                    width:"50%",
                    title:'选择城市',
                    showClose:true,
                    showButton:true,
                    showOk:true,
                    showCancel:true,
                    okBtnName: '确认',
                    cancelBtnName: '取消',
                    contentType:'content',
                    onOk: function(){
                        $.each($(".udb_div_province ul li"),function(){
                            if($(this).hasClass("udb_c_on")){
                                text.push($(this).html());
                                ids.push($(this).attr('data-id'));
                            }
                        });

                        $(".dsy-"+dsyId).find("#ids").val(ids);
                        $(".dsy-c-"+dsyId).removeClass("none").html(text);
                        dialog.setLoading(false);
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    },
                    onCancel:function(){
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                });
                var cid = $(".dsy-"+dsyId).find("#ids").val().split(",");
                $.each(cid,function(i,v){
                    $(".udb_province_show"+v).addClass("udb_c_on");
                });
            });

    }).on('click', '.udb_times', function() {
        $(this).parents(".card-content-inner").slideUp("slow", function() {//slide up
            $(this).remove();//then remove from the DOM
            if($(".card-content-inner .udb_del").length <= 0){
                $(".udb_bnt_item").addClass("none");
            }
        });
    }).on('click', '.udb_div_province .udb_c li', function() {

        if($(this).hasClass("udb_c_on")){
            var bln = false;
            $.each($(".udb_div_city ul li"),function(k,v){
                if($(this).hasClass("udb_c_on")){
                    bln = true;
                    return false;
                }
            });
            if(!bln){
                $(this).removeClass("udb_c_on");
               // $(".udb_div_city").addClass("none");
            }
        }else{
            $(this).addClass("udb_c_on");
            //$(".udb_div_city").removeClass("none");
        }
        var pid = $(this).attr("data-id");
        $(".udb_div_city ul").html("");
        $.post("{{u('FreightList/regionajax')}}",{pid:pid,modelId:dsyId},function(res){
            $.each(res,function(i,v){

                $.each(v,function(is,vs){
                    var css = "";
                    if(vs.selected){
                        css = "udb_c_on";
                    }

                    if(vs.allselected){
                        css = "udb_c_on";
                    }
                    var html = "<li class='"+css+"'  data-id="+vs.id+" data-pid="+vs.pid+">"+vs.name+"</li>"
                    $(".udb_div_city ul").append(html);
                });

            });
        });

    }).on('click', '.udb_div_city .udb_c li', function() {
        var province = {};
        var city = [];
        if($(this).hasClass("udb_c_on")){
            $(this).removeClass("udb_c_on");
            $(".udb_div_area").addClass("none");
        }else{
            $(this).addClass("udb_c_on");
            $(".udb_div_area").removeClass("none");
        }
        $.each($(".udb_div_city ul li"),function(k,v){
            if($(this).hasClass("udb_c_on")){
                city.push($(this).attr("data-id"));
            }
        });
        province.ids = city;
        province.pid = $(this).attr("data-pid");
        province.modelId = dsyId;
        $.post("{{u('FreightList/saveCheckLocation')}}",province,function(res){});
    });

});
</script>
@stop



