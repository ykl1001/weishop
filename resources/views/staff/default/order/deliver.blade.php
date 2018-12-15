@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <style type="text/css">
        .p0{padding: 0;}
        .mt0{margin-top: 0;}
        .bar-footer{height: 3rem;}
        .bar-footer~.content{bottom: 3rem;}
        .actions-modal-button{color:#313233;font-size: 16px;}
    </style>
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <div class="bar bar-tab y-orderbutton">
        <a href="#" class="button bg_red f_fff udb_show_bnt" data-false="false">确认发货</a>
    </div>
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
        <div class="list-block y-ulnobor y-sptj">

            <ul id="show_0">
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">发货方式</div>
                        <div onclick="$.clickfhfs()" class="item-after f13">
                            物流公司<i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">物流公司</div>
                        <div onclick="$.clickjsjump()" class="item-after f_aaa f13">
                            @if(!empty($args['keycode'])){{$args['keycode']}}@else选择物流@endif<i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </li>
                <li class="item-content pl0">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">发货单号</div>
                        <div class="item-after f_aaa"><input type="text" class="tr f13" placeholder="请输入快递单号" id="number" @if(!empty($args['number']))value="{{$args['number']}}"@else @endif></div>
                    </div>
                </li>
            </ul>

            <ul id="show_1" style="display: none">
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">发货方式</div>
                        <div onclick="$.clickfhfs()" class="item-after f13">
                            其他物流<i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">物流公司</div>
                        <div class="item-after f_aaa f13">
                            <input type="text" class="tr f13" placeholder="请输入物流公司" id="company">
                        </div>
                    </div>
                </li>
                <li class="item-content pl0">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">发货单号</div>
                        <div class="item-after f_aaa"><input type="text" class="tr f13" placeholder="请输入快递单号" id="number1" @if(!empty($args['number']))value="{{$args['number']}}"@else @endif></div>
                    </div>
                </li>
            </ul>

            <ul id="show_2" style="display: none">
                <li class="item-content">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">发货方式</div>
                        <div onclick="$.clickfhfs()" class="item-after f13">
                            无需物流<i class="icon iconfont ml5 f14">&#xe64b;</i>
                        </div>
                    </div>
                </li>
                <li class="item-content pl0">
                    <div class="item-inner">
                        <div class="item-title f_5e f13">备注内容</div>
                        <div class="item-after f_aaa" style="width: 70%;"><input type="text" class="tr f13" placeholder="请输入备注内容" id="remark"></div>
                    </div>
                </li>
            </ul>
            <input type="hidden" class="tr f13"  id="type" value="0">
        </div>
        <div class="content-block-title m10 f_999 f12">订单详情</div>
        <div class="list-block">
            <ul>
                <li class="item-content p0">
                    <div class="item-inner pl10">
                        <div class="f14 w100">
                            <span class="f_l">订&nbsp;单&nbsp;号:</span>
                            <div class="y-xddxqcont f_999">{{$data['sn']}}</div>
                        </div>
                    </div>
                </li>
                <li class="item-content p0">
                    <div class="item-inner pl10">
                        <div class="f14 w100">
                            <span class="f_l">收货地址：</span>
                            <div class="y-xddxqcont f_999">
                                <p><span class="mr10">{{$data['name']}}</span><span>{{$data['mobile']}}</span></p>
                                <p>{{$data['province'] . $data['city'] . $data['area'] . $data['address']}}</p>
                            </div>
                        </div>
                    </div>
                </li>
                @if($data['buyRemark'])
                    <li class="item-content p0">
                        <div class="item-inner pr10 pl10">
                            <div class="f14 w100">
                                <span class="f_l">备注信息：</span>
                                <div class="y-xddxqcont f_999">{{$data['buyRemark']}}</div>
                            </div>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        <div class="content-block-title m10 f_999 f12">商品列表</div>
        <div class="list-block media-list f14 y-xddxqlist">
            <ul>
                @foreach($data['orderGoods'] as $v)
                    <li>
                        <div class="item-content">
                            <div class="item-media"><a href="#"><img src="{{ formatImage($v['goodsImages'],100,100) }}" width="45.5"></a></div>
                            <div class="item-inner f12">
                                <div class="item-title-row">
                                    <a href="#"><div class="item-title">{{$v['goodsName']}}</div></a>
                                    <div class="item-after">
										@if($v['salePrice'] <= 0)
                                            <p>￥{{sprintf("%.2f",$v['price'])}}</p>
                                        @else
                                            <p>￥{{sprintf("%.2f",($v['price'] * $v['salePrice']) / 10)}}</p>
                                        @endif
                                        @if($v['salePrice'] > 0)
                                            <del class="f_999">￥{{sprintf("%.2f",$v['price'])}}</del>
                                        @endif
                                    </div>
                                </div>
                                @if($v['goodsNorms'])
                                    <div class="item-title-row f_999">
                                        <div class="item-title">{{str_replace(':','-',$v['goodsNorms']['skuName'])}}</div>
                                        <div class="item-after f_999">x{{$v['num']}}</div>
                                    </div>
                                @else
                                    <div class="item-title-row c-gray">
                                        <div class="item-title"></div>
                                        <div class="item-after c-gray">x{{$v['num']}}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
@stop
@section('show_nav')@stop
@section('page_js')
    <script type="text/javascript">
        Zepto(function($) {
            $.clickfhfs =  function () {
                var buttons1 = [
                    {
                        text: '物流公司',
                        onClick: function() {
                            $("#show_0").show();
                            $("#show_1").hide();
                            $("#show_2").hide();
                            $("#type").val(0);
                        }
                    },
                    {
                        text: '其他物流',
                        onClick: function() {
                            $("#show_0").hide();
                            $("#show_1").show();
                            $("#show_2").hide();
                            $("#type").val(1);
                        }
                    },
                    {
                        text: '无需物流',
                        onClick: function() {
                            $("#show_0").hide();
                            $("#show_1").hide();
                            $("#show_2").show();
                            $("#type").val(2);
                        }
                    }
                ];
                var buttons2 = [
                    {
                        text: '取消',
                        bg: 'danger'
                    }
                ];
                var groups = [buttons1, buttons2];
                $.actions(groups);
            }


            $(document).off('click','.udb_show_bnt');
            $(document).on('click','.udb_show_bnt', function () {
                if($(this).attr("data-false") == "true"){
                    return false;
                }
                var type = $("#type").val();
                if(type == 0){
                    var number = $('#number').val();
                    var keycode = "{{$args['keycode']}}";
                    var id = "{{$data['id']}}";
                    if(number == ''){
                        $.toast('请填写发货单号！');
                        return false;
                    }
                    if(keycode == ''){
                        $.toast('请选择物流公司！');
                        return false;
                    }
                }else if(type == 1){
                    var number = $('#number1').val();
                    var company = $('#company').val();
                    var id = "{{$data['id']}}";
                    if(number == ''){
                        $.toast('请填写发货单号！');
                        return false;
                    }
                    if(company == ''){
                        $.toast('请选择物流公司！');
                        return false;
                    }
                }else if(type == 2){
                    var remark = $('#remark').val();
                    var id = "{{$data['id']}}";
                    if(remark == ''){
                        $.toast('请填写备注内容!');
                        return false;
                    }
                }

                var thisz = $(this);
                thisz.attr("data-false","true");
                $.post("{{u('Order/postlogistics')}}",{number:number,keycode:keycode,orderId:id,remark:remark,company:company,type:type},function(result){
                    if(result.code == 0){
                       JumpURL('{{ u('Order/detail',['id' => $data['id']]) }}','order_detail_viwe',2);
                    }else if(result.code == 20002){
                        $.toast("订单已处理过了,");
                        JumpURL('{{ u('Order/detail',['id' => $data['id']]) }}','order_detail_viwe',2);
                    }else{
                        if(result.message == "fail"){
                            $.toast(result.reason);
                        }else{
                            $.toast("发货失败");
                        }
                    }
                    thisz.attr("data-false","false");
                },'json');

            });
        });

        $.clickjsjump = function(){
            var number = $('#number').val();
            JumpURL('{{ u('logisticsCompany/index',['id' => $data['id']]) }}&number='+number+'&keycode={{$args['keycode']}}','logisticsCompany_index_viwe',2)
        }
    </script>
@stop

