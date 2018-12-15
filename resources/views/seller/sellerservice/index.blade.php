@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">服务管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
	                    	<search> 
								<row>
									<item name="name" label="服务名称"></item>
									<item label="分类">
					                    <select name="cateId" class="sle">
					                        <option value="0">全部</option>
					                        @foreach($cate as $val)
					                            <option value="{{ $val['id'] }}"  @if($search_args['cateId'] == $val['id']) selected @endif>{{ $val['name'] }}</option>
					                        @endforeach
					                    </select>
					                </item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<btns>
								<linkbtn label="添加服务" url="{{ u('SellerService/create') }}" css="btn-gray"></linkbtn>
								<!-- <linkbtn label="导出到Excel" type="export" url="{{ u('Goods/export?'.$excel) }}" css="btn-gray"></linkbtn> -->
								<linkbtn type="destroy" css="btn-gray"></linkbtn>
							</btns>
	                        <table css="goodstable" relmodule="" checkbox="1">
	                            <columns>
	                                <column label="服务名称" align="left">
	                                	<a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img fl">
                                            <img src="{{ formatImage($list_item['image'],70,70) }}" alt="">
	                                	</a>
	                                	<div class="goods_name">{{ $list_item['name'] }}</div>
	                                </column>
	                                <column label="商品标签" align="left" width="120">
										<p class="pl5">{{$list_item['systemTagListPid']['name'] or '无'}}|{{$list_item['systemTagListId']['name'] or '无'}}</p>  
									</column>
	                                <column label="服务分类" width="120">
	                                	{{ $list_item['cate']['name'] }}
	                                </column>
	                                <column label="价格" width="50">
	                                	￥{{ $list_item['price'] }}
	                                </column> 
	                                <!--column code="brief" label="服务描述" width="170"></column--> 
	                                <column code="status" label="上/下架" type="status" width="50"></column>
	                                <actions width="60"> 
										<action type="edit" css="blu"></action> 
										<action type="destroy" css="red" ></action> 
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
