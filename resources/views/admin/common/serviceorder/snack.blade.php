<div class="u-tt clearfix">
	<span class="fl f14">服务列表</span>
</div>
<div class="m-taborder tds" style="width:100%;margin-top:15px">
	<table>
		<tr style="background-color: #F3F6FA;">
			<td width="40%">
				<p class="tc f14">
                    服务名称
				</p>
			</td>
			<td width="30%">
				<p class="tc f14">
					数量
				</p>
			</td>
			<td width="20%">
				<p class="tc f14">
					单价
				</p>
			</td>
		</tr>
        @foreach($data['orderGoods'] as $val)
		<tr>
			<td width="40%">
				<p class="tc f14">
					{{$val['goodsName']}}
				</p>
			</td>
			<td width="30%">
				<p class="tc f14">
					x{{$val['num']}}
				</p>
			</td>
			<td width="20%">
				<p class="tc f14">
					￥{{(double)round(($val['price'] * $val['num']),2)}}
				</p>
			</td>
		</tr>
        @endforeach
		<tr>
			<td width="100%" colspan="4" >
				<p class="tr pr10 f16">
					<span class="ml20">使用积分：{{ $data['integral'] }}</span>
				</p>
				<p class="tr pr10 f14">
					<span class="ml20">总金额：￥{{$data['totalFee']}}</span>
					<span class="ml20">商品金额：￥{{$data['goodsFee']}}</span>
				</p>
				<p class="tr pr10 f14">
					<span class="ml20">配送费：￥{{$data['freight']}}</span>
				</p>
				<p class="tr pr10 f14">
					<span class="ml20">优惠券：￥{{$data['discountFee']}}</span>
					<span class="ml20">积分抵扣金额：￥{{$data['integralFee']}}</span>
					<span class="ml20">首单优惠：￥{{$data['activityNewMoney']}}</span>
                    <span class="ml20">满减优惠：{{$data['activityFullMoney']}}</span>
                    <span class="ml20">特价优惠：￥{{$data['activityGoodsMoney']}}</span>
				</p>
				<p class="tr pr10 f14">
					<span class="ml20">实付金额：￥{{$data['payFee']}}</span>
                    <span class="ml20">商家入账：￥{{$data['sellerFee']}}</span>
                    <span class="ml20">抽成金额：￥{{(double)$data['drawnFee']}}</span>
				</p>
			</td>
		</tr>

	</table>
</div>