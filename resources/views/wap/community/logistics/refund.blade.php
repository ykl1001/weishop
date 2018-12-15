@extends('wap.community._layouts.base')


@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="$.href('{{u('Logistics/ckservice',['id' => $data['id'],'type'=>$args['type']])}}')" data-transition='slide-out' data-no-cache="true">
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right y-splistcd" href="#" data-transition='slide-out'>
            <span class="icon iconfont">&#xe692;</span>
            @foreach($indexnav as $key => $i_nav)
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="y-redc"></span>
                @endif
            @endforeach
        </a>
        <h1 class="title f16">申请退款</h1>
    </header>
    <style>
        /*取消原因*/
        .y-cancelreason{margin: -.5rem;}
        .y-cancelreason li{}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;
            word-break:break-all;}
    </style>
@stop

@section('content')
    <ul class="x-ltmore f12 c-gray current_icon none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')">
                <i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
            </li>
        @endforeach
    </ul>
    <nav class="bar bar-tab y-heightnone">
        <a href="#" class="button button-fill button-danger y-button" id="udb_dsy_bnt_refund">提交</a>
    </nav>
    <div class="content" id=''>
        <div class="list-block media-list">
            <ul class="y-nobor2">
                <li>
                    <a class="item-link item-content p0" href="#" onclick="$.href('{{u('Logistics/ckservice',['id' => $data['id'],'type'=>$args['type']])}}')">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">申请服务</div>
                                <div class="item-after c-gray f12">@if($args['orderType'] == 1)退款退货@elseif($args['orderType'] == 2)仅退款@else请选择@endif<i class="icon iconfont ml5">&#xe602;</i></div>
                                <input  type="hidden" name="orderType" value="{{$args['orderType']}}"/>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="item-link item-content p0" href="#" id="refund">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">@if($args['orderType'] == 1)退货@else退款@endif原因</div>
                                <div class="item-after c-gray f12"><span id="newtip">请选择@if($args['orderType'] == 1)退货@else退款@endif原因</span><i class="icon iconfont ml5">&#xe602;</i></div>
                                <input type="hidden"  name="content"/>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="list-block media-list">
            <ul class="y-nobor2">
                <li>
                    <a class="item-link item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="item-title-row">
                                <div class="item-title">退款金额</div>
                                <div class="item-after c-gray mt10 f12">￥<span class="c-red f16">{{sprintf("%.2f",$data['payFee'])}}</span></div>
                            </div>
                            <div class="item-title c-gray f12 mt-10">退款金额最多<span>{{sprintf("%.2f",$data['payFee'])}}</span>元</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div class="content-block-title f12 c-red">
            <i class="icon iconfont va-1 f18">&#xe620;</i>
            金额不可以超过{{sprintf("%.2f",$data['payFee'])}}元
        </div>
        <div class="list-block media-list y-qrddqt">
            <ul>
                <li>
                    <a href="#" class="item-link item-content">
                        <div class="item-inner">
                            <div class="item-title-row f14">
                                <div class="item-title">退款说明</div>
                            </div>
                            <div class="item-title-row f14">
                                <input type="text" placeholder="请填写备注信息(非必填)" class="y-qrddinput" name="refundExplain">
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
                    </li>

                </form>
            @endfor
        </ul>

    </div>
    <script type="text/tpl" id="cancehtml_udb_dsy">
        <ul class="y-cancelreason tl f13">
            <li><span id="cancelreason1">大小尺寸与商品描述不符</span><input type="radio" name="reason" class="fr y-radio" value="1" checked></li>
            <li><span id="cancelreason2">买家发错货</span><input type="radio" name="reason" class="fr y-radio" value="2"></li>
            <li><span id="cancelreason3">尺码拍错/不喜欢/效果差</span><input type="radio" name="reason" class="fr y-radio" value="3"></li>
            <li><span id="cancelreason4">颜色/款式/图案与描述不符</span><input type="radio" name="reason" class="fr y-radio" value="4"></li>
            <li><span id="cancelreason5">收到商品少件或破损</span><input type="radio" name="reason" class="fr y-radio" value="5"></li>
            <li><span id="cancelreason6">材质/面料与商品描述不符</span><input type="radio" name="reason" class="fr y-radio" value="6"></li>
            <li class="y-otherrea">
                <span id="cancelreason7">其他原因</span><input type="radio" name="reason" class="fr y-radio" value="7">
                <textarea id="cancelreasontext" placeholder="请输入其他原因" maxlength="200" class="y-otherreasons c-gray none"  rows="1" onpropertychange="this.style.height=this.scrollHeight+'px';"  oninput="this.style.height=this.scrollHeight+'px';" style="overflow:hidden;height:16px;"></textarea>
            </li>
        </ul>
    </script>

    <script type="text/javascript">
        Zepto(function($){

            $(document).off("click",".page-current #udb_dsy_bnt_refund");
            $(document).off("click",".page-current #refund");
            $(document).on("click",".page-current #refund",function(){
                var textcancel = $(".page-current #cancehtml_udb_dsy").html();
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
                                if(cancelradioval == 7){
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
                data.refundType = $(".page-current input[name='orderType']").val();
                data.content = $(".page-current input[name='content']").val();
                data.refundExplain = $(".page-current input[name='refundExplain']").val();
                var images = [];
                $(".page-current input[name=images]").each(function(){
                    if($(this).val() != "" ){
                        images.push($(this).val());
                    }
                })
                data.images = images;
                data.orderId = {{$data['id']}};
                data.type = {{$args['type'] or 0}};
                $.post("{{ u('Logistics/refundSave') }}", data, function(res){
                    if(res.code == 81003){
                        $.toast(res.msg);
                        var url = "{{ u('Logistics/refundview') }}?orderId="+data.orderId;
                        $.href(url);
                    }else{
                        if(res.msg == "" || res.msg == undefined){
                            $.toast("提交失败,请刷新重试!");
                        }else{
                            $.toast(res.msg);
                        }
                    }
                },'json');
            });
            $(document).off("click",".y-cancelreason li input");
            //取消原因—其他原因
            $(document).on("click",".y-cancelreason li input",function(){
                $(".y-otherreasons").addClass("none");
            }).on("click",".y-cancelreason li.y-otherrea input",function(){
                $(".y-otherreasons").removeClass("none");
            }); 
             //评价页面照片删除
            $(document).off("touchend",".page-current .y-addimg .delete");
            $(document).on("touchend",".page-current .y-addimg .delete",function(){
                $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                $(this).addClass("none");
                $("#upimage_" + $(this).data('id')).val("");
                return false;
            });
        });
    </script>
@stop
@section($js)
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
    </script>
@stop