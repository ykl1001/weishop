@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search url="index">
            <row>
                <linkbtn label="全部">
                    <attrs>
                        <url>{{ u('SendDataStaff/index') }}</url>
                    </attrs>   
                </linkbtn>
            	<linkbtn label="今天">
                    <attrs>
                        <url>{{ u('SendDataStaff/index', ['time'=>1]) }}</url>
                    </attrs>   
                </linkbtn>
                <linkbtn label="7天">
                    <attrs>
                        <url>{{ u('SendDataStaff/index', ['time'=>7]) }}</url>
                    </attrs>   
                </linkbtn>
                <linkbtn label="30天">
                    <attrs>
                        <url>{{ u('SendDataStaff/index', ['time'=>30]) }}</url>
                    </attrs>   
                </linkbtn>
	            <item name="beginTime" label="开始时间" type="date"></item>
	            <item name="endTime" label="结束时间" type="date"></item>
	            <item name="cityName" label="城市"></item>
                <btn type="search"></btn>
	            <linkbtn label="导出到EXCEL" type="export" url="{{ u('SendDataStaff/export', $search_args) }}"></linkbtn>
            </row>
        </search>
        <table>
            <columns>
                <column code="id" label="编号" width="20"></column>
                <column code="name" label="姓名" width="50" align="left"></column>
                <column code="company" label="所属公司" width="50" align="left"></column>
                <column code="address" label="所在城市" width="40" align="left"></column>
                <column code="total.totalOrder" label="订单总数" width="40"></column>
                <column code="total.totalEndOrder" label="完成总数" width="50"></column>
                <column code="total.mackMoney" label="赚取金额" width="50"></column>
                <column code="total.totalErrOrder" label="异常单" width="60"></column>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')

@stop