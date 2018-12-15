<div  id="y-previewbox" class="y-previewbox">
<div class="y-previewmain f12">
		<div>
			<p class="tc f12">{{$seller['name']}}</p>
			<p class="tc f14">---@if($data['paymentType'] == "cashOnDelivery")货到付款@elseif($data['paymentType'] == ""  || $data['paymentType'] == "free")@else在线支付@endif-{{$data['payType']}}({{$data['orderStatusStr']}})---</p>
		</div>
		<div class="f10">
			<p><span class="fl">订单编号:</span><span class="y-addr">{{$data['sn']}}</span></p>
			<p><span class="fl">下单时间:</span><span class="y-addr">{{$data['createTime']}}</span></p>
			<p><span class="fl">联系人:</span><span class="y-addr">{{$data['name']}}</span></p>
			<p><span class="fl">联系方式:</span><span class="y-addr">{{$data['mobile']}}</span></p>
			<p><span class="fl">收货地址:</span><span class="y-addr">{{$data['province'] . $data['city'].$data['area']. $data['address']}}</span></p>
		</div>
		<div class="y-splitlinebox">
			<div class="y-splitline"><span>@if($data['orderType'] == 1)商品详情@else服务详情@endif</span></div>
		</div>
		<ul class="y-commodity f10">
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w30 bold">名称</p>
					<p class="y-w25 bold">单价</p>
					<p class="y-w20 bold">数量</p>
					<p class="y-w25 bold">金额</p>
				</div>
			</li>
			@foreach($data['OrderGoods'] as $g)
			<li>
				<p class="show_p">@if($g['goods'][0]['goodsSn'] != "")({{$g['goods'][0]['goodsSn']}})@endif</p>
				<div class="y-distribution clearfix">
					<p class="y-w30">{{$g['goodsName']}}{{$g['goodsNorms'] ? '['.$g['goodsNorms'].']' : ''}}</p>
					<p class="y-w25">{{sprintf('%.2f',$g['price'])}}</p>
					<p class="y-w20">{{$g['num']}}</p>
					<p class="y-w25">{{sprintf('%.2f',round(($g['price'] * $g['num']),2))}}</p>
				</div>
			</li>
			@endforeach
		</ul>
		<div class="y-splitlinebox">
			<div class="y-splitline"><span>其他</span></div>
		</div>
		<ul class="y-commodity">
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">运费</p>
					<p class="y-w25">{{sprintf('%.2f',$data['freight'])}}</p>
				</div>
			</li>
			@if($data['discountFee'] > 0)
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">优惠券</p>
					<p class="y-w25">{{sprintf('%.2f',$data['discountFee'])}}</p>
				</div>
			</li>
			@endif
			@if($data['integralFee'] > 0)
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">积分抵现</p>
					<p class="y-w25">{{sprintf('%.2f',$data['integralFee'])}}</p>
				</div>
			</li>
			@endif
			@if($data['activityNewMoney'] > 0)
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">首单优惠</p>
					<p class="y-w25">{{sprintf('%.2f',$data['activityNewMoney'])}}</p>
				</div>
			</li>
			@endif
			@if($data['activityFullMoney'] > 0)
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">满减优惠</p>
					<p class="y-w25">{{sprintf('%.2f',$data['activityFullMoney'])}}</p>
				</div>
			</li>
			@endif
			@if($data['activityGoodsMoney'] > 0)
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">特价优惠</p>
					<p class="y-w25">{{sprintf('%.2f',$data['activityGoodsMoney']) }}</p>
				</div>
			</li>
			@endif
			@if($data['buyRemark'] != "")
			<li>
				<div class="y-distribution clearfix">
				<p class="y-w75">备注:&nbsp;</p>
				</div>
			</li>
			<li>
				<div class="y-distribution clearfix">
				<p class="pl">{{$data['buyRemark']}}</p>
				</div>
			</li>
			@endif
		</ul>
		<div class="y-splitlinebox"></div>
		<ul class="y-commodity">
			<li>
				<div class="y-distribution clearfix">
					<p class="y-w75">实际支付</p>
					<p class="y-w25">
						￥{{sprintf('%.2f',$data['payFee'])}}
					</p>
				</div>
			</li>
		</ul>
		<p class="tc mt10">谢谢惠顾！</p>
	</div>
<div>