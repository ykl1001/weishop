@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('css')
    <style>
        .modal.show_img_x  {
            width:inherit;
            left: 38%;
        }
        .service-deal {
            z-index: 99999 !important;
        }
        .y-addimg{width: 100%;box-sizing: border-box;padding-left: .5rem;margin-bottom: .5rem;}
        .y-addimg li{width: 22%;box-sizing: border-box;float: left;text-align: center;margin-right: 3%;color: #54abee;position: relative;max-height: 6.2rem;}
        .y-addimg li label{width: 100%;height: 100%;display: block;border-radius: 5px;overflow: hidden;}
        .y-addimg li p{position: absolute;top: 0;left: 0;z-index: 10;width: 100%;text-align: center;background: #f1f1f1;}
        .y-addimg li img{width: 100%;vertical-align: top;}
        .y-addimg li .delete{position: absolute;top: -10px;right: -10px;}
        input,div {font-size: 14px !important;}
    </style>
@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{$nav_back_url}}','{{$css}}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r" onclick="$.goodssave({{Input::get("tradeId")}},{{Input::get("type")}})">
            完成
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop
@if($data && $nopre != "nopre")
@section('preview')
    <div class="flex service-deal">
        <div class="flex-2 tc">
            @if($data['status'] == 1)
                <i class="icon iconfont">&#xe67e;</i>
                <span class="focus-color-f" id="opgoods" onclick="$.opgoods(2,{{$data['id']}},{{$args['tradeId']}})">下架</span>
            @else
                <i class="icon iconfont" id="opgoods_iconfont">&#xe67f;</i>
                <span class="focus-color-f" id="opgoods" onclick="$.opgoods(1,{{$data['id']}},{{$args['tradeId']}})" >上架</span>
            @endif
        </div>
        <div class="flex-2 tc">
            <a href="#" onclick="JumpURL('{{u('Seller/preview',['id'=>$data['id'],'type'=>$args['type'],'tradeId'=>$args['tradeId']])}}','#seller_preview_view',2)">
                <i class="icon iconfont">&#xe680;</i>
                <span>预览</span>
            </a>
        </div>
        @if($data['type'] == 1)
            <div class="flex-2 tc" onclick="$.opgoods(3,{{$data['id']}},{{$args['tradeId']}})">
                @else
                    <div class="flex-2 tc" onclick="$.opgoods(4,{{$data['id']}},{{$args['tradeId']}})">
                        @endif
                        <i class="icon iconfont">&#xe61e;</i>
                        <span>删除</span>
                    </div>
            </div>
            @stop
        @endif
        @section('content')
            <form action="javascript:;" id="goods-form">
                <div id="preview" @if($data)style="height: 200px;" @endif>
                    <div class="content-block-title m10">商品图片(第一张为封面，最多4张)</div>
                    <ul class="y-addimg clearfix">

                        <?php $img = $data['images'] != "" ? $data['images']:$showData['imgs']; ?>

                        @if($data)
                            <?php
                            $img = array_filter($img);
                            $count = count($img);
                            if($count < 4){
                                $mustcount = 4 -$count;
                            }else{
                                $mustcount = 0;
                            }
                            ?>
                            @foreach($img as $k=>$v)
                                <li id="li_{{$k}}">
                                    <label id="imglabel-{{$k}}" for="image-form-{{$k}}">
                                        <img data-num="{{$k}}" class="image_upload" src="{{ formatImage($v,640,640) }}">
                                    </label>
                                    <i class="delete tc" data-id="{{$k}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                    <input type="text" name="imgs[]" id="upimage_{{$k}}" value="{{$v}}" style="display:none"> 
                                </li>
                            @endforeach
                            @if($mustcount > 0)
                                @for($i=0; $i<$mustcount; $i++)
                                    <li id="li_{{$i+$count}}">
                                        <label id="imglabel-{{$i+$count}}" for="image-form-{{$i+$count}}">
                                            <img data-num="{{$i+$count}}" class="image_upload"  src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                                        </label>
                                        <i class="delete tc none" data-id="{{$i+$count}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                        <input type="text" name="imgs[]" id="upimage_{{$i+$count}}" style="display:none"> 
                                    </li>
                                @endfor
                            @endif
                        @else
                            @if($img != "")
                                <?php
                                $img = array_filter($img);
                                $count = count($img);
                                if($count < 4){
                                    $mustcount = 4 -$count;
                                }else{
                                    $mustcount = 0;
                                }
                                ?>
                                @foreach($img as $k=>$v)
                                    <li id="li_{{$k}}">
                                        <label id="imglabel-{{$k}}" for="image-form-{{$k}}">
                                            <img data-num="{{$k}}" class="image_upload" src="{{ formatImage($v,640,640) }}">
                                        </label>
                                        <i class="delete tc" data-id="{{$k}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                        <input type="text" name="imgs[]" id="upimage_{{$k}}" value="{{$v}}" style="display:none">
                                        <input id="image-form-{{$k}}" type="file" accept="image/*" style="display:none" />
                                    </li>
                                @endforeach
                                @if($mustcount > 0)
                                    @for($i=0; $i<$mustcount; $i++)
                                        <li id="li_{{$i+$count}}">
                                            <label id="imglabel-{{$i+$count}}" for="image-form-{{$i+$count}}">
                                                <img data-num="{{$i+$count}}" class="image_upload" src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                                            </label>
                                            <i class="delete tc none" data-id="{{$i+$count}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                            <input type="text" name="imgs[]" id="upimage_{{$i+$count}}" style="display:none"> 
                                        </li>
                                    @endfor
                                @endif
                            @else
                                @for($i=0; $i<=3; $i++)
                                    <li id="li_{{$i}}">
                                        <label id="imglabel-{{$i}}" for="image-form-{{$i}}">
                                            <img data-num="{{$i}}" class="image_upload" src="{{ asset('wap/community/newclient/images/addpic.png') }}">
                                        </label>
                                        <i class="delete tc none" data-id="{{$i}}"><i class="icon iconfont f20">&#xe605;</i></i>
                                        <input type="text" name="imgs[]" id="upimage_{{$i}}" style="display:none"> 
                                    </li>
                                @endfor
                            @endif
                        @endif
                    </ul>
                </div>
                <input type="hidden" name="type" value="{{ $data['type'] or $args['type'] }}"/>
                <input type="hidden" value="{{ $data['cateId'] or $args['tradeId']}}" name="tradeId" />
                <input type="hidden" value="{{ (int)$data['id'] }}" name="id" />
                <input type="hidden" value="{{$data['systemGoodsId'] or 0 }}" name="systemGoodsId" />
                @if($args['type'] == 1)
                    <div class="list-block">
                        <ul>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">商品名称:</div>
                                        <div class="item-input">
                                            <input type="text" class="f14" placeholder="必填" name="name" id="name" value="{{$data['name'] or $showData['name']}}">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">商品编码:</div>
                                        <div class="item-input">
                                            <input type="text" class="f14" @if($data) readonly="readonly" @endif placeholder="请输入1-16位商品编码" name="goodsSn" id="goodsSn" maxlength="16" onKeyUp="value=value.replace(/[\W]/g,'')" value="@if($data){{$data['goodsSn'] or "无"}}@else{{$showData['goodsSn']}}@endif">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="y-spbq" @if($data == "") onclick="$.showData()" @else @if($data['systemGoodsId'] ==  0 || $data['systemGoodsId'] ==  "" ) onclick="JumpURL('{!! u('Seller/getTagLists',['nav_back_url'=>$nav_back_url]) !!}','{{$css}}',2)" @else  onclick="$.showSystemMsg()"  @endif @endif>
                        <span>商品标签:</span>
                <span class="fr">
                     @if($tag)
                        <span id="systemTagList">{{$tag['pid']['name'] or '未选择'}}</span> - <span id="systemTag">{{$tag['name'] or '未选择'}}</span>
                        <input type="hidden" name="systemTagListPid" id="systemTagListPid" value="{{$tag['pid']['id'] or 0}}">
                        <input type="hidden" name="systemTagListId" id="systemTagListId" value="{{$tag['id'] or 0}}">
                    @else
                        <span id="systemTagList">请选择分类</span>
                    @endif
                    <i class="icon iconfont">&#xe64b;</i>
                </span>
                    </div>
                    <div class="list-block add-b @if($data) @if($data['norms'])add-block @endif @endif show_norms @if($showData['norms'])add-block @endif">
                        @if($data)
                            @if($data['norms'])
                                @foreach( $data['norms'] as $k=> $v)
                                    <div  id="del{{$v['id']}}" >
                                        <div class="delete-but" onclick ="$.deletebut({{$v['id']}})">
                                            <i class="icon iconfont right-ico">&#xe619;</i>
                                        </div>
                                        <ul class="goods-editer-b s-goods-editer-b">
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">型&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
                                                        <div class="item-input">
                                                            <input type="hidden" placeholder="" name="norms[{{$k}}][id]" id="id" value="{{ $v['id'] }}">
                                                            <input type="text" class="f14" placeholder="尺寸，颜色，大小等" name="norms[{{$k}}][name]" id="norms" value="{{$v['name']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                                        <div class="item-input">
                                                            <input type="text" class="f14" placeholder="请输入金额（元）"  name="norms[{{$k}}][price]" id="price" value="{{$v['price']}}">
                                                        </div>
                                                        <span class="unit">元</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                                        <div class="item-input">
                                                            <input type="text" class="f14" name="norms[{{$k}}][stock]" placeholder="必须是数字"  id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{$v['stock']}}">
                                                        </div>

                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <ul class="goods-editer-b h-goods-editer-b">
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                                <div class="item-input">
                                                    <input type="text" class="f14" placeholder="请输入金额（元）"  name="price" id="price" value="{{$data['price']}}">
                                                </div>
                                                <span class="unit">元</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                                <div class="item-input">
                                                    <input type="text" class="f14"  placeholder="必须是数字"   value="{{$data['stock']}}" name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            @endif
                        @else
                            @if($showData['norms'])
                                @foreach( $showData['norms'] as $k=> $v)
                                    <div  id="del{{$k + 200}}" >
                                        <div class="delete-but" onclick ="$.deletebut({{$k + 200}})">
                                            <i class="icon iconfont right-ico">&#xe619;</i>
                                        </div>
                                        <ul class="goods-editer-b s-goods-editer-b">
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">型&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号:</div>
                                                        <div class="item-input">
                                                            <input type="text" class="f14" placeholder="尺寸，颜色，大小等" name="norms[{{$k + 200}}][name]" id="norms" value="{{$v['name']}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                                        <div class="item-input">
                                                            <input type="text" class="f14" placeholder="请输入金额（元）"  name="norms[{{$k + 200}}][price]" id="price" value="{{$v['price']}}">
                                                        </div>
                                                        <span class="unit">元</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="item-content">
                                                    <div class="item-inner">
                                                        <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                                        <div class="item-input">
                                                            <input type="text" class="f14" name="norms[{{$k + 200}}][stock]" placeholder="必须是数字"  id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{$v['stock']}}">
                                                        </div>

                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <ul class="goods-editer-b h-goods-editer-b">
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                                <div class="item-input">
                                                    <input type="text" class="f14" placeholder="请输入金额（元）"  name="price" id="price" value="{{$showData['price']}}">
                                                </div>
                                                <span class="unit">元</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="item-content">
                                            <div class="item-inner">
                                                <div class="item-title label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</div>
                                                <div class="item-input">
                                                    <input type="text"class="f14"  placeholder="必须是数字"   value="{{$showData['stock']}}" name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            @endif
                        @endif
                    </div>
                    <div class="add_goods_specifications w_b">
                        <i class="icon iconfont">&#xe618;</i>
                        <p class="w_b_f_1 f14">添加商品规格</p>
                    </div>
                    <div class="list-block" style="margin-bottom:0;">
                        <div class="item-title label y-spadd" style="width:98%;">
                            <p class="content-block-title m10 f14">描述:</p>
                            <div style="width:100%;height:200px;">
                                <script id="editor" name="brief" type="text/plain">{!!$data['brief'] or $showData['brief']!!}</script>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="list-block">
                        <ul>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">服务名称:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="必填" name="name" value="{{$data['name'] or $showData['name']}}">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="y-spbq" @if($data == "") onclick="$.showData()" @else @if($data['systemGoodsId'] ==  0 || $data['systemGoodsId'] ==  "" ) onclick="JumpURL('{!! u('Seller/getTagLists',['nav_back_url'=>$nav_back_url]) !!}','{{$css}}',2)" @else  onclick="$.showSystemMsg()"  @endif @endif>
                        <span>商品标签:</span>
                <span class="fr">
                    @if($tag)
                        <span id="systemTagList">{{$tag['pid']['name'] or '未选择'}}</span> - <span id="systemTag">{{$tag['name'] or '未选择'}}</span>
                    @else
                        <span id="systemTagList">请选择分类</span>
                    @endif

                    <i class="icon iconfont">&#xe64b;</i>
                </span>
                        <input type="hidden" name="systemTagListPid" id="systemTagListPid" value="{{$tag['pid']['id'] or 0}}">
                        <input type="hidden" name="systemTagListId" id="systemTagListId" value="{{$tag['id'] or 0}}">
                    </div>
                    <div class="list-block">
                        <ul>
                            <!-- Text inputs -->
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入价格" name="price" value="{{$data['price'] or $showData['price']}}">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label">时&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;长:</div>
                                        <div class="item-input">
                                            <input type="text" placeholder="请输入时长" name="duration" value="{{$data['duration'] or $showData['price']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
                                        </div>
                                        <span class="unit">分钟</span>
                                    </div>
                                </div>
                            </li>
                            <li onclick="showStaffIframe();">
                                <div class="item-content">
                                    <a href="#">
                                        <div class="item-inner">
                                            <div class="item-title label">服务人员:</div>
                                            <div class="item-input">
                                                <input type="text" name="staffName" readonly="readonly" placeholder="请选择" value="{{$data['allStaffName'] or $showData['allStaffName']}}"><!--禁止输入-->
                                                <input type="hidden" name="staffId" value="{{$data['allStaffId'] or $showData['allStaffId']}} "/>
                                            </div>
                                            <span class="icon iconfont unit">&#xe64b;</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="list-block" style="margin-bottom:0;">
                        <ul>
                            <li class="align-top">
                                <div class="item-content">
                                    <div class="item-inner">
                                        <div class="item-title label y-spadd" style="width:98%;">
                                            <p>描述:</p>
                                            <div style="width:100%;height:200px;">
                                                <script id="editor" name="brief" type="text/plain">{!!$data['brief'] or $showData['brief']!!}</script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                @endif
            </form>
            <div class="all-add"></div>
        @stop
        @section('page_js')
            <script type="text/javascript">
                var ue = UE.getEditor('editor',{
                    toolbars:[['Undo', 'Redo']]
                });
                $(document).on('click','.image_upload', function () {
                    var thisObj = $(this); 
                    $(this).fanweImage({
                        width:320, 
                        height:320, 
                        callback:function(url, target) {
                            thisObj.get(0).src = url;
                            $("#upimage_"+thisObj.data('num')).val(url); 
                            $("#imglabel-"+thisObj.data('num')).siblings(".delete").removeClass("none"); 
                        }
                    });
                }); 
                $.showData = function(){
                    var query = $("#{{$id_action.$ajaxurl_page}} #goods-form").serialize();

                    $.ajax({
                        url: "{{u('Seller/showData')}}",
                        dataType: "json",
                        data:query,
                        type: "POST",
                        success: function(ajaxobj){
                            JumpURL("{!! u('Seller/getTagLists',['nav_back_url'=>$nav_back_url]) !!}",'{{$css}}',2);
                        }
                    });
                } 

                //评价页面照片删除
                $(document).off("touchend",".delete");
                $(document).on("touchend",".delete",function(){
                    $(this).parents("li").find("img").attr("src", "{{asset('wap/community/client/images/addpic.png')}}");
                    $(this).addClass("none");
                    $("#upimage_" + $(this).data('id')).val("");
                    return false;
                });


                $.showSystemMsg = function (){
                    $.toast("平台商品禁止修改标签");
                }
                var winH = $(".page-group").height(),conth = $(window).height();
                $("input").live("click",function(){
                    if($.device['os'] == 'ios'){
                    }else{
                        $(".content").scrollTop($(this).offset().top+$(".content").scrollTop()-50);
                    }
                });
                $(window).resize(function(){
                    if(conth == $(window).height()){
                        $("textarea,input").blur();
                    }
                });
                $("textarea,input").blur(function(){
                    $(".content").css("padding-bottom",0);
                    winH = $(".page-group").height();
                });
                ue.ready(function() {
                    UE.getEditor('editor').setHeight(168);
                    var interalTimer = setInterval(function(){
                        if(ue.isFocus()){
                            $(".y-spadd div").eq(0).css({"bottom":"0","position":"fixed","z-index":16,"width":"100%","height":"70%","background":"#ffffff"});
                        }else{
                            $(".y-spadd div").eq(0).css({"position":"static"});
                        }
                    },300)

//            $(UE.getEditor('editor').iframe.contentWindow.document).keydown(function(event){
//                 if(event.keyCode == ""){
//                     $(".y-spadd div").eq(0).css({"position":"static"});
//                 }
//            });
                });

            //分销模式
            $(document).off('click','.passage_name');
            $(document).on('click','.passage_name', function () {
              var buttons1 = [
                {
                    text: '分销通道',
                    label: true
                },
                @foreach($passageId as $key => $value)
                {
                    text: "{{$value['name']}}",
                    bold: true,
                    color: 'danger',
                    onClick: function() {
                        $(".passage_id").val("{{$value['id']}}");
                        $(".passage_name").val("{{$value['name']}}");
                    }
                },
                @endforeach
              ];
              var buttons2 = [
                {
                  text: '取消',
                  bg: 'danger'
                }
              ];
              var groups = [buttons1, buttons2];
              $.actions(groups);
            });

            //分销模式
            $(document).off('click','.scheme_name');
            $(document).on('click','.scheme_name', function () {
              var buttons1 = [
                {
                    text: '分销方案',
                    label: true
                },
                @foreach($schemeId as $key => $value)
                {
                    text: "{{$value['name']}}",
                    bold: true,
                    color: 'danger',
                    onClick: function() {
                        $(".scheme_id").val("{{$value['id']}}");
                        $(".scheme_name").val("{{$value['name']}}");
                    }
                },
                @endforeach
              ];
              var buttons2 = [
                {
                  text: '取消',
                  bg: 'danger'
                }
              ];
              var groups = [buttons1, buttons2];
              $.actions(groups);
            });
            </script>
@stop
@section('show_nav')@stop