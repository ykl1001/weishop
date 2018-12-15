@extends('admin._layouts.base')

@section('css')
<style type="text/css">
	.wxts{margin-left: 100px;}
	.u-addspbtn2{margin-left: 10px;cursor: pointer;}
</style>
@stop

@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="save" nobtn="1">
			<!--<yz:fitem name="SmsUrl" 	 label="发送地址" attr="disabled='true'"></yz:fitem>-->
			<yz:fitem name="SmsUserName" label="短信账号"></yz:fitem>  
			<yz:fitem name="SmsPassword" label="短信密码"></yz:fitem>
			<!-- <div class="wxts">
				温馨提示：成功修改账号后请前往 缓存管理 清空缓存。
			</div> -->
			<div class="u-fitem clearfix">
                <span class="f-tt">
                    &nbsp;
                </span>
                <div class="f-boxr">
                      <button type="submit" class="u-addspbtn fl">提 交</button>
                      <span onclick="$.surplus()" class="u-addspbtn2">剩余查询</span>
                </div>
            </div>
		</yz:form>
	@yizan_end
@stop 

@section('js')
<script type="text/javascript">
	$(function(){
		$.surplus = function() {
			$.ShowAlert("正在查询，请稍候...");
			$.post("{{ u('SmsConfig/surplus') }}",function(res){
				if(res.code != 0){
                    $.ShowAlert(res.msg);
                }else{
                    $.ShowAlert(res.data.info);
                }
			})
		}
	})
</script>
@stop
