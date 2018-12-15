@extends('seller._layouts.base')

@section('content')
  <div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 商品管理 -->
				<div class="g-fwgl">
					<p class="f-bhtt f14 clearfix">
						<span class="ml15 fl">管理员组管理</span>
					</p>
				</div> 
				<div class="m-tab m-smfw-ser">
					@yizan_begin
                	   <yz:list>
                		<btns>
                			<linkbtn label="添加管理员组" url="{{ u('AdminRole/create') }}" css="btn-gray"></linkbtn>
                            <linkbtn type="destroy" css="btn-gray"></linkbtn>
                		</btns>
                		<table checkbox="1">
                			<columns>
                				<column code="id" label="编号"></column>
                				<column code="name" label="组名称"></column>
                				<column code="status" label="状态" type="status"></column>
                				<actions>
                					<action type="edit" css="blu"></action>
                					<!-- @if($list_item['canDelete'] == 1) -->
                					<action type="destroy" css="red"></action>
                					<!-- @else -->
                					<action type="destroy" click="javascript:;" style="color:#ccc;cursor:default"></action>
                					<script type="text/javascript">
                                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled');
                                    </script>
                                    <!-- @endif -->
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



