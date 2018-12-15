@yizan_begin
<php>
    $navs = ['nav1','nav2','nav3','nav4','nav5'];
    $nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ;
    $$nav = "on";
</php>
<yz:list>
    <tabs>
    <navs>
        <nav label="全部订单">
            <attrs>
                <url>{{ u('Order/index',['nav'=>'nav1']) }}</url>
                <css>{{$nav1}}</css>
            </attrs>
        </nav>
        <nav label="待发货订单">
            <attrs>
                <url>{{ u('Order/index',['status'=>'1','nav'=>'nav2']) }}</url>
                <css>{{$nav2}}</css>
            </attrs>
        </nav>
        <nav label="待完成订单">
            <attrs>
                <url>{{ u('Order/index',['status'=>'2','nav'=>'nav3']) }}</url>
                <css>{{$nav3}}</css>
            </attrs>
        </nav>
        <nav label="已取消订单">
            <attrs>
                <url>{{ u('Order/index',['status'=>'4','nav'=>'nav5']) }}</url>
                <css>{{$nav5}}</css>
            </attrs>
        </nav>
        <nav label="已完成订单">
            <attrs>
                <url>{{ u('Order/index',['status'=>'3','nav'=>'nav4']) }}</url>
                <css>{{$nav4}}</css>
            </attrs>
        </nav>
    </navs>
</tabs>
<search url="{{ $searchUrl }}">
    <row>
        <item name="sn" label="订单SN码"></item>
        <item name="name" label="会员名称"></item>
        <item name="mobile" label="联系电话"></item>
    </row>
    <row>
        <item name="staffName" label="配送人员"></item>
        <item name="beginTime" label="开始时间" type="date"></item>
        <item name="endTime" label="结束时间" type="date"></item>
        <item label="支付方式" >
            <yz:select name="payTypeStatus" options="0,1,2,3" texts="全部,在线支付,货到付款,未支付" selected="$search_args['payTypeStatus']"></yz:select>
        </item>
        <btn type="search" css="btn-gray"></btn>
    </row>
</search>

<btns>
    <linkbtn label="导出到EXCEL" type="export" url="{{ u('Order/export?'.$excel) }}" css="btn-gray"></linkbtn>
</btns>
<table>
    <columns>
        <column code="sn" label="订单SN码" width="150"></column>
        <column code="name" label="会员名称" width="100"></column>
        <column code="mobile" label="联系电话"></column>
        <column code="address" label="配送地址">{{$list_item['province']}}{{$list_item['city']}}{{$list_item['area']}}{{ $list_item['address'] }}</column>
        <column label="配送人员" width="100">{{ $list_item['staff']['name'] }}</column>
        <column code="totalFee" label="订单金额" width="80"></column>
        <column label="支付方式" width="80">
            @if($list_item['isCashOnDelivery'])
                货到付款
            @else
                @if($list_item['payStatus'] == 1)
                    在线支付
                @else
                    未支付
                @endif
            @endif
        </column>
        <column code="status" label="订单状态" width="80">
            {{ $list_item['orderStatusStr'] }}
        </column>
        <column css="xiaopiao" label="小票打印" width="80">
            <p onclick="showUrl({{$list_item['id']}})" >小票打印</a>
        </column>
        <actions width="80">
            <action label="详情" css="blu">
                <attrs>
                    <url>{{ u('Order/detail',['orderId'=>$list_item['id']]) }}</url>
                </attrs>
            </action>
            <!--<action type="destroy" css="red"></action>-->
        </actions>
    </columns>
</table>

</yz:list>
@yizan_end