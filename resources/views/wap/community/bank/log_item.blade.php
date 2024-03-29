@foreach($data['paylogs'] as $v)
	<li class="item-content">
	    <div class="item-inner f14">
	        <div class="item-title-row">
	            <div class="item-title f14 c-black">{{ Lang::get('wap.pay_type.'.$v['payType']) }}</div>
	            <div class="item-after f12 c-gray2">{{$v['createTime']}}</div>
	        </div>
	        <div class="item-subtitle f12 c-gray">交易号：{{$v['sn']}}</div>
	        <div class="item-title-row">
	            <div class="item-title f14 c-gray">
	            	余额：{{$v['balance']}}
	            </div>
                @if($v['payType'] == 1 || $v['payType'] == 5 || $v['payType'] == 8 || $v['payType'] == 9)
        			<div class="item-after f14 c-gray">-{{$v['money']}}</div>
        		@else
					<div class="item-after f14 c-red">+{{$v['money']}}</div>
        		@endif
	        </div>
	    </div>
	</li>
@endforeach

