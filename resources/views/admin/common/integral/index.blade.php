@extends('admin._layouts.base')
@section('css')
<style>
    .y-jfimg {
        height:100% !important;
    }
</style>
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        @yizan_yield("search")
        <search method="get">
            <row>
                <item name="name" label="商品名称"></item>
                <btn type="search"></btn>
            </row>
        </search>
        @yizan_stop
        <btns>
            <linkbtn label="积分商品设置" url="{{ u('Integral/create') }}" css="btn-green"></linkbtn>
            <linkbtn label="删除" type="destroy"></linkbtn>
        </btns>
        <table checkbox="1">
            <columns>
                <column code="id" label="编号" width="100" iscut="1"></column>
                <column code="name" label="商品名称" width="100" iscut="1"></column>
                <column code="exchangeIntegral" label="积分" width="100" iscut="1"></column>
                <column code="status" label="是否配送" width="100">
                    {{$list_item['isVirtual'] ? "是" : "否"}}
                </column>
                <column code="status" label="上架状态" width="100">
                    {{$list_item['status'] ? "上架" : "下架"}}
                </column>
                <actions width="150">
                    <action type="edit" css="blu"></action>
                    <a class=" blu" data-pk="1" target="_self" onclick="$.exchangeIntegral({{$list_item['id']}},{{$list_item['exchangeIntegral']}})">修改积分</a>
                    <action type="destroy" css="red"></action>
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/tpl" id="exchangeIntegral">
	<div style="width:100%;text-align:center;padding:10px;">
		<span style=" line-height: 30px;">积分修改：</span> <input style="width:50%" type="text" maxlength="7" id="exchangeIntegral_show" value="" name="integral" class="u-ipttext" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
	</div>
</script>
<script type="text/tpl" id="exchangeIntegraldel">
	<div style="width:90%;text-align:center;padding:10px;">
		确定删除该条商品积分设置
	</div>
</script>
<script type="text/javascript">
    $.exchangeIntegral = function(id,exchangeIntegral){
        var dialog = $.zydialogs.open($("#exchangeIntegral").html(), {
            boxid:'SET_GROUP_WEEBOX',
            width:300,
            title:'积分编辑设置',
            showClose:true,
            showButton:true,
            showOk:true,
            showCancel:true,
            okBtnName: '确认修改',
            cancelBtnName: '取消',
            contentType:'content',
            onOk: function(){

                var  exchangeIntegral_val = $("#exchangeIntegral_show").val();
                var data = {}
                if(exchangeIntegral_val == "" || exchangeIntegral_val == 0 ){
                    $.ShowAlert("请输入修改的积分");
                }else if(exchangeIntegral_val == exchangeIntegral){
                    $.ShowAlert("与原积分相同");
                }
                else{
                    dialog.setLoading();
                    data[id] = exchangeIntegral_val;
                    $.post("{{ u('Integral/saveIntegral') }}",{'integral':exchangeIntegral_val,'id':id},function(res){
                        $.ShowAlert("操作成功");
                        dialog.setLoading(false);
                        if(res.code==88889) {
                            window.location.reload();
                        }
                    },'json');
                }
            },
            onCancel:function(){
                $.zydialogs.close("SET_GROUP_WEEBOX");
            }
        });

        $("#exchangeIntegral_show").val(exchangeIntegral);
    }

    $.exchangeIntegraldel = function(id){
        var dialog = $.zydialogs.open($("#exchangeIntegraldel").html(), {
            boxid:'SET_GROUP_WEEBOX',
            width:300,
            title:'确定删除',
            showClose:true,
            showButton:true,
            showOk:true,
            showCancel:true,
            okBtnName: '确认',
            cancelBtnName: '取消',
            contentType:'content',
            onOk: function(){
                dialog.setLoading();
                var data = {}
                data[id] = 0;
                $.post("{{ u('Integral/save') }}",{'data':data ,'del':1},function(){
                    dialog.setLoading(false);
                    window.location.reload();
                },'json');
            },
            onCancel:function(){
                $.zydialogs.close("SET_GROUP_WEEBOX");
            }
        });
    }
</script>
@stop
