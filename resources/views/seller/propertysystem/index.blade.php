@extends('seller._layouts.base')
@section('css')
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">物业配置管理</span>
					</p>
				</div>
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
							<btns>
								<linkbtn label="添加物业配置" url="{{ u('PropertySystem/create') }}" css="btn-gray"></linkbtn>
								<linkbtn type="destroy" css="btn-gray"></linkbtn>
							</btns> 
							<table css="goodstable" relmodule="PropertySystem" checkbox="1">
								<columns> 
								<column code="id" label="编号" align="center"></column>

                                    <column code="name" label="物业配置名称" align="center"></column>
                                    <column code="status" label="状态" width="50">
                                        @if($list_item['status'] == 1)
                                            <i title="停用" class="fa fa-check text-success table-status table-status1" status="0" field="status"> </i>
                                        @else
                                            <i title="启用" class="fa table-status fa-lock table-status0" status="1" field="status"> </i>
                                        @endif
                                    </column>
                                    <column code="sort" label="排序" width="50"></column>
                                    <!-- <column code="content" label="公告内容" align="left"></column>   -->
								<column code="createTime" label="发布日期" align="center">
									{{ yztime($list_item['createTime']) }}
								</column>     
								<actions> 
									<action type="edit" css="blu"></action>
									<action type="destroy" css="red"></action>
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
