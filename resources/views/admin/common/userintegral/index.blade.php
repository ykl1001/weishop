@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
        <search>
            <row>
                <item name="name" 	label="会员名称"></item>
                <item name="mobile" 	label="手机号"></item>
            </row>
            <row>
                <item name="beginTime" label="开始时间" type="date"></item>
                <item name="endTime" label="结束时间" type="date"></item>
                <btn type="search"></btn>
            </row>

        </search>
        <table>
			<columns>
                <column code="id" label="编号" width="40"></column>
                <column code="username" label="会员名" align="center" width="100">
                    {{ $list_item['user']['name'] }}
                </column>
                <column code="integral" label="积分" align="center" width="50"></column>
                <column code="remark" label="详情" align="center" width="100"></column>
                <column code="createTime" label="时间" align="center" width="100" type="time"></column>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')

@stop
