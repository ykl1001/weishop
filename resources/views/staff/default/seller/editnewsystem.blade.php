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
    </style>
@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ u('Seller/commodity',['systemTagListPid' => $args['systemTagListPid'],'systemTagListId' => $args['systemTagListId'],'type' => 1,'tradeId'=>$args['tradeId'] ]) }}','#seller_goods_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <span class="button button-link button-nav f_r" onclick="$.goodssave({{ $args['tradeId']}},1) ">
            完成
        </span>
        <h1 class="title">{{$title}}</h1>
    </header>
@stop
@section('css')
@stop
@section('distance')id="service-add" @stop
@section('content')
    @yizan_begin
    <form action="javascript:;" id="img">
		<label id="imglabel" class="img-up-lb" for="upload_but"  style="display:inline-block;width:100%;height:10rem;position:absolute;z-index:10000;">
        <input type="file"  id="upload_but" accept="image/*" style="display:none;" />
		</label>
    </form>

    <form action="javascript:;" id="goods-form">
        <div id="preview" @if($data)style="height: 200px;" @endif>
            @if($data)
                <img id="imghead" src="{{ formatImage($data['images'][0],640,640) }}" alt="" style="width: 100%;height: 10.5rem" >
            @else
                <img id="imghead" class="imghead"  style="width: 100%;height: 10.5rem">
            @endif
            @if($data)
                <div class="upload_again">@if($args['type'] == 1)点击上传商品图片@else点击上传服务图片@endif</div>
            @else
                <div class="upload_instructions">
                    <i class="icon iconfont right-ico">&#xe689;</i>
                    <p>点击上传图片</p>
                </div>
            @endif
        </div>
        <input type="hidden" name="imgs[]" id="imgs" value="{{ $data['images'][0] }}"/>
        <input type="hidden" name="type" value="{{ $data['type'] or $args['type'] }}"/>
        <input type="hidden" value="{{ $data['cateId'] or $args['tradeId']}}" name="tradeId" />
        <input type="hidden" name="systemGoodsId" id="{{$data['id']}}" value="{{$data['id']}}">
        <div class="list-block">
            <ul>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label">商品名称:</div>
                            <div class="item-input">
                                <input type="text" placeholder="必填" name="name" id="name" value="{{$data['name']}}">
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label">商品编码:</div>
                            <div class="item-input">
                                <input type="text" class="f14" placeholder="请输入1-16位商品编码" name="goodsSn" id="goodsSn" maxlength="16" onKeyUp="value=value.replace(/[\W]/g,'')" value="">
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="y-spbq" onclick="$.showSystemMsg()" >
            <span>商品标签:</span>
            <span class="fr">
                    <span id="systemTagList">{{$tag['pid']['name'] or '未选择'}}</span> - <span id="systemTag">{{$tag['name'] or '未选择'}}</span>
                    <input type="hidden" name="systemTagListPid" id="systemTagListPid" value="{{$data['systemTagListPid'] or 0}}">
                    <input type="hidden" name="systemTagListId" id="systemTagListId" value="{{$data['systemTagListId'] or 0}}">
                <i class="icon iconfont">&#xe64b;</i>
            </span>
        </div>
        <div class="list-block add-b @if($data) @if($data['norms'])add-block @endif @endif show_norms">
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
                                                <input type="text" placeholder="尺寸，颜色，大小等" name="norms[{{$k}}][name]" id="norms" value="{{$v['name']}}">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="item-content">
                                        <div class="item-inner">
                                            <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                            <div class="item-input">
                                                <input type="text" placeholder="请输入金额（元）"  name="norms[{{$k}}][price]" id="price" value="{{$v['price']}}">
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
                                                <input type="text" name="norms[{{$k}}][stock]" placeholder="必须是数字"  id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" value="{{$v['stock']}}">
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
                                        <input type="text" placeholder="请输入金额（元）"  name="price" id="price" value="{{$data['price']}}">
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
                                        <input type="text" placeholder="必须是数字"   value="{{$data['stock']}}" name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                @endif
            @else
                <ul class="goods-editer-b h-goods-editer-b">
                    <li>
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="item-title label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</div>
                                <div class="item-input">
                                    <input type="text" placeholder="请输入金额（元）"  name="price" id="price">
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
                                    <input type="text" placeholder="必须是数字"  name="stock" id="stock" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" >
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            @endif
        </div>
        <div class="add_goods_specifications w_b">
            <i class="icon iconfont">&#xe618;</i>
            <p class="w_b_f_1">添加商品规格</p>
        </div>
        <div class="list-block">
            <ul>
                <li class="align-top">
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title label y-spadd">
                                <p>描述:</p>
                                <div style="   width:100%;			height:400px;">
                                    <script id="editor" name="brief" type="text/plain">{!!$data['brief']!!}</script>
                                </div>
                                <script type="text/javascript">
                                    //实例化编辑器
                                    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
                                    var ue = UE.getEditor('editor',{
                                        toolbars:[['Source', 'Undo', 'Redo']]
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
    <div class="blank0825"></div>
    <div class="all-add"></div>
    @yizan_end
@stop
@section($js)

    <script type="text/javascript">
		function CutCallBack(base64Img){
			if(base64Img.length <= 0){
				$.alert("获取图片失败！");
			}else{
				
				$("#upload_but").fanweImage({
						width:640,
						height:640,
						callback:function(result) {
						var div = document.getElementById('preview');
						var upload_but=document.getElementById('upload_but');
						var img = document.getElementById('imghead');
						div.innerHTML ='<img id="imghead" src="'+result+'" style="width:100%"><div class=upload_again>点击上传商品图片</div>';
						div.style.height='200px';
						upload_but.style.height='200px';

						var imgs=document.getElementById('imgs');
						imgs.value = result;
						}
				});
				
			}
		}
		if(window.App){
			$("#imglabel").removeAttr("for").bind('click',function(){
			App.CutPhoto('{"w":'+ 600 +',"h":'+600+'}');
			});
		}else{
			$(document).on('change', "#upload_but", function(){
				$.showIndicator();
				$(this).fanweImage({
						width:640,
						height:640,
						callback:function(result) {
						var div = document.getElementById('preview');
						var upload_but=document.getElementById('upload_but');
						var img = document.getElementById('imghead');
						div.innerHTML ='<img id="imghead" src="'+result+'" style="width:100%"><div class=upload_again>点击上传商品图片</div>';
						div.style.height='200px';
						upload_but.style.height='200px';

						var imgs=document.getElementById('imgs');
						imgs.value = result;
						}
				})

			});
		}
        $.showSystemMsg = function (){
            $.toast("平台商品禁止修改标签");
        }
    </script>
@stop
@section('show_nav')@stop