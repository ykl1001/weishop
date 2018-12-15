@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	td p{padding: 2px;}
	.avatar img{
		border-radius:50%;width:48px;height:48px;
		-webkit-box-shadow:0 0 10px #ccc;  
	  -moz-box-shadow:0 0 10px #ccc;  
	  box-shadow:0 0 10px #ccc;   
  }
</style>
@stop

@section('right_content')
	@yizan_begin
	<yz:list>
		<search method="get">
			<row>
				<item name="name" label="会员名称"></item>  
				<item name="mobile" label="会员手机"></item>
				<item label="会员状态">
					<yz:select name="status" options="-1,1,2" texts="所有会员,锁定,正常" selected="$search_args['status']"></yz:select>
				</item>
                <item label="会员类型">
                    <yz:select name="userType" options="0,1,2" texts="所有会员,商家会员,买家会员" selected="$search_args['userType']"></yz:select>
                </item>
                <btn type="search"></btn>
			</row>
		</search>
        <btns>
            <linkbtn label="导出到EXCEL" type="export">
                <attrs>
                    <url>{{ u('User/export', $search_args ) }}</url>
                </attrs>
            </linkbtn>
            <linkbtn label="删除" type="destroy"></linkbtn>
            <span style="color: red; font-size: 15px;">* 请勿随意删除会员，容易导致数据错误</span>
        </btns>
		<table checkbox="1">
			<columns>
                <column code="id" label="会员ID" width="50"></column>
				<column label="头像" align="tc" css="avatar" code="avatar" type="image" width="100">
					<img src="{{$list_item['avatar'] or $site_config['admin_logo'] }}" width="48px"/>
				</column>
				<column label="名称" align="tc" code="name">
					<p>
                        {{ $list_item['name'] }}
                        @if(!empty($list_item['seller']['name']))
                        <span style="color:#666">({{$list_item['seller']['name']}})</span>
                        @endif
                    </p>
				</column>
                <column label="手机" align="" width="100" code="name">
                    <p>{{ $list_item['mobile'] }}</p>
                </column>
                <column label="余额" align="center" width="100" code="balance">
                    <p><a href="{{u('PayLog/index',['mobile'=>$list_item['mobile']])}}">{{ (double)$list_item['balance'] }}</a></p>
                </column>
                <column label="现有积分" align="center" width="100" code="balance">
                    <p><a href="{{u('UserIntegral/index',['mobile'=>$list_item['mobile']])}}">{{ (int)$list_item['integral'] }}</a></p>
                </column>
                <column label="累计积分" align="center" width="100" code="balance">
                    <p><a href="{{u('UserIntegral/index',['mobile'=>$list_item['mobile']])}}">{{ (int)$list_item['totalIntegral'] }}</a></p>
                </column>				
                <column label="最近登录" align="" code="mobile">
                    <p>{{ yztime($list_item['loginTime'])  }}</p>
                </column>
				<column label="注册时间" align="" width="130" code="mobile">
					<p>{{ yztime($list_item['regTime'])  }}</p>
				</column>
				<column code="status" label="状态" type="status"  width="30"></column>
				<actions width="120"  align="left">
                    <action type="edit" css="blu" url="javascript:$.updateBalance('{{ $list_item['id'] }}');" label="修改余额"></action>&nbsp;&nbsp;
                    <action type="edit" css="blu"></action>&nbsp;&nbsp;
					@if(!$list_item['seller'] && !$list_item['staff'])
					<action type="destroy" css="red"></action>
                    @else
                    <script type="text/javascript">
                        $(".tr-"+{{$list_item['id']}}+" input[name='key']").prop('disabled','disabled');
                    </script>
					@endif
                    <a href="{{u('User/paylog',['userId'=>$list_item['id']])}}" class="black" data-pk="1204" target="_blankk">账户明细</a>
                </actions>
			</columns>
		</table>
	</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/tpl" id="updateForm">
	<div style="width:350px;padding:10px;">
	    <div style="height:40px;line-height:40px;">
            <label>金额：</label>
            <input type="number" name="money" style="border:1px solid #EEE;height:25px;lin-height:25px;" id="money"/>
	    </div>
	    <div style="height:40px;line-height:40px;">
            <label>类型：</label>
            <select name="type" class="sle" id="type">
                <option value="1">充值</option>
                <option value="2">扣款</option>
            </select>
	    </div>
	    <div style=margin-top:10px;">
            <label style="float:left;">备注：</label>
		    <textarea name='disposeRemark' id='remark' placeholder='请务必填写备注' style="width:300px;height:50px;border:1px solid #EEE"></textarea>
		</div>
	</div>
</script>
<script type="text/javascript">
    $.updateBalance = function(id){
            var dialog = $.zydialogs.open($("#updateForm").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:'修改余额',
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确定',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    var money = $("#money").val();
                    var type = $("#type").val();
                    var remark = $("#remark").val();
                    var data = {
                        "userId" : id,
                        "money" : money,
                        "type" : type,
                        "remark" : remark
                    };

                    if(remark == ""){
                        $.ShowAlert("请务必填写备注");
                        return false;
                    }
                    
                    $.post("{{ u('User/updatebalance') }}",data,function(res){
                            $.ShowAlert(res.msg);
                            if (res.code == 0) {
                               window.location.reload();
                            }
                    },"json");

                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });

    }
</script>
@stop