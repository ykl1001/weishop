@extends('seller._layouts.base')

@section('content')
  <div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 商品管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">管理员管理</span>
					</p>
				</div> 
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:list>
                            <btns>
                                <linkbtn label="添加管理员" url="{{ u('AdminUser/create') }}" css="btn-gray"></linkbtn>
                                <linkbtn type="destroy" css="btn-gray"></linkbtn>
                            </btns>
	                        <table css="goodstable" relmodule="" checkbox="1">
	                            <columns>
	                                <column code="name" label="管理员"></column>
                    				<column code="role.name" label="所属组"></column>
                    				<column code="loginTime" label="最后登录时间">
                    					{{ yztime( $list_item['loginTime'] ) }}
                    				</column>
                    				<column code="loginIp" label="最后登录IP"></column>
                    				<column code="loginCount" label="登录次数"></column>
                    				<column code="createTime" label="创建时间">
                    					{{ yzday( $list_item['createTime'] ) }}
                    				</column>
                    				<column code="status" label="状态" type="status"></column>
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



