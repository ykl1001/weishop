@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{ $nav_back_url }}','{{ $url_css }}',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    @if($data['isCanAccept'] || $data['isPay'] || $data['isLogistics']|| $data['isRefund'])
        <div class="bar bar-tab y-agreerefund">
            @if($data['isCancfOrder'] && in_array($role,[1,3,5,7]) )
                <a href="#" id="isCanAccept" onclick="$.isCanCancel({{$data['id']}},2 )" class="button f12 y-bgff2d4b ml10">确定取消</a>
            @endif
            @if($data['isPay'])
                <a href="#" onclick="$.isCanCancel({{$data['id']}},1 )" class="button button-light">关闭订单</a>
            @endif
            @if($data['isCanAccept'])
                <a href="#" onclick="JumpURL('{{u('Order/deliver',['id'=>$data['id']])}}','#order_deliver_view',2)" class="button button-light c-bgred">发货</a>
            @endif
            @if($data['isLogistics'])
                <a href="#" onclick="JumpURL('{{u('Order/logistics',['id'=>$data['id']])}}','#order_logistics_view',2)" class="button button-light"external>查看物流</a>
            @endif
            @if($data['isRefund'])
                <a href="#" onclick="JumpURL('{{u('Order/refundview',['id'=>$data['id']])}}','#order_refundview_view',2)" class="button button-light"external>{{$data['orderNewStatusStr']['title']}}</a>
            @endif
        </div>
     @endif
@stop
@section('contentcss')pull-to-refresh-content @stop
@section('distance')data-ptr-distance="20" @stop
@section('content')
    @include('staff.default._layouts.refresh')
    <div class="lists_item_ajax">
        @include("staff.default.order.store_detail_item")
    </div>
@stop
@section($js)
    <script type="text/javascript">
        $(function() {
            if($(".udb_dsy_item_li .item-title-row").length <= 0){
                $(".udb_dsy_item_li").remove();
            }
            //取消
            $.isCanCancel = function (id, status) {
                $.modal({
                    title:  '是否同意取消',
                    text: "会员申请取消订单，是否同意",
                    buttons: [
                        {text: '取消'},
                        {
                            text: '确定',
                            bold:true,
                            onClick: function() {
                                $.status(id, {{ORDER_STATUS_CANCEL_USER_SELLER}});
                            }
                        }
                    ]
                })
            }
            $.status = function (id, status) {
                $.showIndicator();
                $.post("{{ u('Order/orderReceiving') }}", {'id': id, 'status': status}, function (res) {
                    if(res.code != 0){
                        $.toast(res.msg);
                        $.hideIndicator();
                        return false;
                    }
                    if("{{explode("_",$id_action)[0]}}" == "order"){
                        JumpURL('{{ u('Order/detail',['id'=>$data['id']]) }}','#order_detail_view',1);
                    }else{
                        JumpURL('{{ u('Index/detail',['id'=>$data['id']]) }}','#index_detail_view',1);
                    }
                    $.hideIndicator();
                }, "json");
            }
        });
    </script>
@stop

@section('show_nav')@stop