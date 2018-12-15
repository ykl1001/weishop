@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="audit">
	<yz:fitem name="name" label="代理账户" type="text"></yz:fitem>
	<yz:fitem name="realName" label="真实姓名" type="text"></yz:fitem>   
	<yz:fitem name="mobile" label="联系电话" type="text"></yz:fitem> 
	<yz:fitem name="level" label="代理级别" type="text"></yz:fitem>  
	@if($data)
    @if($data['level'] > 1)
        <div class="u-fitem clearfix ">
            <span class="f-tt">
                 父级代理:
            </span>
            <div class="f-boxr">
                {{$data['parentProxy']['name']}}
            </div>
        </div>
    @endif
	<div class="u-fitem clearfix ">
        <span class="f-tt">
             最后登录IP:
        </span>
        <div class="f-boxr">
            {{$data['loginIp']}}
        </div>
    </div>
	<div class="u-fitem clearfix ">
        <span class="f-tt">
             最后登录时间:
        </span>
        <div class="f-boxr">
            {{yztime($data['loginTime'])}}
        </div>
    </div>
	<div class="u-fitem clearfix ">
        <span class="f-tt">
             登录次数:
        </span>
        <div class="f-boxr">
            {{$data['loginCount']}}
        </div>
    </div>
	@endif
	<yz:fitem name="checkVal" label="审核原因" type="textarea"></yz:fitem>
	<yz:fitem name="isCheck" label="审核状态">
		<php>
			if($data['isCheck'] == 0){
				$data['isCheck'] = 1;
			}
		</php>
		<yz:radio name="isCheck" options="-1,1" texts="拒绝,通过" checked="$data['isCheck']" default="0"></yz:radio>
	</yz:fitem>
</yz:form>
@yizan_end

@stop 


