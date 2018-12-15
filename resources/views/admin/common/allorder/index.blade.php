@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<php> 
		$navs = ['nav1','nav2','nav3','nav4','nav5', 'nav6', 'nav7'];
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
                    $.post("{!! urldecode(u('AllOrder/export',$args)) !!}",{page:page,json:1},function(result){
                        if(result.code == 0){
                            $.ShowAlert("导完了");
                        }else{
                            window.open("{!! urldecode(u('AllOrder/export',$args)) !!}"+"&page="+page);
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
                            <url>{{ u('AllOrder/index',['nav'=>'nav1']) }}</url>
                            <css>{{$nav1}}</css>
                        </attrs>
                    </nav>
                    <nav label="待付款">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'6','nav'=>'nav2']) }}</url>
                            <css>{{$nav2}}</css>
                        </attrs>
                    </nav>
                    <nav label="待发货">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'7','nav'=>'nav3']) }}</url>
                            <css>{{$nav3}}</css>
                        </attrs>
                    </nav>
                    <nav label="退款中">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'5','nav'=>'nav4']) }}</url>
                            <css>{{$nav4}}</css>
                        </attrs>
                    </nav>
                    <nav label="已发货">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'8','nav'=>'nav5']) }}</url>
                            <css>{{$nav5}}</css>
                        </attrs>
                    </nav>
                    <nav label="已完成">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'3','nav'=>'nav6']) }}</url>
                            <css>{{$nav6}}</css>
                        </attrs>
                    </nav>
                    <nav label="已关闭">
                        <attrs>
                            <url>{{ u('AllOrder/index',['status'=>'4','nav'=>'nav7']) }}</url>
                            <css>{{$nav7}}</css>
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
					<column code="id" label="编号"  width="20"></column>
					<column code="sn" label="订单号" align="left" style="vertical-align:top;" width="190">
						<p>{{ $list_item['sn'] }}</p> 
						<p>下单时间：{{ yztime($list_item['createTime']) }}</p>
					</column>
					<column code="user" label="会员信息" align="left" width="120">
                        <p>{{ $list_item['name'] }}</p>
						<p>{{ $list_item['mobile'] }}</p>
					</column>
					@yizan_yield("seller")
					<column label="商家信息" align="left" width="110">
						<p>{{ $list_item['seller']['name'] }}</p>
						<p>{{ $list_item['seller']['mobile'] }}</p>
					</column>
					@yizan_stop
					<column label="代理商" align="left" width="110">
					@if($list_item['firstLevel']['id'] > 0 )
						<p>一级：{{$list_item['firstLevel']['name']}}（{{ $list_item['firstLevel']['realName'] }}）</p>
						@if($list_item['secondLevel']['id'] > 0 )<p>二级：{{$list_item['secondLevel']['name']}}（{{ $list_item['secondLevel']['realName'] }}）</p>@endif
						@if($list_item['thirdLevel']['id'] > 0 )<p>三级：{{$list_item['thirdLevel']['name']}}（{{ $list_item['thirdLevel']['realName'] }}）</p>@endif
					@else
						平台
					@endif
					</column>
					<column code="fee" label="订单金额" align="left" width="60">
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
					<column label="状态" width="60">
						{{ $list_item['orderStatusStr'] }}
					</column>
					<actions width="30">
						<p>
                            @yizan_yield('select')
							<action label="查看" css="blu">
								<attrs>
									<url>{{ u('AllOrder/detail', ['id'=>$list_item['id']]) }}</url>
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
		
		var dfk = "{{ $total['dfk'] > 999 ? '999+' : $total['dfk'] }}";
		var dfh = "{{ $total['dfh'] > 999 ? '999+' : $total['dfh'] }}";
		var tkz = "{{ $total['tkz'] > 999 ? '999+' : $total['tkz'] }}";
		$(".tab-navs .tab-nav").each(function(k, v){
			if(k == 1 && dfk > 0)
			{
				var obj = $(this).find("a");
				obj.text(obj.text()+'('+dfk+')');
			}
			if(k == 2 && dfh > 0)
			{
				var obj = $(this).find("a");
				obj.text(obj.text()+'('+dfh+')');
			}
			if(k == 3 && tkz > 0)
			{
				var obj = $(this).find("a");
				obj.text(obj.text()+'('+tkz+')');
			}
		});
	});
</script>
@stop
