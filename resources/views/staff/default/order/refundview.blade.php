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
    @if($data['status'] == 0)
    <div class="bar bar-tab y-agreerefund">
        <a href="#" class="button button-light" id="udb_refund_btn_n" data-status="2">拒绝退款</a>
        <a href="#" id="udb_refund_btn_y" class="button button-light" data-status="1">同意退款</a>
    </div>
    @endif
    @if($data['status'] == 3 && $data['refundType'] == 1)
    <div class="bar bar-tab y-agreerefund">
        <a href="#" class="button button-light" id="udb_refund_btn_yes" data-status="4">确定收货</a>
    </div>
    @endif
@stop
@section('contentcss')bg_fff @stop

@section('content')
    <ul class="y-refunddetails">
        @if($refund['stepThree']['status'] == 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">退款成功</span>
                    <span class="f_999 f12">{{$refund['stepThree']['time']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>{{$refund['stepThree']['brief']}}</p>
                </div>
            </li>
        @endif
        @if(in_array($data['status'],[5]))
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">平台核审通过</span>
                    <span class="f_999 f12">{{$data['adminDisposeTime']}}</span>
                </div>
            </li>
        @endif
        @if(in_array($data['status'],[6]))
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">平台核审拒绝</span>
                    <span class="f_999 f12">{{$data['adminDisposeTime']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>{{$data['adminDisposeContent']}}</p>
                </div>
            </li>
        @endif

        @if(in_array($data['status'],[4]) && $data['refundType'] == 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">退款中</span>
                    <span class="f_999 f12">{{$data['staffDisposeTime']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>待平台处理</p>
                </div>
            </li>
        @endif

        @if(in_array($data['status'],[3,4,5,6]) &&  $data['refundType']== 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">买家已退货</span>
                    <span class="f_999 f12">{{$data['userDisposeTime']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>物流公司：{{$data['userDisposeName']}}</p>
                    <p>物流单号：{{$data['userDisposeNumber']}}</p>
                    <p>物流公司：退货凭证</p>
                    @if($data['userDisposeImages'])
                        <p class="mt5 y-average">
                            @foreach($data['userDisposeImages'] as $img)
                                <img src="{{$img}}" width="24%" class="vat">
                            @endforeach
                        </p>
                    @endif
                </div>
            </li>
        @endif
        @if($data['status'] == 1 && $data['refundType'] != 1)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">平台处理中</span>
                </div>
                <div class="f_999 f12">
                    <p>请勿相信任何人给您发来的可以退款的链接，以免钱款被骗。</p>
                </div>
            </li>
        @endif
        @if(in_array($data['status'],[1,3,4,5,6]))
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家处理通过</span>
                    <span class="f_999 f12">{{$data['sellerDisposeTime']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>退货地址：{{$data['sellerAddress']}}</p>
                    @if($data['refundType'] == 1)
                        <p>商家同意了本次今后服务申请。请将退货商品包装好，且商品不影响二次销售；请勿发平邮或到付件，商品寄出后，需及时在每笔退款上操作“填写物流信息”，以免影响退款进度</p>
                    @else
                        <p>本次退款申请达成</p>
                    @endif
                </div>
            </li>
        @endif

        @if($data['status']  == 2)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">退款失败</span>
                    <span class="f_999 f12">{{$data['sellerDisposeTime']}}</span>
                </div>
            </li>
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家拒绝</span>
                    <span class="f_999 f12">{{$data['sellerDisposeTime']}}</span>
                </div>
                <div class="f_999 f12">
                    <p>您拒绝了本次退款申请</p>
                    @if($data['sellerDisposeImages'])
                        <p class="mt5 y-average">
                            @if($data['sellerDisposeImages'][0])
                                @foreach($data['sellerDisposeImages'] as $img)
                                    <img src="{{$img}}" width="24%" class="vat">
                                @endforeach
                            @endif
                        </p>
                    @endif
                </div>
            </li>
        @endif

        @if($data['status'] == 0)
            <li>
                <div class="y-stepnumber"></div>
                <div class="y-titlerow">
                    <span class="y-refundtitle">商家处理中</span>
                </div>
            </li>
        @endif
        <li>
            <div class="y-stepnumber"></div>
            <div class="y-titlerow">
                <span class="y-refundtitle">{{$data['order']['users']['name']}}:发起了申请</span>
                <span class="c-gray f12">{{$data['createTime']}}</span>
            </div>
            <div class="f_999 f12">
                <p>发起了@if($data['refundType'] == 1)退款退货@else仅退款@endif申请，原因:{{$data['content']}}@if($data['refundExplain'])，说明：{{$data['refundExplain']}}@endif</p>

                @if($data['images'][0])
                    <p class="mt5 y-average">
                        @foreach($data['images'] as $img)
                            <img src="{{$img}}" width="24%" class="vat">
                        @endforeach
                    </p>
                @endif
            </div>
        </li>
    </ul>
@stop
@section('show_nav')@stop
@section('page_js')
    <script type="text/javascript">
        Zepto(function($) {
            var type = "{{$data['refundType']}}"
            $("#udb_refund_btn_y,#udb_refund_btn_n,#udb_refund_btn_yes").click(function(){
                var status = $(this).data('status');

                if(status == 2){
                    JumpURL('{{ u('Order/refunddispose',['id' => $data['orderId']]) }}','order_refunddispose_viwe',2)
                }else{
                    var title = "同意退款"
                    var msg = "同意退款以后￥{{$data['money']}}将退回买家帐号"
                    if(status == 4){
                        title = "确认收货";
                        msg = "提交后由平台处理退款"
                    }
                    var id = {{$data['id']}};
                    var orderId = {{$data['orderId']}};
                    $.modal({
                        title: title,
                        text:msg,
                        buttons: [
                            {text: '取消'},
                            {
                                text: '确定',
                                bold:true,
                                onClick: function() {
                                    $.post("{{u('Order/refund')}}",{id:id,orderId:orderId,status:status},function(res){
                                       if(res.msg == null){
                                           $.toast("操作成功");
                                           JumpURL('{{ u('Order/detail',['id' => $data['orderId']]) }}','order_detail_viwe',2)
                                       }else{
                                           $.toast(res.msg);
                                       }
                                    });
                                }
                            }
                        ]
                    })
                }
            });
        });

        $.clickjsjump = function(){
            var number = $('#number').val();
            JumpURL('{{ u('logisticsCompany/index',['id' => $data['id']]) }}&number='+number,'logisticsCompany_index_viwe',2)
        }
    </script>
@stop

