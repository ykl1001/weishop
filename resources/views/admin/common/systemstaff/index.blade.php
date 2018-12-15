@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<php>
		$types = array(
			0 => '个人加盟人员',
			1 => '配送人员',
			2 => '服务人员',
			3 => '配送和服务人员',
		);
	</php>
		<yz:list>
			<search> 
				<row>
					<item name="sellerId" type="hidden"></item>
					<item name="name" label="员工姓名"></item>
					<item name="mobile" label="员工电话"></item>
				</row>
                <row>
                    <yz:fitem name="provinceId" label="所在地区">
                        <yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1" new="1"></yz:region>
                    </yz:fitem>
                    <btn type="search"></btn>
                </row>
			</search>
			<btns>
				@if($sellerId > 0 && $seller['type'] == 2)
					<linkbtn type="add">
						<attrs>
							<url>{{ u('Staff/create',['sellerId'=>$sellerId]) }}</url>
						</attrs>
					</linkbtn>
				@endif
                <linkbtn label="删除" type="destroy"></linkbtn>
                <linkbtn label="添加人员" type="add"></linkbtn>
			</btns>
			<table checkbox="1">
				<columns>
					<column code="id" label="编号" width="50"></column>
                    <column code="seller" label="当前状态" align="left">
                        <p>{{ $list_item['isWork'] ? '上班' : '下班' }}</p>
                    </column>
					<column code="seller" label="服务城市" align="left">
                        <p>
                            {{ $list_item['province']['name'] ? $list_item['province']['name'] : '' }}
                            {{ $list_item['city']['name'] ? $list_item['city']['name'] : '' }}
                            {{ $list_item['area']['name'] ? $list_item['area']['name'] : '' }}
                        </p>
					</column>
					<column code="staff" label="人员名称">
						<p>{{ $list_item['name'] }}</p>
					</column>
					<column code="type" label="类型" >
						<p>{{$types[$list_item['type']]}}</p>
					</column>
					<column code="mobile" label="手机号"></column>
					<column code="company" label="所属公司"></column>
					<column code="status" type="status" label="状态" width="80"></column>
					<actions width="80">
						<action type="edit" css="red"></action>
						<action type="destroy" css="red"></action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
@stop
