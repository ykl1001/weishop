@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<php> 
		$navs = ['nav1','nav2','nav3','nav4','nav5', 'nav6'];
		$nav = in_array(Input::get('nav'),$navs) ? Input::get('nav') : 'nav1' ; 
		$$nav = "on";
	</php>
		<yz:list>
            <div class="list-btns">
                <a id="export" href="javascript:;$.export(1)" target="_self" class="btn mr5 btn-gray">
                    导出到EXCEL
                </a>
            </div>
            <script>
                $.export = function(page){
					var text = "订单";
					 $.zydialogs.open("<p style='margin: 30px'>正在导出“"+text+"”···<br><br><br><br></p>",{
                        width:300,
                        title:text,
                        showButton:false,
                        showClose:false,
                        showLoading:true
                    }).setLoading();
                    $.post("{!! urldecode(u('Order/export',$args)) !!}",{page:page,json:1},function(result){
                        if(result.code == 0){
							$.zydialogs.close();
                            $.ShowAlert("导完了");
							return false;
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

            <tabs>
                @yizan_yield('navs')
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
                @yizan_stop
            </tabs>
		@yizan_yield('searchUrl')
			<search url="{{ $searchUrl }}">
		@yizan_stop
				<row>
					<item name="sn" label="订单流水"></item>
					@yizan_yield("search_userMobile")
                    <item name="mobile" label="联系电话"></item>
					@yizan_stop
                    <item name="sellerName" label="商家名称"></item>
				</row>

				<row>
					<item name="beginTime" label="开始时间" type="date"></item>
					<item name="endTime" label="结束时间" type="date"></item>
					<!-- <item label="支付状态">
						<yz:select name="payStatus" options="-1,0,1" texts="全部,未支付,已支付" selected="$search_args['payStatus']"></yz:select>
					</item> -->
					<item label="支付方式" >
			            <yz:select name="payTypeStatus" options="0,1,2,3" texts="全部,在线支付,货到付款,未支付" selected="$search_args['payTypeStatus']"></yz:select>
			        </item>
					<btn type="search"></btn>
				</row>
			</search>
			<table>
				<columns>
					<column code="sn" label="订单信息" align="left" style="vertical-align:top;" width="190">
						<p>订单号：{{ $list_item['sn'] }}</p>
                        <p>订单状态：{{ $list_item['orderStatusStr'] }}</p>
						<p>下单时间：{{ yztime($list_item['createTime']) }}</p>
					</column>
					<column code="user" label="收货信息" align="left" width="120">
                        <p>联系人：{{ $list_item['name'] }}</p>
						<p>电话：{{ $list_item['mobile'] }}</p>
						<p>地址：{{$list_item['province']}}{{$list_item['city']}}{{$list_item['area']}}{{ $list_item['address'] }}</p>
					</column>
					@yizan_yield("seller")
					<column label="商家信息" align="left" width="110">
						<p>名称：{{ $list_item['seller']['name'] }}</p>
						<p>手机：{{ $list_item['seller']['mobile'] }}</p>
					</column>
					@yizan_stop
					@yizan_yield("staff")
					<column label="员工信息" align="left" width="110">
                        @if($list_item['staff'])
						<p>名称：{{ $list_item['staff']['name']}}</p>
						<p>手机：{{ $list_item['staff']['mobile'] }}</p>
                        @else
                        <p>暂未分配人员</p>
                        @endif
					</column>
					@yizan_stop
					<column code="fee" label="金额" align="left" width="60">
						<p>总额：{{ $list_item['totalFee'] }}</p>
						<p>支付：{{ $list_item['payFee'] }}</p>
                        <p>商品：{{ $list_item['goodsFee'] }}</p>
                        <p>配送：{{ $list_item['freight'] }}</p>
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
					<actions width="30">
						<p>
                            @yizan_yield('select')
							<action label="查看" css="blu">
								<attrs>
									<url>{{ u('Order/detail', ['id'=>$list_item['id']]) }}</url>
								</attrs>
							</action>
                            @yizan_stop
						</p>
						<!--@if( $list_item['isCanDelete'] === true)
						<p><action type="destroy"></action></p>
						@endif-->
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#yzForm').submit(function(){
			var beginTime = $("#beginTime").val();
			var endTime = $("#endTime").val();
			if(beginTime!='' || endTime!='') {
				if(beginTime==''){
					alert("开始时间不能为空");return false;
				}
				else if(endTime==''){
					alert("结束时间不能为空");return false;
				}
				else if(endTime < beginTime){
					alert("开始时间不能大于结束时间");return false;
				}
			}
		});
	});
</script>
@stop
