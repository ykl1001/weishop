@extends('admin._layouts.base')
@section('right_content')

	<div>
		<div class="m-zjgltbg">
			<div class="p10">
				<!-- 人员管理 -->

				<!-- 人员表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>

							@yizan_yield('btns')
	                    	<btns>
                                <btns>
                                    <linkbtn label="添加人员">
                                        <attrs>
                                            <url>{{ u('Property/staffcreate', ['sellerId'=>Input::get('sellerId')]) }}</url>
                                        </attrs>
                                    </linkbtn>

                                    {{--<linkbtn type="destroy" css="btn-gray"></linkbtn>--}}

                                </btns>
							</btns>
							@yizan_stop
	                    	<search>
								<row>
									<item name="sellerId" type="hidden" val="{{Input::get('sellerId')}}"></item>
                                    <item name="name" label="姓名"></item>
                                    <item name="mobile" label="电话"></item>
									<btn type="search" css="btn-gray"></btn>
								</row>
							</search>
							<table css="goodstable">
                                <columns>
                                    <column code="id" label="编号" width="30" align="center" css="pl10"><p>{{$list_item['id']}}</p></column>
                                    <column code="avatar" label="头像" type="image" width="60"></column>
                                    <column code="name" label="姓名" width="60" align="center" css="pl10">
                                    	<p>{{$list_item['name']}}</p>
                                    </column>
                                    <column code="repairNumber" label="员工号" width="60" align="center" css="pl10">
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
                                    {{--<column code="status" label="状态" type="status" width="60">--}}
                                    {{--</column>--}}
                                    @yizan_yield('actions')
					                <actions width="80">
		    							<action label="编辑" css="blu">
		    								<attrs>
												<url>{{ u('Property/staffedit',['sellerId'=>Input::get('sellerId'),'id'=>$list_item['id']]) }}</url>
											</attrs>
										</action>
                                        <action label="删除" css="red">
                                            <attrs>
                                                <click>$.RemoveItem(this, '{!!u('Property/staffdestroy',['sellerId'=>Input::get('sellerId'), 'id'=>$list_item['id']])!!}', '你确定要删除该数据吗？');</click>
                                            </attrs>
                                        </action>		        					</actions>
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
