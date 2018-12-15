@extends('admin._layouts.base')
@section('css')
<style type="text/css">
</style>
@stop
<?php
$useType = [
        ['id'=>0,'name'=>'请选择'],
        ['id'=>1,'name'=>'无限制'],
        ['id'=>2,'name'=>'指定分类'],
        ['id'=>3,'name'=>'指定商家']
];
 ?>
@section('right_content')
	@yizan_begin
		<yz:list>
			<search> 
				<row>
					<item name="name" label="优惠名称" ></item>
                    <item label="创建时间">
                        <input type="text" name="beginTime" id="beginTime" class="date u-ipttext" style="width: 150px;" value="{{ $search_args['beginTime'] }}"> -
                        <input type="text" name="endTime" id="endTime" class="date u-ipttext" style="width: 150px;" value="{{ $search_args['endTime'] }}">
                    </item>
				</row>
                <row>
                    <item name="money" label="面额"></item>
                    <item label="类型">
                        <yz:select name="useType" label="类型" options="$useType" valuefield="id" textfield="name" selected="$search_args['useType']"></yz:select>
                    </item>
                    <item name="sellerName" label="商户名称" ></item>
                    <btn type="search"></btn>
                </row>
			</search>
			<btns>
				<linkbtn label="添加优惠券" type="add"></linkbtn>
				
			</btns>
			<table>
				<columns>
					<column label="优惠券名称" align="center" code="name" width="180"></column>
                    <column label="面额" align="center" code="money" width="50">{{  $list_item['money'] }}元</column>
                    <column label="创建时间" align="center" code="createTime" type="time" width="180"></column>
                    <column label="有效期" align="center" code="ableDateTime" width="250"></column>
                    <column label="类型" align="center" code="useTypeStr"></column>
                    <column label="优惠券使用" align="left">
                        <a href="{{ u('PromotionSn/index',['promotionId'=>$list_item['id']]) }}">已发放({{$list_item['promotionSnCount']}})</a>
                        &nbsp;&nbsp;
                        <a href="{{ u('PromotionSn/index',['promotionId'=>$list_item['id'], 'status'=>3]) }}">已使用({{$list_item['usePromotionSnCount']}})</a>
                    </column>
					<actions width="150" align="left">
                        @if($list_item['sendType']==null)
                            @if(($list_item['type'] == 1 && $list_item['endTime'] > UTC_TIME) || $list_item['type'] == 2)
                                <action label="发放" css="blu">
                                    <attrs>
                                        <url>{{ u('Promotion/sendsn',['id'=>$list_item['id']]) }}</url>
                                    </attrs>
                                </action>
                            @else
                                <action label="发放" css="gray" style="cursor:default">
                                    <attrs>
                                        <url>#</url>
                                    </attrs>
                                </action>
                            @endif
                            &nbsp;&nbsp;
                        @endif

							<action label="列表" css="blu">
								<attrs>
									<url>
									{{ u('PromotionSn/index',['promotionId'=>$list_item['id'],'promotionName'=>$list_item['name'],]) }}
									</url>
									<target>_new</target>
								</attrs>
							</action>&nbsp;&nbsp;


							<action label="编辑" css="blu" type="edit"></action>&nbsp;&nbsp;

						@if($list_item['activityCount'] == 0 && $list_item['promotionSnCount'] == 0)
						<action type="destroy"  css="red"></action>
						@endif
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

		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});
</script>
@stop

