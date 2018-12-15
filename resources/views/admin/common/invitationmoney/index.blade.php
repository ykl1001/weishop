@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
        <yz:list>
            <search>
                <row>
                    <item name="paySn" label="流水号"></item>
                    <item name="beginTime" label="开始时间" type="date"></item>
                    <item name="endTime" label="结束时间" type="date"></item>
                    <btn type="search"></btn>
                </row>
            </search>
			<table>
                    <columns>
                        <column label="编码">
                            <div>{{ $list_item['sn'] }}</div>
                        </column>
                        <column label="名称">
                            <div>{{ $list_item['user']['name'] }}</div>
                        </column>
                        <column label="手机号码">
                            <div>{{ $list_item['user']['mobile'] }}</div>
                        </column>
                        <column label="充值信息" align="left">
                            <div>购买项目：{{ $list_item['content'] }}</div>
                            <div>购买費用：{{ $list_item['money']  }}</div>
                        </column>
                        <column label="支付信息" align="left">
                            <div>支付方式：{{ Lang::get('admin.payments.'.$list_item['paymentType'])  }}</div>
                            <div>支付状态：{{ $list_item['status'] ? '充值成功' :"充值失败" }}</div>
                        </column>
                        <column label="充值时间">
                            <div>{{ yzTime($list_item['createTime'])  }}</div>
                        </column>
                    </columns>
            </table>
        </yz:list>
    @yizan_end
@stop