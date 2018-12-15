@extends('admin._layouts.base')
@section('css') 
@stop 
@section('right_content')
@yizan_begin 
		<yz:list>
    		<search url="{{ $url }}">
    			<row>
                    <item name="name" label="昵称"></item>
                    <item name="mobile" label="电话"></item>
    				<btn type="search"></btn>
    			</row>
    		</search>

            <yz:tabs>
                <navs>
                    <nav name="status" label="全部">
                        <attrs>
                            <url>{{ u('Sharechapman/index',['status'=>'0','nav'=>1]) }}</url>
                            <css>@if( $nav == 1) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="待处理">
                        <attrs>
                            <url>{{ u('Sharechapman/index',['status'=>'1','nav'=>2]) }}</url>
                            <css>@if( $nav == 2) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="已通过">
                        <attrs>
                            <url>{{ u('Sharechapman/index',['status'=>'2','nav'=>3]) }}</url>
                            <css>@if( $nav == 3 ) on @endif</css>
                        </attrs>
                    </nav>
                    <nav name="status" label="已拒绝">
                        <attrs>
                            <url>{{ u('Sharechapman/index',['status'=>'3','nav'=>4]) }}</url>
                            <css>@if( $nav == 4 ) on @endif</css>
                        </attrs>
                    </nav>
                </navs>
            </yz:tabs>
			<table>
				<columns>
                    <column label="昵称" code="name" width="60">
                        <p>{{$list_item['user']['name']}}</p>
                    </column>
                    <column label="电话" code="mobile" width="60">
                        <p>{{$list_item['user']['mobile']}}</p>
                    </column>
                    <column label="申请需求" width="250">
                        <p>{{$list_item['remark']}}</p>
                    </column>
                    <column label="状态" code="status" width="40">
                            {{ Lang::get('admin.status_txian.'.$list_item['status']) }}
                    </column>
                    <actions width="80">
                        @if($list_item['status'] != 1)
                            <action label="通过" click="WithdrawMoneys('1','{{ $list_item['id'] }}' )" css="blu"></action>
                            <action label="拒绝" click="WithdrawMoneys('-1', '{{ $list_item['id'] }}' )" css="red mt10"></action>
                        @endif
                    </actions>
				</columns>
			</table>
		</yz:list> 
	@yizan_end
@stop
@section('js')
<script type="text/tpl" id="WithdrawMoneys">
<div style="width:400px;text-align:center;margin:15px 0"> 
	<span class="msgs"style="display: block;text-align:center;margin:5px"></span>
	<textarea name='disposeRemark' id='disposeRemark' placeholder='请填写处理的备注。' style="width:380px;height:100px;border:1px solid #EEE"></textarea>
</div>
</script> 
<script type="text/javascript"> 
//提现处理
function WithdrawMoneys(type,id) {
	if(type == 1){
		var name = "确认";
	}else{
		var name = "拒绝";
	}
    var dialog = $.zydialogs.open($("#WithdrawMoneys").html(), {
        boxid:'SET_GROUP_WEEBOX',
        width:300,
        title:name+'审核',
        showClose:true,
        showButton:true,
        showOk:true,
        showCancel:true,
        okBtnName: name+"审核",
		cancelBtnName: '取消',
        contentType:'content',
        onOk: function(){
     		dialog.setLoading();
            var query = new Object();
            query.id = id;
        	query.content = $("#disposeRemark").val();
            if(type == "-1"){ //拒绝
                query.status = "{{ STATUS_WITHDRAW_REFUSE }}";
                $.post("{{ u('Sharechapman/dispose')  }}",query,function(result){
                    dialog.setLoading(false);
                    if(result.status == true){
                        window.location.reload();
                    }else{
                        $.ShowAlert(result.msg);
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                },'json');
            }else{
             query.status = "{{ STATUS_WITHDRAW_PASS }}";
             $.post("{{ u('Sharechapman/dispose')  }}",query,function(result){
                    dialog.setLoading(false);
                    if(result.status == true){
                        window.location.reload();
                    }else{
                        $.ShowAlert(result.msg);
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                },'json');
            }
        },
		onCancel:function(){
            $.zydialogs.close("SET_GROUP_WEEBOX");
        }  
	});	
}
</script>  
@stop