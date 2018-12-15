@extends('proxy._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="update">
	<yz:fitem name="name" label="商家名称" type="text"></yz:fitem>
	<yz:fitem name="mobile" label="联系电话" type="text"></yz:fitem>
	@if($data['type'] == 2)
	<yz:fitem name="contacts" label="法人/店主" type="text"></yz:fitem>
	@endif
	<yz:fitem name="city" label="所在地区" type="text">
        <span>{{$data['province']['name']}}{{$data['city']['name']}}{{$data['area']['name']}}</span>
	</yz:fitem> 
	<yz:fitem name="address" label="详细地址" type="text"></yz:fitem> 
	<yz:fitem name="map" label="服务定位" type="text">
	<php>
		$map = explode(',', $data['mapPointStr']);
		$point[0] = $map[1];
		$point[1] = $map[0];
		$mappoint = implode(',',$point);
	</php>
		<img src="http://st.map.qq.com/api?size=400*260&center={{$mappoint}}&zoom=16" />
	</yz:fitem> 
	<yz:fitem name="logo" label="商家图片">
		<a href="{{ $data['logo'] }}" target="_blank"><img src="{{ formatImage($data['logo'],0,160,0) }}" alt="" height="160"></a>
	</yz:fitem>
    @if($data['type'] == 1)
        <yz:fitem name="contacts" label="真实姓名" type="text"></yz:fitem>
    @endif

	<yz:fitem label="佣金比例"> 
        {{ $data['deduct'] or '0'}}
        <span style="color:#000;">%</span> 
	</yz:fitem>
	<yz:fitem label="审核原因" >
		{{ $data['checkVal'] }}
	</yz:fitem>
	<yz:fitem label="审核状态">
		@if($data['isCheck'] == 0)
		未处理
		@elseif($data['isCheck'] == 1)
		已通过
		@else
		已拒绝
		@endif
	</yz:fitem>
</yz:form>
<script type="text/javascript">
	$(function(){
		$(".u-addspbtn").hide();
	})
</script>
@yizan_end

@stop 


