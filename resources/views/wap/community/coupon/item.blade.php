
	<!-- 1.9-2.0.2 -->
	<!-- <div class="tab active @if($val['type'] == 'offset') y-coupmaindyq @else  @endif ">
	    <div class="card y-coupmain">
	        <div class="card-content">
	            <div class="y-coupleft f12">
	                <div class="y-couplmain">
	                    <p class="mb5">
	                    	@if($val['type'] == 'money')
	                    		<i class="f18">￥</i><span>{{$val['money']}}</span>
	                    	@else
				                <span>抵</span>
				            @endif
	                    </p>
	                    <p>到期时间</p>
	                    <p>{{$val['expireTimeStr']}}</p>
	                </div>
	            </div>
	            <div class="y-coupright">
	                <div class="y-couprmain">
	                    <p>{{ $val['name'] }}</p>
	                    <p>{{ $val['brief'] }}</p>
	                </div>
	            </div>
	            @if($val['status'])<div class="y-failure"></div>@endif
	        </div>
	    </div>
	</div> -->
	<!-- 2.0.3 -->
	@if($args['status'] == 1)
		<!-- 不可使用 -->
	    <ul class="y-xcoupon y-xcoupongray">
	    	@foreach($list['list'] as $val)
				<li class="clearfix f12" id="li-{{ $val['id'] }}">
				    <div class="y-xcouponleft tc">
				        <div class="c-gray">￥<span class="f24">{{$val['money']}}</span></div>
				        <p class="c-gray">满减券</p>
				    </div>
				    @if(!empty($val['brief']))
				    <a href="javascript:$.checkBrief({{ $val['id'] }})" class="c-blue f12 y-viewdetails">查看详情></a>
				    @endif
				    <div class="y-xcouponright">
				        <p class="c-black f14 name">{{ $val['name'] }}</p>
				        <p class="c-gray">满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
				        <p class="c-gray">{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
				    </div>
				    @if($val['useTime'] > 0)
				    	<div class="y-xcouponico y-ysyico"></div>
				    @else
				    	<div class="y-xcouponico y-ysxico"></div>
				    @endif
				    <div class="brief none">
				    	<ul>
				    		<li>
				    			<p>1、满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
				    		</li>
				    		<li>
				    			<p>2、{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
				    		</li>
                            <li>
                                <p>3、{{ $val['brief'] }}</p>
                            </li>
                            <li>
                                <p>4、{{ $val['useTypeStr'] }}</p>
                            </li>
				    	</ul>
				    </div>
				</li>
			@endforeach
	    </ul>
	@else
	    <!-- 可使用 -->
	    <ul class="y-xcoupon">
		    @foreach($list['list'] as $val)
				<li class="clearfix f12" id="li-{{ $val['id'] }}">
				    <div class="y-xcouponleft tc">
				    	<div class="c-red">￥<span class="f24">{{$val['money']}}</span></div>
				        <p class="c-gray">满减券</p>
				    </div>
			        @if(!empty($val['brief']))
			        <a href="javascript:$.checkBrief({{ $val['id'] }})" class="c-blue f12 y-viewdetails">查看详情></a>
			        @endif
				    <div class="y-xcouponright">
				        <p class="c-black f14 name">{{ $val['name'] }}</p>
				        <p class="c-gray">满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
				        <p class="c-gray">{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
				    </div>
				    <div class="brief none">
				    	<ul>
				    		<li>
				    			<p>1、满{{$val['limitMoney']}}元减{{$val['money']}}元</p>
				    		</li>
				    		<li>
				    			<p>2、{{$val['beginTimeStr']}}至{{$val['expireTimeStr']}}有效</p>
				    		</li>
				    		<li>
				    			<p>3、{{ $val['brief'] }}</p>
				    		</li>
                            <li>
                                <p>4、{{ $val['useTypeStr'] }}</p>
                            </li>
				    	</ul>
				    </div>
				</li>
			@endforeach
	    </ul>
	@endif




