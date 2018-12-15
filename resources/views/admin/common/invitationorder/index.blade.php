@extends('admin._layouts.base')

@section('css')
@section('right_content')
    @yizan_begin
    <yz:list>
        <search> 
            <row>
                <item name="sn" label="订单号"></item>
                <item name="buyer" label="购买人"></item>
                <item name="invitor" label="推荐人"></item>   
            </row>
            <row>
                <item label="状态">
                    <yz:select name="status" options="0,1,2" texts="全部,未完结,已完结" selected="$search_args['status']"></yz:select> 
                </item>
                <btn type="search"></btn> 
            </row>
        </search>  
        <table> 
            <columns>
                <column code="sn" label="订单号" width="160"></column> 
                <column label="订单金额" >{{ $list_item['payFee'] }}</column>
                <column code="returnFee" label="总返佣" >{{ $list_item['isRefund'] ? 0 : $list_item['returnFee'] }}</column>
                <column code="level1" label="初级" >{{ $list_item['isRefund'] ? 0 : $list_item['level0'] }}</column>
                <column code="level1" label="一级" >{{ $list_item['isRefund'] ? 0 : $list_item['level1'] }}</column>
                <column code="level2" label="二级" >{{ $list_item['isRefund'] ? 0 : $list_item['level2'] }}</column>  
                <column code="level3" label="三级" >{{ $list_item['isRefund'] ? 0 : $list_item['level3'] }}</column>  
                <column width="70" label="购买会员" >{{$list_item['user']['name']}}</column>  
                <column label="订单状态" >{{ $list_item['orderCompleteStatus'] ? '已完结' : '未完结' }}</column>  
                <column label="是否退款" >{{ $list_item['isRefund'] ? '是' : '否' }}</column>  
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
@stop
