@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.m-tab table tbody td{padding: 5px 0px;}
	.pl10 p{padding-left: 10px;}
</style>
@stop
@section('content')

	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 人员管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">维修人员</span>
					</p>
				</div>
				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>

							@yizan_yield('btns')
	                    	<btns>
								<linkbtn label="添加" url="{{ u('RepairStaff/create') }}" css="btn-gray"></linkbtn>
								<linkbtn type="destroy" css="btn-gray"></linkbtn>
							</btns>
							@yizan_stop
	                    	<search>
								<row>
									<item name="type" type="hidden" val="$args['type']"></item>
                                    <item name="name" label="姓名"></item>
                                    <item name="mobile" label="电话"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>	                        
							<table css="goodstable" checkbox="1">
                                <columns>
                                    <column code="id" label="编号" width="30" align="left" css="pl10"><p>{{$list_item['id']}}</p></column>
                                    <column code="avatar" label="头像" type="image" width="60"></column>
                                    <column code="name" label="姓名" width="60" align="left" css="pl10">
                                    	<p>{{$list_item['name']}}</p>
                                    </column>
                                    <column code="repairNumber" label="员工号" width="60" align="left" css="pl10">
                                        <p>{{$list_item['repairNumber']}}</p>
                                    </column>

                                    <column code="sex" label="性别" width="120">
                                    	<p>@if($list_item['sex'] == 1) 男 @else 女 @endif</p>
                                    </column>

                                    <column code="mobile" label="电话" width="100">
                                        <p>{{$list_item['mobile']}}</p>
                                    </column>
                                    <column code="type" label="维修类型" width="150">
                                    	<p>{{$list_item['repairName']}}</p>
                                    </column>
                                    <column code="status" label="状态" type="status" width="60">
                                    </column>
                                    @yizan_yield('actions')
					                <actions width="80">
		    							<action label="编辑" css="blu">
		    								<attrs>
												<url>{{ u('RepairStaff/edit',['id'=>$list_item['id'],'type'=>$list_item['type']]) }}</url>
											</attrs>
										</action>
		        						<action type="destroy" css="red"></action>                						
		        					</actions>
					                @yizan_stop
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
