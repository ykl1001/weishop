	@foreach($data as $key => $item)
		<li>
			<div class="goods">
				<a href="{{u('Goods/detail',['goodsId'=>$item['goods_id']])}}" class="" external>
					<div class="x-fwpic pr">
						<img src="{{ formatImage(preg_replace('/,.*/','',$item['goods_image']),300,300)}}"  onerror='this.src="{{ asset("wap/community/newclient/images/no.jpg") }}"' />
					</div>
					<p class="f12 c-black goodsname">
						<span class="fl na">{{$item['goods_name']}}</span>
						<span class="time c-red">￥{{$item['price']}}</span>
					</p>
				</a>
				<a href="{{u('Seller/detail',['id'=>$item['sellerId'],'urltype'=>1])}}" class="c-gray f12 storename">
					<i class="icon iconfont c-gray f13 vat">&#xe632;</i>
					<span>{{$item['name']}}</span>
					<i class="icon iconfont c-gray fr f12">&#xe602;</i>
				</a>
			</div>
		</li>
	@endforeach