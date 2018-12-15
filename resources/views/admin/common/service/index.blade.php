@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
	<?php
		$morefxCss = FANWEFX_SYSTEM === false ? 'none' : '';
	?>
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
            <linkbtn label="导出到EXCEL" type="export" url="{{ u('Service/export', $search_args) }}"></linkbtn>
            <linkbtn label="删除" type="destroy"></linkbtn>
            <linkbtn label="批量设置分销方案" id="morefx" url="#" css="{{$morefxCss}}"></linkbtn>
        </btns>
        <table checkbox="1">
            <columns>
                <column code="id" label="编号" width="40"></column>
                <column code="name" label="商家名" align="left" width="50"></column>
                <column label="加盟类型" width="40">
                    @if($list_item['type'] == 1)
                        个人加盟
                    @elseif($list_item['type'] == 2)
                        商家加盟
                    @elseif($list_item['type'] == 3)
                        物业公司
                    @else
                        未知
                    @endif
                </column>
                <column label="店铺类型" width="40">
                    <!-- 物业不显示店铺类型 -->
                    @if($list_item['type'] == 3)
                        -
                    @else
                        @if($list_item['storeType'] == 1)
                            全国店
                        @elseif($list_item['storeType'] == 0)
                            周边店
                        @endif
                    @endif
                </column>
                <column code="balance" label="余额" align="center" width="50"></column>
                <column code="lockMoney" label="冻结金额" align="center" width="60"></column>
                <column code="goods" label="商品管理" align="center"  width="280">
                    <p>
                        <a href="{{ u('Service/goodslists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">商品({{$list_item['goodscount']}})</a>&nbsp;&nbsp;
                        @if($list_item['storeType'] == 1)
                            <a href="###" style="color:#ccc;cursor:default;">服务(0)</a>&nbsp;&nbsp;
                        @elseif($list_item['storeType'] == 0)
                            <a href="{{ u('Service/servicelists',['sellerId'=>$list_item['id']]) }}" style="color:grey;">服务({{$list_item['servicecount']}})</a>&nbsp;&nbsp;
                        @endif
                        <a href="{{ u('Staff/index',['sellerId'=>$list_item['id']]) }}" style="color:grey;">人员({{$list_item['staffcount']}})</a>
                        <a href="{{ u('Service/catelists',['sellerId'=>$list_item['id'], 'type'=>1]) }}" style="color:grey;">商品分类({{$list_item['goodscatecount']}})</a>&nbsp;&nbsp;
                        @if($list_item['storeType'] == 1)
                            <a href="###" style="color:#ccc;cursor:default;">服务分类(0)</a>&nbsp;&nbsp;
                        @elseif($list_item['storeType'] == 0)
                            <a href="{{ u('Service/catelists',['sellerId'=>$list_item['id'], 'type'=>2]) }}" style="color:grey;">服务分类({{$list_item['servicecatecount']}})</a>&nbsp;&nbsp;
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
                <column code="creatTime" label="入驻时间" width="120">
                    <p>{{ yztime($list_item['createTime']) }}</p>
                </column>

                <column code="Cancel" label="订单" width="110">
                    <p>全部 （{{$list_item['ordercount']}}） 取消（<php> echo $list_item['sellerCancel'] +  $list_item['userCancel']; </php>）</p>
                </column>
                {{--<column code="sellerCancel" label="商家取消订单次数" width="110"></column>--}}
                {{--<column code="userCancel" label="会员取消订单次数" width="110"></column>--}}
                <column code="status" label="状态" width="40" type="status"></column>
                <actions width="120">
                    <action type="edit" css="blu" url="javascript:$.updateBalance('{{ $list_item['id'] }}');" label="修改余额"></action>&nbsp;&nbsp;
                    <action type="edit" css="blu"></action>&nbsp;&nbsp;
                    @if($list_item['id'] != ONESELF_SELLER_ID)
                        <action type="destroy" css="red"></action>
                    @endif

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
    	    <div style="margin-top:10px;">
                <label style="float:left;">备注：</label>
    		    <textarea name='disposeRemark' id='remark' placeholder='请务必填写备注' style="width:300px;height:50px;border:1px solid #EEE"></textarea>
    		</div>
    	</div>
    </script>

    <script type="text/tpl" id="checkfx">
        <div style="width:350px;padding:10px;">
            <div style="height:40px;line-height:40px;text-align:center">
                <select name="schemeId" class="sle" id="schemeId">
                    @foreach($schemeId as $key => $value)
                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                    @endforeach
                </select>
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
                        "sellerId" : id,
                        "money" : money,
                        "type" : type,
                        "remark" : remark
                    };
                    if(remark == ""){
                        $.ShowAlert("请务必填写备注");
                        return false;
                    }

                    $.post("{{ u('Service/updatebalance') }}",data,function(res){
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

        $("#morefx").click(function(){
            var dialog = $.zydialogs.open($("#checkfx").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:'将选中的商户批量设置为以下方案',
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确定',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    $.checkfx();

                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });

            
        });

        $.checkfx = function(){
            var ids = [];
            $("tr div.checker span.checked input").each(function(k, v){
                if(!isNaN($(this).val()))
                {
                    ids[k] = $(this).val();
                }
            });

            var schemeId = $("#schemeId").val();

            if(ids.length < 1)
            {
                $.ShowAlert("请至少选择一个商家");
                return false;
            }
            
            $.post("{{ u('Service/morefx') }}", {'ids':ids, 'schemeId':schemeId}, function(result){
                $.ShowAlert(result.msg);

                if(result.code == 0)
                {
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                    setTimeout(function(){
                        window.location.reload();
                    }, 3000);
                }
            });

            
        }
    </script>
@stop