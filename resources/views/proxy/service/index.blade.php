@extends('proxy._layouts.base')
@section('css')
@stop
@section('right_content')
	@yizan_begin
	<yz:list>
		<search url="index">
			<row>

				<item name="name" label="商家名"></item>
                <item name="mobile" label="联系电话"></item>
                <item label="状态">
                    <yz:select name="status" options="0,1,2" texts="全部,关闭,开启" selected="$search_args['status']"></yz:select>
                </item>
                <item label="商家分类">
                    <select name="cateId" class="sle">
                        <option value="0">请选择</option>
                        @foreach($cateIds as $cate)
                            <option value="{{ $cate['id'] }}"  @if((int)Input::get('cateId') == $cate['id']) selected @endif>{{ $cate['name'] }}</option>
                        @endforeach
                    </select>
                </item>
            </row>
            <row>
				<yz:fitem name="provinceId" label="所在地区">
					<yz:region name="provinceId" pval="$search_args['provinceId']" cval="$search_args['cityId']" aval="$search_args['areaId']" showtip="1" new="1"></yz:region>
				</yz:fitem>
				<btn type="search"></btn>
			</row>
		</search>
		<btns> 
			<linkbtn type="add" url="create"></linkbtn> 
		</btns>
		<table >
		<columns> 
			<column code="" label="代理级别" width="60">
				<php> 
					$level = 1;
					if($list_item['third']['id'] > 0){
						$level = $list_item['third']['level'];
					}
					if($list_item['second'] > 0){
						$level = $list_item['second']['level'];
					}
					if($list_item['third'] > 0){
						$level = $list_item['third']['level'];
					}
				</php>
				{{$level}}级
			</column>
			<column code="name" label="商家名" align="left" width="70"></column>
            <column code="balance" label="余额" align="center" width="50"></column>
			<column code="goods" label="商品管理" align="center" width="250">
				<p>
					<a href="{{ u('Service/goodsLists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">商品({{$list_item['goodscount']}})</a>&nbsp;&nbsp;

					@if($list_item['storeType'] == 1)
						<a href="###" style="color:#ccc;cursor:default;">服务(0)</a>&nbsp;&nbsp;
					@else
						<a href="{{ u('Service/serviceLists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">服务({{$list_item['servicecount']}})</a>&nbsp;&nbsp;
					@endif

					<a href="{{ u('Service/staffLists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">人员({{$list_item['staffcount']}})</a>&nbsp;&nbsp;
					<a href="{{ u('Service/cateLists',['sellerId'=>$list_item['id'], 'type'=>1]) }}" style="color:grey;">商品分类({{$list_item['goodscatecount']}})</a>&nbsp;&nbsp;

					@if($list_item['storeType'] == 1)
						<a href="###" style="color:#ccc;cursor:default;">服务分类(0)</a>&nbsp;&nbsp;
					@else
						<a href="{{ u('Service/cateLists',['sellerId'=>$list_item['id'], 'type'=>2]) }}" style="color:grey;">服务分类({{$list_item['servicecatecount']}})</a>&nbsp;&nbsp;
					@endif
				</p>
			</column> 
			<column code="city" label="城市" width="120">
				<p>{{$list_item['province']['name']}}{{$list_item['city']['name']}}</p>
			</column>
			<column code="mobile" label="联系电话" width="80"></column>
			<!-- <column code="status" label="状态" width="40">
				@if($list_item['status'] == 1)
                    <i title="点击停用" class="fa fa-check text-success table-status table-status1" status="0" field="status"> </i>
                @else
                    <i title="点击启用" class="fa table-status fa-lock table-status0" status="1" field="status"> </i>
                @endif
			</column> -->
			<column code="status" label="状态" width="40" >@if($list_item['status']) 开启 @else 关闭 @endif</column> 
			<actions width="60"> 
				<p><action type="edit" css="blu" label="详情"></action></p> 
			</actions> 
		</columns>  
	</table>
	</yz:list>
	@yizan_end
@stop 