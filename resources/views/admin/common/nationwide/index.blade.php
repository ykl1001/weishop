@extends('admin._layouts.base')
@section('css')
    <style type="text/css">
        .ssshow{
            height: 50px;
            line-height: 50px;
            width: 100%;
            text-align: center;
            color: #bbb;
            font-size: 12px;
        }
        .notice{
            width:100%;
            height:40px;
            background:#FFFD86;
            line-height: 40px;
            margin-bottom: 5px;
            text-indent: 1em;
        }
        .notice b{
            color:red;
        }
    </style>
@stop
@section('right_content')
    @yizan_begin
    <!-- 列表 -->
    <yz:list>
        <!-- 搜索 -->
        <search url="{{ $url }}">
            <row>
                <item name="orderSn" label="订单号"></item>
                <item name="mobile" label="会员手机号"></item>
                <item name="beginTime" label="开始时间" type="date"></item>
                <item name="endTime" label="结束时间" type="date"></item>
                <btn type="search"></btn>
            </row>
        </search>
        <table>
            <rows>
                <headers>
                    <header label="订单号"></header>
                    <header label="退款金额"></header>
                    <header label="退款类型"></header>
                    <header label="申请时间"></header>
                    <header label="状态"  width="200"></header>
                    <header label="操作"  width="100"></header>
                </headers>
                <row>
                    <tr class="{{ $list_item_css }}" style="text-align:left;">
                        <td>
                            <p>
                                @if($list_item['orderId'] > 0)
                                    @if($list_item['orderType'] == 1)
                                        <a href="{{ u('Order/detail',['id'=>$list_item['orderId']]) }}">{{ $list_item['sn'] }}</a>
                                    @else
                                        <a href="{{ u('ServiceOrder/detail',['id'=>$list_item['orderId']]) }}">{{ $list_item['sn'] }}</a>
                                    @endif
                                @else
                                    <a href="#">{{ $list_item['sn'] }}</a>
                                @endif</p>
                        </td>
                        <td>
                            <p>{{$list_item['money']}}</p>
                        </td>
                        <td>
                            <p>
                                @if($list_item['refundType'] == 1)
                                    退款退货
                                @else
                                    仅退款
                                @endif
                            </p>
                        </td>
                        <td>
                            <p>{{ yztime( $list_item['createTime'] ) }}</p>
                        </td>
                        <td rowspan="3">
                            {{Lang::get("api_system.refund.".$list_item['status'])}}
                        </td>
                        <td rowspan="3">
                            @if( $list_item['refundType'] != 0 && $list_item['status'] == 4 )
                                <p><a href="#" onclick="$.udbShowRefund({{$list_item['id']}})">确认退款</a></p>
                                <br/>
                                <p><a href="#" class="red"  onclick="$.udbShowRefunds({{$list_item['id']}})">拒绝退款</a></p>
                            @endif
                            @if( $list_item['refundType'] == 0 && $list_item['status'] == 1 )
                                <p><a href="#" onclick="$.udbShowRefund({{$list_item['id']}})">确认退款</a></p>
                                <br/>
                                <p><a href="#" class="red"  onclick="$.udbShowRefunds({{$list_item['id']}})">拒绝退款</a></p>
                            @endif
                            @if($list_item['status'] == 5 )
                                <p><a href="{{u('UserRefund/index')}}?status=1&nav=2">查看</a></p>
                            @endif
                            @if($list_item['status'] == 6 )
                                <p><a href="{{u('UserRefund/index')}}?status=0&nav=1">查看</a></p>
                            @endif
                        </td>
                    </tr>
                    <tr class="{{ $list_item_css }}" style="text-align:left;">
                        <td colspan="4" style="text-align:left;">
                            <p><b>退款原因：</b>{{ yzHtmlSpecialcharsDecode( $list_item['content'] ) }}</p>
                            @if( $list_item['refundExplain'])
                                <p>
                                    <b>退款说明：</b>{{ yzHtmlSpecialcharsDecode( $list_item['refundExplain'] ) }}</p>
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr class="{{ $list_item_css }}" style="text-align:left;">
                        <td colspan="4" style="text-align:left;">
                            <p><b>退款图片：</b></p>
                            <p>
                                @foreach($list_item['images'] as $v)
                                    <a href="{{ $v }}" target="_blank" class="goodstable_img fl">
                                        <img src="{{formatImage($v,80,80)}}" alt="">
                                    </a>
                                @endforeach
                             </p>
                        </td>
                    </tr>
                </row>
            </rows>
        </table>
    </yz:list>
    @yizan_end
@stop
@section('js')
    <script type="text/tpl" id="serviceContent">
	<div style="width:300px;text-align:center;padding:10px;">
        <ul class="y-cancelreason tl f13">
           <textarea id="cancelreasontext" placeholder="请输入原因" maxlength="200" class="y-otherreasons c-gray "></textarea>
        </ul>
	</div>
</script>
    <script type="text/javascript">
        $(function(){
            $.udbShowRefund = function(id){
                $.post("{{u('Nationwide/disposesave')}}",{id:id,status : 5},function(res){
                     if(res.code == 0){
                         if(res.data.status == 0 ){
                             window.location =  '{{u('UserRefund/index')}}?status=0&nav=1';
                         }else{
                             window.location =  '{{u('UserRefund/index')}}?status=1&nav=2';
                         }
                     }else{
                         $.ShowAlert(res.msg);
                     }
                });
            }
            $.udbShowRefunds = function(id){

                var dialog = $.zydialogs.open($("#serviceContent").html(), {
                    boxid:'SET_GROUP_WEEBOX',
                    width:300,
                    title:'拒绝理由',
                    showClose:true,
                    showButton:true,
                    showOk:true,
                    showCancel:true,
                    okBtnName: '确认理由',
                    cancelBtnName: '取消',
                    contentType:'content',
                    onOk: function(){
                        var refuseContent = $("#cancelreasontext").val();

                        if(refuseContent != ""){
                            dialog.setLoading();
                            $.post("{{u('Nationwide/disposesave')}}",{id:id,status:6,content:refuseContent},function(res){
                                if(res.code == 0){
                                    location.reload();
                                }else{
                                    $.ShowAlert(res.msg);
                                }
                            },'json');
                        }else{
                            $.ShowAlert("请输入理由");
                        }
                        dialog.setLoading(false);
                    },
                    onCancel:function(){
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                });
            }
            $('#yzForm').submit(function(){
                var beginTime = $("#beginTime").val();
                var endTime = $("#endTime").val();
                if(beginTime!='' || endTime!='') {
                    if(beginTime==''){
                        alert("开始时间不能为空");return false;
                    }
                    else if(endTime==''){
                        alert("结束时间不能为空");return false;
                    }
                    else if(endTime < beginTime){
                        alert("开始时间不能大于结束时间");return false;
                    }
                }
            });
        });
    </script>
@stop 