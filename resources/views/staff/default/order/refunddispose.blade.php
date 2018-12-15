@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <style type="text/css">
        .p0{padding: 0;}
        .mt0{margin-top: 0;}
        .bar-footer{height: 3rem;}
        .bar-footer~.content{bottom: 3rem;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <div class="bar bar-tab y-orderbutton">
        <a href="#" class="button bg_red f_fff" id="udb_dsy_bnt_refund">确认拒绝</a>
    </div>
@stop
@section('contentcss')@stop
@section('content')
    <div class="list-block y-ulnobor y-sptj">
        <ul>
            <li class="item-content">
                <div class="item-inner">
                    <div class="item-title f_5e f14">拒绝原因</div>
                    <div class="item-after f_aaa f13" id="refund"><span id="newtip">请选择拒绝原因</span><i class="icon iconfont ml5 f14">&#xe64b;</i></div>
                    <input  type="hidden" name="content"/>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block media-list">
        <ul>
            <li>
                <a href="#" class="item-link item-content">
                    <div class="item-inner pr10">
                        <div class="item-title-row f14">
                            <div class="item-title">拒绝说明</div>
                        </div>
                        <div class="item-title-row">
                            <textarea type="text" placeholder="您可以告诉买家拒绝的详细原因和下一步如何操作，以便买家处理" class="f12 pl0" name="refundExplain"></textarea>
                        </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <ul class="y-addimg clearfix">
    @for($i=1; $i<=4; $i++)
        <form>
            <li id="li_{{$i}}">
                <label id="imglabel-{{$i}}" class="img-up-lb" for="image-form-{{$i}}">
                    <img data-num="{{$i}}" class="image_upload" src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                </label>
                <i class="delete tc none" data-id="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                <input type="text" name="images" id="upimage_{{$i}}" style="display:none">
                <input id="image-form-{{$i}}" type="file" accept="image/*" style="display:none" />
            </li>

        </form>
    @endfor
    </ul>
@stop
@section('show_nav')@stop
@section('page_js')
    <script type="text/tpl" id="cancehtml_udb_dsy">
        <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">商品已影响二次销售</span><input type="radio" name="reason" class="fr y-radio" value="1" checked ></li>
            <li><span id="cancelreason3">商品已发货</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li><span id="cancelreason4">买家不想退款了</span><input type="radio" name="reason" class="fr y-radio" value="4"></li>
            <li class="y-otherrea">
                <span id="cancelreason7">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="5">
                <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
            </li>
        </ul>
    </script>
    <script type="text/javascript">
        $(document).on('click','.image_upload', function () {
            var thisObj = $(this); 
            $(this).fanweImage({
                width:320, 
                height:320, 
                callback:function(url, target) {
                    thisObj.get(0).src = url;
                    $("#upimage_"+thisObj.data('num')).val(url); 
                    $("#li_"+thisObj.data('num')+" .delete").removeClass("none");
                }
            });
        });  
        Zepto(function($) {

            $(document).off("click",".page-current #udb_dsy_bnt_refund");
            $(document).off("click","#refund");
            $(document).on("click","#refund",function(){
                var textcancel = $("#cancehtml_udb_dsy").html();
                $.modal({
                    title:  '请选择退款原因',
                    text: textcancel,
                    buttons: [
                        {text: '取消'},
                        {
                            text: '确定',
                            bold:true,
                            onClick: function() {
                                var cancelradioval = $(' .y-cancelreason input[name="reason"]:checked ').val();
                                if(cancelradioval ==5){
                                    var cancelRemark = $(" #cancelreasontext").val();
                                    cancelRemark = (cancelRemark == "") ? $("#cancelreason"+cancelradioval).html() : cancelRemark;
                                }else{
                                    var cancelRemark = $(" #cancelreason"+cancelradioval).html();
                                }
                                $(".page-current #newtip").text(cancelRemark);
                                $(".page-current input[name='content']").val(cancelRemark);

                            }
                        }
                    ]
                })
            }).on("click",".page-current #udb_dsy_bnt_refund",function(){

                var data = {}
                data.content = $(".page-current input[name='content']").val();
                data.refundExplain = $(".page-current textarea[name='refundExplain']").val();
                data.status = 2;

                var images = [];
                $(".page-current input[name=images]").each(function(){
                    if($(this).val() != "" ){
                        images.push($(this).val());
                    }
                })
                data.images = images;
                data.id = "{{$data['id']}}";
                data.orderId = "{{$data['orderId']}}";

                $.post("{{ u('Order/refundSave') }}", data, function(res){
                    if(res.code == 81003){
                        $.toast(res.msg);
                        JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)
                    }else{
                        $.toast(res.msg);
                    }
                },'json');
            }); 
             //评价页面照片删除
            $(document).off("touchend",".y-addimg .delete");
            $(document).on("touchend",".y-addimg .delete",function(){
                $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                $(this).addClass("none");
                $("#upimage_" + $(this).data('id')).val("");
                return false;
            });
        });
 
    </script>
@stop

