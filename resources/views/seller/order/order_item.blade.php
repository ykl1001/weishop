@yizan_begin
<php>
    $navs = ['nav1','nav2','nav3','nav4','nav5','nav6','nav7','nav8','nav9'];
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
        <nav label="待付款@if($orderStatus['payment'])({{$orderStatus['payment']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'3','nav'=>'nav4']) }}</url>
                <css>{{$nav4}}</css>
            </attrs>
        </nav>
        <nav label="待发货@if($orderStatus['shipped'])({{$orderStatus['shipped']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'4','nav'=>'nav5']) }}</url>
                <css>{{$nav5}}</css>
            </attrs>
        </nav>
        <nav label="退款中@if($orderStatus['refund'])({{$orderStatus['refund']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'5','nav'=>'nav6']) }}</url>
                <css>{{$nav6}}</css>
            </attrs>
        </nav>
        <nav label="已发货@if($orderStatus['affirmCont'])({{$orderStatus['affirmCont']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'7','nav'=>'nav8']) }}</url>
                <css>{{$nav8}}</css>
            </attrs>
        </nav>
        <nav label="已完成@if($orderStatus['rate'])({{$orderStatus['rate']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'6','nav'=>'nav7']) }}</url>
                <css>{{$nav7}}</css>
            </attrs>
        </nav>
        <nav label="已关闭@if($orderStatus['cancelCount'])({{$orderStatus['cancelCount']}})@endif">
            <attrs>
                <url>{{ u('Order/index',['status'=>'8','nav'=>'nav9']) }}</url>
                <css>{{$nav9}}</css>
            </attrs>
        </nav>
    </navs>
</tabs>
<search url="{{ $searchUrl }}">
    <row>
        <item name="sn" label="订单SN码"></item>
        <item name="name" label="联系人名称"></item>
        <item name="mobile" label="联系电话"></item>
    </row>
    <row>
        <item name="staffName" label="运 单 号"></item>
        <item name="beginTime" label="开始时间" type="date"></item>
        <item name="endTime" label="结束时间" type="date"></item>
        <item label="支付方式" >
            <yz:select name="payTypeStatus" options="0,1,2,3" texts="全部,在线支付,货到付款,未支付" selected="$search_args['payTypeStatus']"></yz:select>
        </item>
        <btn type="search" css="btn-gray"></btn>
    </row>
</search>

<div class="list-btns">
	<a id="export" href="javascript:;$.export(1)" target="_self" class="btn mr5 btn-gray">
		导出到EXCEL
	</a>
</div>
<script>
	$.export = function(page){
		$.post("{!! urldecode(u('Order/export',$args)) !!}",{page:page,json:1},function(result){
			if(result.code == 0){
				$.ShowAlert("导完了");
			}else{
				window.open("{!! urldecode(u('Order/export',$args)) !!}"+"&page="+page);
				page++;
				setTimeout(function(){
					$.export(page);
				}, 1000);
			}
		},'json');
	}
</script>
<table>
    <columns>
        <column code="id" label="编号" width="80"></column>
        <column code="sn" label="订单SN码" width="150"></column>
        <column code="name" label="会员信息" width="150" align="left">
            <p>名称:{{$list_item['user']['name']}}</p>
            <p>手机:{{$list_item['user']['mobile']}}</p>
        </column>

        <column code="name" label="收货信息" width="180" align="left">
            <p>收货地址:{{$list_item['province']}}{{$list_item['city']}}{{$list_item['area']}}{{ $list_item['address'] }}</p>
            <p>联 系 人:{{$list_item['name']}}</p>
            <p>联系号码:{{$list_item['mobile']}}</p>
        </column>
        <column code="name" label="物流信息" width="180" align="left">
            @if($list_item['orderTrack'])
                <p>物流公司:{{$list_item['orderTrack']['expressCompany']}}</p>
                <p>运 单 号:{{$list_item['orderTrack']['expressNumber']}}</p>
            @else
                <p style="text-align: center">暂无物流消息</p>
            @endif
        </column>
        <column code="totalFee" label="订单金额" width="80" align="left">
            <p>总额:{{$list_item['totalFee']}}</p>
            <p>支付:{{$list_item['payFee']}}</p>
            <p>商品:{{$list_item['goodsFee']}}</p>
            <p>运费:{{$list_item['freight']}}</p>
        </column>
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