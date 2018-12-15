@extends('seller._layouts.base')
@section('css')
<style type="text/css">
    .m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
    <div>
        <div class="m-zjgltbg">                 
            <div class="p10">
                <!-- 服务管理 -->
                <div class="g-fwgl">
                    <p class="f-bhtt f14 clearfix">
                        <span class="ml15 fl">评价管理</span>
                    </p>
                </div>
                <!-- 服务表格 -->
                <div class="m-tab m-smfw-ser">
                    @yizan_begin
                        <yz:list>
                            <search> 
                                <row>
                                    <yz:select name="star" options="0,1,2,3,4,5" texts="全部,1星,2星,3星,4星,5星" selected="$search_args['star']"></yz:select>
                                    <item name="sn" label="订单编号" style="width:180px"></item>
                                    <item name="beginTime" label="开始时间" type="date"></item>
                                    <item name="endTime" label="结束时间" type="date"></item>
                                    <btn type="search" css="btn-gray"></btn>
                                </row>
                            </search>
                            <table css="goodstable" relmodule="GoodsSeller">
                                <columns>
                                    <column label="编号" code="id" width="80"></column>
                                    <column label="订单号" code="order.sn"></column>
                                    <column label="会员名" code="user.name"></column>
                                    <column label="店铺评分" code="star"></column>
                                    <column label="评价时间">
                                        {{ Time::toDate($list_item['createTime']) }}
                                    </column>
                                    <actions width="100">
                                        <action label="查看" css="blu">
                                            <attrs>
                                                <url>{{ u('Comment/alldetail',['orderId'=>$list_item['order']['id']]) }}</url>
                                                <attr>target="_blank"</attr>
                                            </attrs>
                                        </action>
                                    </actions>
                                </columns>
                            </table>
                        </yz:list>
                    @yizan_end
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
