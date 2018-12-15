
@foreach($list['orderList'] as $v)
    <div class="card y-ddcard" id="list_item{{ $v['id'] }}">
        <div class="card-header">
            <div class="y-ordertitle" @if($v['sellerId'] > 0) onclick="$.herf('{{ u('Seller/detail',['id'=>$v['sellerId'],'urltype'=>2]) }}')" @else onclick="$.toast('商家已关闭或不存在')" @endif>
                <i class="icon iconfont vat mr5">&#xe632;</i>
                <span class="y-ddmaxwidth">{{ $v['shopName'] or '商家已关闭或不存在'}}</span>
                <i class="icon iconfont vat ml5 f14">&#xe602;</i>
            </div>
            <span class="c-red">{{ $v['orderStatusStr']}}</span>
        </div>
        <div class="card-content">
            <div class="list-block media-list y-ddlistblock">
                <ul>
					@foreach($v['orderGoods'] as $item)
						<li  onclick="$.href('{{ u('Logistics/refundview',['orderId'=>$v['id']]) }}')">
							<a href="#" class="item-link item-content"><!-- onclick="$.href('{{ u('Seller/detail',['id'=>$v['sellerId'],'urltype'=>2]) }}')" -->
								<div class="item-media">
									<img src="{{formatImage($item['goodsImages'],200,200)}}" width="45.5">
								</div>
								<div class="item-inner f12">
									<div class="item-title-row">
										<div class="item-title">{{$item['goodsName']}}</div>
										<div class="item-after">
											@if($item['salePrice'] <= 0)
												<p>￥{{$item['price']}}</p>
											@else
												<p>￥{{($item['price'] * $item['salePrice']) / 10}}</p>
											@endif
											@if($item['salePrice'] > 0)
												<del class="c-gray">￥{{$item['price']}}</del>
											@endif
										</div>
									</div>
									@if($item['goodsNorms'])
										<div class="item-title-row c-gray">
											<div class="item-title">{{str_replace(':','-',$item['goodsNorms'])}}</div>
											<div class="item-after c-gray">x{{$item['num']}}</div>
										</div>
									@else
										<div class="item-title-row c-gray">
											<div class="item-title"></div>
											<div class="item-after c-gray">x{{$item['num']}}</div>
										</div>
									@endif
								</div>
							</a>
						</li>
					@endforeach
                </ul>
            </div>
        </div>
        <div class="card-footer">
            <span></span>
            <span>支付金额：￥<span class="">{{$v['payFee']}}</span>&nbsp;&nbsp;退款金额：￥<span class="">{{$v['payFee']}}</span></span>
        </div>
    </div>
@endforeach