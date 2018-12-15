@extends('admin._layouts.base')
@section('css')
@stop

<?php
$type = [
        '0' => "初级",
        '1' => "I级",
        '2' => "II级",
        '3' => "III级",
];
?>
@section('right_content')
    @yizan_begin
    <yz:list>
        <search>
            <row>
                <item name="sn" label="订单号"></item>
                <item name="userName" label="会员"></item>
            </row>
            <row>
                <item label="状态">
                    <yz:select name="status" options="0,1,2" texts="全部,未完结,已完结" selected="$search_args['status']"></yz:select>
                </item>
                <input type="hidden" name="invitationId" value="{{$search_args['invitationId']}}" />
                <btn type="search"></btn>
            </row>
        </search>
        <table>
            <columns>
                <column code="sn" label="订单号" width="190"></column>
                <column code="totalFee" label="订单金额" >{{'￥'.$list_item['payFee']}}</column>
                <column code="sellerFullSubsidy" label="商家满减补贴" >{{'￥'.$list_item['sellerFullSubsidy']}}</column>
                <column code="activityGoodsMoney" label="商家商品优惠" >{{'￥'.$list_item['activityGoodsMoney']}}</column>
                <column code="returnMoney" label="佣金" >{{'￥'.$list_item['returnMoney']}}</column>
                <column code="level" label="推荐级别" >{{$type[$list_item['level']]}}</column>
                <column code="percent" label="比率" >{{$list_item['percent'].'%'}}</column>
                <column code="userName" label="购买会员" ></column>
                <column code="orderStatus" label="订单状态" >
                    @if($list_item['orderStatus'])
                        已完结
                    @else
                        @if($list_item['status'] == '404' ||  (int)$list_item['cancelTime'] > 0)
                            平台退回
                        @else
                            未完结
                        @endif
                    @endif
                </column>
                <column  label="是否退款" >{{ $list_item['status'] == '404' || (int)$list_item['cancelTime'] > 0 ? '是' : '否' }}</column>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
@stop