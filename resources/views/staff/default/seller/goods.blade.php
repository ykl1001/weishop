@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Seller/goodslists')}}','#seller_seller_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <div class="bar bar-header-secondary y-header-sec p0" style="background: none;">
        <div class="p075" style=" padding-bottom:0!important; overflow:hidden;">
            <input type="text" name="keywords" id="keywords_word" onkeydown="$.keywords_goods({{$status}},{{$id}},{{$args['type']}});" class="icon iconfont tc search-box-input f14" placeholder="&#xe63e; 请输入想要搜索的商品" value="{{Input::get("keywords")}}" style="line-height: 1.5rem;" />
        </div>
        <div class="management-editor plr085 clearfix">
            <span class="f_l name">商品分类列表</span>
            <div class="check_all f_l">
                <input type="radio" name="all" id="all"        class="mt "   onclick="$.checkAll('goodsId')" />
                <span  class="  focus-color-f">全选</span>
                <input type="radio" name="all" id="Checkbox1"  class="mt "   onclick="$.uncheckAll('goodsId')" />
                <span  class="  focus-color-f">全不选</span>
            </div>
            <span  class=" f_r focus-color-f" onclick="$.sellerEditor('seller_editor-but_goods')" id="seller_editor-but_goods">编辑</span>
        </div>
        <div class="buttons-tab flex" id="service">
            <div class="flex-1 p-0-085 shuxian"><a  href="#" onclick="JumpURL('{{u('Seller/goods',['status'=>1,'id'=>$id,'type'=>1])}}','#seller_goods_view_1',2)" class="tab @if($status == 1)active @endif button" style="top:0;">上架商品</a></div>
            <div class="flex-1 p-0-085"><a  href="#" onclick="JumpURL('{{u('Seller/goods',['status'=>2,'id'=>$id,'type'=>1])}}','#seller_goods_view_2',2)" class="tab button  @if($status == 2)active @endif" style="top:0;">下架商品</a></div>
        </div>
    </div>
@stop
@section('css')
@stop
@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop
@section('content')
    <div>
        <div class="tabs">
            <div id="shangjia" class="tab active">
                @if($goods)
                    <ul class="service-list lists_item_ajax" style="overflow: hidden;padding-bottom: 1rem;">
                        @include('staff.default.seller.service_item')
                    </ul>
                @else
                    <div class="x-null tc"  style="top:40%">
                        <i class="icon iconfont">&#xe60c;</i>
                        <p>很抱歉，暂无商品</p>
                    </div>
                @endif
                <div class="flex service-deal hide">
                    <div class="flex-1 tc">
                        @if($status == 1)
                            <i class="icon iconfont">&#xe67e;</i>
                            <span class="focus-color-f" id="op-goods" onclick="$.opgoods_all(2,{{$id}},{{$status}},{{$args['type']}})" data-type="2" data-goodstype="1">下架</span>
                        @else
                            <i class="icon iconfont">&#xe67f;</i>
                            <span class="focus-color-f" id="op-goods"  onclick="$.opgoods_all(1,{{$id}},{{$status}},{{$args['type']}})"  data-type="1" data-goodstype="1">上架</span>
                        @endif
                    </div>
                    <div class="flex-1 tc" id="op-del" data-type="3" onclick="$.opgoods_all(3,{{$id}},{{$status}},{{$args['type']}})"  >
                        <i class="icon iconfont">&#xe61e;</i>
                        <span>删除</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function(){
            //js获取高度
            var y_height = $(".y-header-sec .p075").height();
            y_height += $(".y-header-sec .management-editor").height();
            y_height += $(".y-header-sec .buttons-tab").height();
            $(".y-header-sec.bar-header-secondary").css("height",y_height);
            y_height += $(".bar-nav").height();
            $(".content").css("top",y_height);

			data.goodsEdieIsCK = 0;
			data.goodsEdieType = 0;

            //复选框全选
            $.checkAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    if (roomids.item(j).checked == false) {
                        roomids.item(j).checked = true;
                    }
                }
				data.goodsEdieIsCK = 0;
            }

            //复选框全不选
            $.uncheckAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    if (roomids.item(j).checked == true) {
                        roomids.item(j).checked = false;
                    }
                }
				data.goodsEdieIsCK = 1;
            }

            //复选框选择转换
            $.switchAll = function (formvalue) {
                var roomids = document.getElementsByName(formvalue);
                for ( var j = 0; j < roomids.length; j++) {
                    roomids.item(j).checked = !roomids.item(j).checked;
                }
            }


			$.sellerEditor = function (formvalue) {
				if($("#{{$id_action.$ajaxurl_page}} #"+formvalue).hasClass("ongoing"))
				{
					data.goodsEdieType = 0;
					$("#{{$id_action.$ajaxurl_page}} #"+formvalue).html("编辑").css("color","#ff2c4c").removeClass("ongoing");
					$("#{{$id_action.$ajaxurl_page}} .management-editor .name").show().siblings(".check_all").hide();
					$("#{{$id_action.$ajaxurl_page}} .service-list li input,.service-list li .big").hide().siblings(".img_box").show();
					$("#{{$id_action.$ajaxurl_page}} div.service-deal").addClass("hide");
					$("#{{$id_action.$ajaxurl_page}} nav.bar-tab").toggleClass("hide");
					$("#{{$id_action.$ajaxurl_page}} .content").css("bottom","2.5rem");
                    $("#{{$id_action.$ajaxurl_page}} .sales").removeClass("y-liwai");
				}
				else
				{
					data.goodsEdieType = 1;
					$("#{{$id_action.$ajaxurl_page}} #"+formvalue).html("完成").css("color","#a9a9a9").addClass("ongoing");
					$("#{{$id_action.$ajaxurl_page}} .management-editor .name").hide().siblings(".check_all").show();
					$("#{{$id_action.$ajaxurl_page}} .service-list li input,.service-list li .big").show().siblings(".img_box").hide();
					$("#{{$id_action.$ajaxurl_page}} div.service-deal").removeClass("hide");
					$("#{{$id_action.$ajaxurl_page}} nav.bar-tab").toggleClass("hide");
					$("#{{$id_action.$ajaxurl_page}} .content").css("bottom",0);
                    $("#{{$id_action.$ajaxurl_page}} .sales").addClass("y-liwai");
				}
			}

        });
    </script>
@stop
@section('preloader')@stop
