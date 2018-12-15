@extends('proxy._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="audit">
	<yz:fitem name="name" label="代理账户" type="text"></yz:fitem>
	<yz:fitem name="realName" label="真实姓名" type="text"></yz:fitem>   
	<yz:fitem name="mobile" label="联系电话" type="text"></yz:fitem> 
	<yz:fitem name="level" label="代理级别" type="text"></yz:fitem>   
	<yz:fitem name="checkVal" label="审核原因" type="text"></yz:fitem>
	<div id="isCheck-form-item" class="u-fitem clearfix ">
        <span class="f-tt">
             审核结果:
        </span>
        <div class="f-boxr">
              <span name="isCheck" id="isCheck" class="">
              	@if($data['isCheck'] == 1)
				<p>已通过</p>
				@elseif($data['isCheck'] == -1)
				<p>拒绝</p>
				@else
				<p>待审核</p>
				@endif
              </span>
        </div>
    </div>
</yz:form>
@yizan_end
	<script type="text/javascript"> 
	$(".u-addspbtn").remove();
	</script>
@stop 


