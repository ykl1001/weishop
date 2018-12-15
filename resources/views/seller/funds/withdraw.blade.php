@extends('seller._layouts.base')
@section('css')
<style type="text/css">
.parentCls {margin:-40px 110px 0;}
.js-max-input {border: solid 1px #ffd2b2;background: #fffae5;padding: 0 10px 0 10px;font-size:20px;font-weight: bold;color: #ff4400;}
.f-ipt{width: 330px;}
.m-tab table tbody td{padding: 10px 5px;font-size: 12px;text-align: left;  }
.hs{background: #f5f5f5 !important;border: 1px solid #ddd !important;color: #828282 !important;}
.msg{color:#ccc}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">
			<p class="f-bhtt f14 clearfix">
				<span class="ml15 fl">提现申请</span>
				<a href="{{ u('Funds/index') }}" class="fr mr15 btn f-bluebtn" style="margin-top:8px;">返回</a>
			</p>
			<div class="p10">
				<!-- 更换绑定银行卡号 -->
				<div class="clearfix mt10">
					<div class="m-yhk m-ghkh" style="width:770px;">
						@yizan_begin
						<yz:list> 
						<table pager="no">
							<row>  							
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td  width="100px;" style="text-align:center">提现金额</td>  
									<td>
										<input type="text"  @if(!$data['lockCycl'])readonly="readonly"@endif style="width:100px;" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  name="money" class="f-ipt fl" placeholder="请输入提现金额">
										<p class="ml20 mt10" style="color: rgb(130, 130, 130);">（提现金额必须是整数 参考可提现金额：<b>{{(int)$data['moneyCycle']}} </b>;当前余额：<b>{{$data['money']}}</b> ） </p>
									</td>   
								</tr>
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">提现至</td>
									<td class="tdtr"> 
										{{ $bank['bank'] . ' 尾号：' . substr($bank['bankNo'],strlen($bank['bankNo']) - 4) }}
									</td>
								</tr>	
								<tr class="{{ $list_item_css }}" style="padding: 5px 0">
									<td style="text-align:center">验证码</td>
									<td class="tdtr"> 
										<input type="hidden" name="mobile" value="{{$bank['mobile']}}" class="f-ipt fl"> 
										<input type="text" style="width:100px;" @if(!$data['lockCycl'])readonly="readonly"@endif onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"  name="verify" class="f-ipt fl" placeholder="请输入短信验证码">
										<button href="javascript:;" class="btn f-btn fl ml10 verify" style="width: 125px;margin-top:5px;line-height:28px;" @if(!$data['lockCycl'])disabled="true"@endif>获取验证码</button>
										<b class="system_msg"></b>
									</td>
								</tr>	
							</row> 
						</table>
						</yz:list>   
						@yizan_end
                        <p class="ml10 tl mt10"> 您下次可提现日期: {{$data['moneyCycleDay']}}</p>
						<p class="tc mt20 mb20">
                            @if($data['lockCycl'])
							<a href="javascript:;" class="btn f-170btn f-170btnok">申请提现</a>
                            @else
                                <a href="javascript:;" class="btn f-170btn hs">申请提现</a>
                            @endif
						</p>
						<div class="f-bhtt f14 clearfix">
							<p class="ml10">提示:</p>
							<p class="pl20 pb10" style="line-height:18px;">
								1. 只能使用本人为户主的卡。<br>
								 2. 绑定手机为注册是绑定的手机号。
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop

@section('js')
<script type="text/javascript" src="{{ asset('js/jsform.js') }}"></script> 
<script type="text/javascript">
	
	$(function(){  
		var obj = new Object(); 
		obj.mobile  	=  "{{$bank['mobile']}}";
		obj.sellerId	=  "{{$sellerId}}"; 
		obj.bankNo    	=  "{{$bank['bankNo']}}";
		obj.bank    	=  "{{$bank['bank']}}";
		obj.verifyCode  = "";
		obj.money  		= ""; 
		obj.id  		= "{{$bank['id']}}";
		var  verifynum =  0;
		$(".verify").click(function(){
			verifynum ++;
			if(obj.sellerId !=　""){
				obj.mobile = $("input[name=mobile]").val();
				if(obj.mobile == "") {
					$.ShowAlert("请先绑定银行卡");
					return false;
				}  
				time();
				$.post("{{ u('Funds/userverify') }}",{mobile:obj.mobile},function(result){ 
					$(".system_msg").text(result.msg);
				},'json');
			}else{
				$.ShowAlert('账户异常请重新登录'); 
				return false;
			}
		});
        $(".hs").click(function(){
            $.ShowAlert('余额未达到提现要求或不在提现周期内!');
        });
		var wait = 60; 
		function time() {    
		    if (wait == 0) { 
				$(".verify").removeAttr("disabled") ;
		        $(".verify").text("免费获取验证码"); 
		        $(".system_msg").text("");
		        wait = 60; 
		    } else { 
		        $(".verify").attr('disabled',"true");
		        $(".verify").text(wait + "秒后获取验证码");  
		        wait--; 
		        setTimeout(function () {
		            time();
		        },
		        1000)
		    }
		}  
		$(".f-170btnok").click(function(){ 
			obj.verifyCode =  $("input[name=verify").val(); 
			obj.money =  $("input[name=money").val(); 
		   	if(verifynum == 0){
				$.ShowAlert("你还没有获取验证码");
				return false;
			}else if(obj.verifyCode == ""){
		   		$.ShowAlert("验证码不能为空");  
		       return  false;  
		   	}else if(obj.money == ""){
		   		$.ShowAlert("请输入提现金额");  
		       return  false;  
		   	}else{
				$.post("{{ u('Funds/ajaxwithdraw') }}",obj,function(result){  
					if(result.code == 0){
						$.ShowAlert("提现申请成功,请等待审核结果");
						function g()
						{
						    window.location="{{ u('Funds/index') }}";
						}
						setInterval(g,2000);
					}
					else{
						if(result.msg != ""){
							$.ShowAlert(result.msg);
						}else{
							$.ShowAlert("提现失败");
						}
						$(".verify").removeAttr("disabled") ;
				        $(".verify").text("免费获取验证码"); 
				        wait = 60; 
					}
				},'json');	   		
		   	}
		}); 

	})
</script> 
@include('seller._layouts.alert')
@stop

