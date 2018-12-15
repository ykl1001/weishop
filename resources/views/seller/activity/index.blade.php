@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<search>
								<row>
									<item name="name" label="活动名称"></item>
				                    <yz:fitem label="举办方">
				                        <yz:select name="type" options="0,1,2" texts="全部,商家,平台" selected="$search_args['type']"></yz:select>
				                    </yz:fitem>
								</row>
								<row>
				                    <item name="startTime" label="活动开始时间" type="date"></item>
				                    <item name="endTime" label="活动结束时间" type="date"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<table relmodule="SystemGoods">
								<columns>
				                    <column code="name" label="活动名称" width="100" iscut="1"></column>
				                    
									<column label="活动时间" width="150">
				                        {{ Time::toDate($list_item['startTime'],'Y-m-d') }} 至 {{ Time::toDate($list_item['endTime'],'Y-m-d') }}
									</column>
									<column label="举办方" width="80">
										{{ $list_item['isSystem'] == 1 ? '平台' : '商家' }}
									</column>
									<column label="状态" width="40">
										@if(in_array($list_item['type'], [4, 5, 6]))
											@if($list_item['timeStatus'] == 1)
												<span style="color:green">进行中</span>
											@elseif($list_item['timeStatus'] == 0)
												<span style="color:red">未开始</span>
											@elseif($list_item['timeStatus'] == -1)
												<span style="color:gray">已过期</span>
											@endif
										@else
											@if($list_item['status'] == 1)
												开启
											@else
												关闭
											@endif
										@endif
									</column>
									<actions width="60">
									    <a href="{{ u('Activity/edit',['id'=>$list_item['id']]) }}" class=" blu" data-pk="1" target="_self">查看</a>
				                    </actions>
				                </columns>
							</table>
				        </yz:list>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop
@section('js')
@stop
