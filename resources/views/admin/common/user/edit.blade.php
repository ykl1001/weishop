@extends('admin._layouts.base')
@section('css')
<style type="text/css"> 
	.f3f6fa{
		background:#f3f6fa;
	}
	.udb_br{
		border: 1px solid #f3f6fa;
	}
	.udb_br .u-rtt{
		margin: 0;
	}
	.udb_br .u-fitem {
		margin-top: 10px;
		margin-bottom: 10px;
	}
</style>
@stop
@section('right_content')
@yizan_begin
	<yz:form id="yz_form" action="update"> 
		<div class="udb_br">		
			<div class="u-rtt f3f6fa">
				<i class="fa fa-money mr15 ml15"></i>账户余额
			</div>
			<yz:fitem label="余额" >
				<p>￥{{$data['balance'] or '0.00'}}</p>
			</yz:fitem>
			<div class="u-rtt f3f6fa">
				<i class="fa fa-user mr15 ml15"></i>基本信息
			</div>
			<yz:fitem name="avatar" label="头像" type="image"></yz:fitem>  
			<yz:fitem name="mobile" label="手机号码" tip="注意：此号码为商家平台或会员帐号，谨慎修改！"></yz:fitem>
			<yz:fitem name="name" label="昵称"></yz:fitem>    
			<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem> 	
			
			<div class="u-rtt f3f6fa">
				<i class="fa fa-credit-card mr15 ml15"></i>银行卡资料
			</div>
			@if($data['bank'])
			<yz:fitem label="银行卡开户行" >
				<p>{{ $data['bank']['bank'] }}</p>
			</yz:fitem>
			<yz:fitem label="银行卡帐号" >
				<p>{{ $data['bank']['bankNo'] }}</p>
			</yz:fitem>
			<yz:fitem label="银行卡持卡人" >
				<p>{{ $data['bank']['name'] }}</p>
			</yz:fitem>
			<yz:fitem label="预留手机号" >
				<p>{{ $data['bank']['mobile'] }}</p>
			</yz:fitem>
			@else
				<yz:fitem label="暂无资料" >
				<p>该会员暂无绑定银行卡</p>
				</yz:fitem>
			@endif
			<div class="u-rtt f3f6fa">
				<i class="fa  fa-cog mr15 ml15"></i>系统操作
			</div>
			<yz:fitem name="status" label="会员状态">
				<yz:radio name="status" options="0,1" texts="锁定,正常" checked="$data['status']"></yz:radio>
			</yz:fitem>
			<div class="u-rtt f3f6fa">
				<i class="fa fa-eye mr15 ml15"></i>注册信息
			</div>
			<yz:fitem name="regIp" label="注册IP" type="text"></yz:fitem>
			<yz:fitem name="regTime" label="注册时间" type="text">
				{{ yztime($data['regTime']) }}
			</yz:fitem>
			<yz:fitem name="loginIp" label="最后登录IP" type="text"></yz:fitem>
			<yz:fitem name="loginTime" label="最后登录时间" type="text">
				{{ yztime($data['loginTime']) }}
			</yz:fitem>
            <input type="hidden" name="fanweId" value="{{$data['fanweId']}}">
		</div>
	</yz:form>
@foreach ($errors->all() as $error)
    <p class="error">{{ $error }}</p>
 @endforeach
 
@yizan_end

@stop 