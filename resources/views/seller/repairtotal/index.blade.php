@extends('seller._layouts.base')
@section('css')
    <style>
        p{word-wrap:break-word; word-break:normal;}
        .tds tr{background-color: #fff;}
        .y-cancelreason{margin:0 15px;}
        .y-cancelreason li span{line-height: 1.75rem;display: inline-block;max-width: 90%;text-overflow: ellipsis;overflow: hidden;white-space: nowrap;vertical-align: top;}
        .y-radio{width: .8rem;height: .8rem;display: inline-block;-webkit-appearance: radio;margin-top: .35rem;}
        .y-otherreasons{clear: both;width: 100%;resize: none;min-height: 22px;overflow:auto;word-break:break-all;border: 0;background: none;}
        .zydialog_head{width:600px;}
    </style>
@stop
@section('content')
<php>
	$status = [
		0 => '待处理',
		1 => '处理中',
		2 => '处理完成',
	];

    $propety = [
    0 => '业主',
    1 => '租客',
    2 => '业主家属',
    ];

</php>
<div>
	<div class="m-zjgltbg">					
		<div class="p10">
			<div class="g-fwgl">
				<p class="f-bhtt f14 clearfix">
					<span class="ml15 fl">维修统计</span>
				</p>
			</div>
			<div class="m-tab m-smfw-ser">
				@yizan_begin
					<yz:list>
						<search url="{{ $searchUrl }}">
                            <row>
                                <item name="userName" label="报修人"></item>
                                <item name="staffName" label="维修人"></item>
                                <yz:fitem  name="status" label="维修结果">
                                    <select name="status" style="min-width:100px;width:auto" class="sle ">
                                        <option value="0" @if($args['status'] == -1) selected @endif >全部</option>
                                        <option value="1"  @if($args['status'] == 0) selected @endif >待处理</option>
                                        <option value="2" @if($args['status'] == 1) selected @endif>处理中</option>
                                        <option value="3" @if($args['status'] == 2) selected @endif>处理完成</option>

                                    </select>
                                </yz:fitem>                            </row>
							<row>
								<item name="name" label="业主名称"></item>  
								<item name="build" label="楼栋号"></item> 
								<item name="roomNum" label="房间号"></item> 
								<btn type="search" css="btn-gray"></btn>
							</row>
						</search>

                        <btns>
                            <btns>
                                <a id="export" href="{!! urldecode(u('RepairTotal/export',$args)) !!}" target="_self" class="btn mr5 btn-gray">
                                    导出到EXCEL
                                </a>
                                <btn type="destroy" css="btn-gray" label="删除"></btn>

                            </btns>
                        </btns>
						<table checkbox="1">
							<columns>
                                <column code="id" label="编号" width="100">
                                    <p>{{ $list_item['id']}}</p>
                                </column>
								<column code="userId" label="报修人" width="100">
                                    <p>{{ $list_item['puser']['name'] }}</p>
                                </column>

                                <column code="puserId" label="认证身份" width="100">
                                    <p>{{ $propety[$list_item['puser']['type']] }}</p>
                                </column>

								<column code="build" label="楼栋号" width="100">
									<p>{{ $list_item['build']['name'] }}</p>
								</column>
								<column code="roomNum" label="房间号" width="100" >
									<p>{{ $list_item['room']['roomNum'] }}</p>
								</column>
                                <column code="type" label="故障类型" width="70">
                                    <p>{{ $list_item['types']['name'] }}</p>
                                </column>

                                <column code="staffName" label="维修人" width="50">
                                    <p>{{ $list_item['staff']['name'] }}</p>
                                </column>

                                <column code="status" label="维修结果 " width="70">
                                    <p> {{ $status[$list_item['status']] }}</p>
                                </column>
								<column code="owner" label="维修评论" >
									<p> @if($list_item['star']){{ $list_item['star']}} @else 0 @endif</p>
								</column>
								<column code="apiTime" label="报修时间" >
									<p>{{ yztime($list_item['apiTime']) }}</p>
								</column>

                                <column code="finishTime" label="完成时间" >
                                    <p>{{ yztime($list_item['finishTime']) }}</p>
                                </column>

								<actions width="80">
									<action label="查看详情" >
										<attrs>
											<url>{{ u('Repair/edit',['id'=> $list_item['id']]) }}</url>
										</attrs>
									</action>

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
