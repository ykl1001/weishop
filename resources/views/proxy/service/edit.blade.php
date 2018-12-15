@extends('proxy._layouts.base')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
<style type="text/css">
	.m-spboxlst .f-boxr {
		width: 610px;
	}
	.f-boxr .btn{background: #efefef; border-color: #dfdfdf; color: #555;}
	.x-gebox{border: 1px solid #ddd; padding: 5px 20px;}
	.x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
	.closege{width: 20px; height: 20px;  display: inline-block; cursor: pointer; vertical-align: middle; margin-top: -2px; background: url( {{asset('wap/community/client/images/ico/close.png')}} ); background-size: 100% 100%;}
    .span-name{width:70px;}
    .p-avoidFee{width: 87px;}
</style>
@stop
<?php// dd($cateIds) ?>
@section('right_content')
@yizan_begin
<yz:form id="yz_form" action="save">
	<dl class="m-ddl">
		<dt>商家信息</dt>
		<dd class="clearfix">
			<yz:fitem name="name" label="商家名称"></yz:fitem>
			@if($data)
				<yz:fitem name="type" type="hidden" val="{{$data['type']}}"></yz:fitem>
				<yz:fitem name="storeType" type="hidden" val="{{$data['storeType']}}"></yz:fitem>
				@if($data['type'] == 2)
				<yz:fitem name="contacts" label="法人/店主"></yz:fitem>
				<yz:fitem name="serviceTel" label="服务电话"></yz:fitem>
				@endif
			@else
			<yz:fitem name="type" label="加盟类型">
				<yz:radio name="type" options="1,2" texts="个人,商家"></yz:radio>
			</yz:fitem>
			<yz:fitem label="店铺类型">
				<yz:select name="storeType" options="1,0" texts="全国店,周边店" selected="$data['storeType']"></yz:select>
			</yz:fitem>
			<yz:fitem name="contacts" label="负责人" pcss="sertype"></yz:fitem>
			<yz:fitem name="serviceTel" label="服务电话" pcss="sertype"></yz:fitem>
			@endif
            <div class="m-yhk m-ghkh" >
                <yz:fitem label="经营类型" pcss="send-cate-type send-cate-group">
                    <div class="input-group">
                        <table border="0">
                             <tbody>
                                <tr>
                                    <td rowspan="2">
                                        <select id="cate_1" name="cateIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
                                        @if(count($data['sellerCate']) > 0)
	                                        @foreach($data['sellerCate'] as $item)
	                                        	<option value="{{$item['cateId']}}" >{{$item['cates']['name']}}</option>
	                                        @endforeach
                                        @endif
                                        </select>
                                    </td>
                                    <td width="60" align="center" rowspan="2">
                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1', 1);">
                                            <span class="fa fa-2x fa-angle-double-left"> </span>
                                        </button>
                                        <br><br>
                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_2', 'cate_1');">
                                            <span class="fa fa-2x fa-angle-left"> </span>
                                        </button>
                                        <br><br>
                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2');">
                                            <span class="fa fa-2x fa-angle-right"> </span>
                                        </button>
                                        <br><br>
                                        <button type="button" class="btn btn-gray" onclick="$.optionMove('cate_1', 'cate_2', 1);">
                                            <span class="fa fa-2x fa-angle-double-right"> </span>
                                        </button>
                                        <input type="hidden" name="cateIds" id="cateIds">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       <select id="cate_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
                                       	@foreach($cateIds as $key => $val)
	                                       	@if($cateIds[$key]['childs'])
		                                       	<optgroup label="{{$val['name']}}" id="group_{{$val['id']}}">
		                                       		@foreach($cateIds[$key]['childs'] as $cs)
												    	<option value="{{$cs['id']}}" data-group="{{$val['id']}}" data-type="{{$val['type']}}">{{$cs['name']}}</option>
												    @endforeach
												</optgroup>
	                                        @else
												<option value="{{$val['id']}}" data-type="{{$val['type']}}">{{$val['name']}}</option>
	                                        @endif
                                        @endforeach
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="blank3"></div>
                    </div>
                </yz:fitem>
                <fitem type="script">
                <script type="text/javascript">
                    jQuery(function($){
                        $("#yz_form").submit(function(){
                            var ids = new Array();
                            $("#cate_1 option").each(function(){
                                ids.push(this.value);
                            })
                            $("#cateIds").val(ids);
                        })
                        $.optionMove = function(from, to, isAll){
                            var from = $("#" + from);
                            var to_group = to;
                            var to = $("#" + to);
                            var list;
                            if(isAll){
                                list = $('option', from);
                            }else{
                                list = $('option:selected', from);
                            }
                            list.each(function(){
                                if($('option[value="' + this.value + '"]', to).length > 0){
                                    $(this).remove();
                                } else {
                                    $('option', to).attr('selected',false);
                                    //还原时验证是否是子分类（向右移动）
                                    if(to_group == "cate_2"){
                                    	//DOM对象转化成JQuery对象
                                    	var $this = $(this);
                                    	if($this.attr("data-group") > 0){
                                    		//移动到对应分组下
                                    		$("#" + to_group + " #group_"+$this.attr("data-group")).append(this);
                                    	}else{
                                    		//移动到最后
                                    		to.append(this);
                                    	}

                                    }
                                    //（向左移动）
                                    else{
                                    	//移动到最后
                                    	to.append(this);
                                    }

                                }
                            });
                        }

                    });
                </script>
                </fitem>
            </div>
			<yz:fitem name="logo" label="商家LOGO" type="image"></yz:fitem>
			<yz:fitem name="image" label="商家背景图" type="image"></yz:fitem>
			<yz:fitem name="brief" label="简介" type="textarea"></yz:fitem>
		</dd>
		<yz:fitem name="provinceId" label="所在地区">
			<yz:region pname="provinceId" pval="$data['province']['id']" cname="cityId" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']" new="1"></yz:region>
		</yz:fitem>
		<!-- 周边店不显示，全国店显示，_address防止与mapPos中的address冲突 -->
		<yz:fitem name="_address" label="详细地址">
			<yz:map addressName="_address" pointName="_mapPoint" pointVal="$data['mapPoint']" addressVal="$data['address']"></yz:map>
		</yz:fitem>

		<!-- 周边店显示，全国店不显示 -->
		<yz:fitem name="mapPos" label="服务范围">
			<yz:mapArea name="mapPos" pointVal="$data['mapPoint']" addressVal="$data['address']" posVal="$data['mapPos']"></yz:mapArea>
		</yz:fitem>
	</dl>
	<dl class="m-ddl">
		<dt>认证信息</dt>
		<dd class="clearfix">
			<yz:fitem name="mobile" label="登录手机号"></yz:fitem>
			@if($data)
			<yz:fitem name="pwd"  label="密码" type="password" tip="不修改请保留为空"></yz:fitem>
			<yz:fitem label="认证状态">
				<php> $isAuthenticate = isset($data['isAuthenticate']) ? $data['isAuthenticate'] : 0 </php>
				<yz:radio name="isAuthenticate" options="1,0" texts="已认证,未认证" checked="$isAuthenticate"></yz:radio>
			</yz:fitem>
			@else
			<yz:fitem name="pwd"  label="密码" type="password">
				<attrs>
					<btip><![CDATA[如果手机号未注册会员，必须设置密码；<br/>如果手机号已经注册过会员，设置密码将重置会员登录密码；]]></btip>
				</attrs>
			</yz:fitem>
			@endif
            @if($data['type'] == 1)
                <yz:fitem name="contacts" label="真实姓名"></yz:fitem>
            @endif
		</dd>
	</dl>
	<dl class="m-ddl">
		<dt>营业设置</dt>
		@if($data)
		<dd class="clearfix" style="padding:15px;">
			@include('admin.common.service.showtime')
            @include('admin.common.service.sztime')
		</dd>
		@endif
		<dd class="clearfix">
			<!-- 全国店不显示 -->
			<yz:fitem label="起送费" name="serviceFee">
				<input type="text" name="serviceFee" class="u-ipttext" value="{{ $data['serviceFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
			</yz:fitem>
			<!-- 全国店不显示 -->
			<yz:fitem label="配送费" name="deliveryFee">
				<input type="text" name="deliveryFee" class="u-ipttext" value="{{ $data['deliveryFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
				<p class="mt10">
					<yz:checkbox name="isAvoidFee" options="1" texts="设置满免" checked="$data['isAvoidFee']"></yz:checkbox>
					满<input type="text" name="avoidFee" class="u-ipttext ml5 mr5 p-avoidFee p-disabled" value="{{ $data['avoidFee'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">免配送费
				</p>
			</yz:fitem>
			<yz:fitem label="佣金比例">
                  <input type="text" name="deduct" class="u-ipttext" value="{{ $data['deduct'] }}" onKeyUp="amount(this)" onBlur="overFormat(this)">
                  <span style="color:#000;">%</span>
                  <span style="color:#ccc;" class="ml10">佣金比例只能是 0% ~ 100% 范围</span>
			</yz:fitem>
			<!-- 全国店不显示 -->
			<yz:fitem label="货到付款" name="isCashOnDelivery">
				<yz:checkbox name="isCashOnDelivery" options="1" texts="支持货到付款" checked="$data['isCashOnDelivery']"></yz:checkbox>
  			</yz:fitem>
  			<!-- 全国店显示 周边店不显示 -->
			<yz:fitem label="退款地址" name="refundAddress">
				<input type="text" name="refundAddress" class="u-ipttext" value="{{ $data['refundAddress'] }}" style="width:300px">
			</yz:fitem>
		</dd>
	</dl>
	@if($data)
	<dl class="m-ddl">
		<dt>银行卡</dt>
		<dd class="clearfix">

			<div class="list-btns add_bank_btn">
	              <a href="javascript:;" class="btn mr5 addbank" >
	                添加银行卡
	            </a>
	        </div>

		<div class="m-tab">
		<table id="checkListTable" class="">
        <thead>
        	<tr><td class="" width="100" order="bank" code="bank"><span>开户行</span></td>
			<td class="" width="150" order="bankNo" code="bankNo"><span>卡号</span></td>
			<td class="" order="name" code="name" width="100"><span>开户名</span></td>
			<td class="" width="100" order="mobile" code="mobile"><span>手机号</span></td>
			<td style="text-align:center;white-space:nowrap;" width="60"><span>操作</span></td>
            </tr>
        </thead>
        <tbody id="banks">
			@foreach($data['banks'] as $item)
            <tr class="tr-68 tr-even" ><td class="" code="bank">{{$item['bank']}}</td>
			<td class="" style="text-align:left;" code="bankNo">{{$item['bankNo']}}</td>
			<td class="" style="text-align:left;" code="name">{{$item['name']}}</td>
			<td class="" code="mobile">{{$item['mobile']}}</td>
			<td class="">
				<p><a href="javascript:;" class=" red" onclick="$.RemoveItem(this, '{{ u('Service/delbank',['id'=>$item['id'],'sellerId'=>$data['id']])}}', '你确定要删除该数据吗？')" target="_self">删除</a></p>
			</td>
            </tr>
			@endforeach
        </tbody>
        </table>
        </div>
		</dd>
	</dl>
	@endif

	@if(!$data || $data['storeType'] == 1)
		<script type="text/javascript">
			$("#mapPos-form-item").addClass("none");
			$("#_address-form-item").removeClass("none");
			//全国店不显示
			$("#serviceFee-form-item,#deliveryFee-form-item,#isCashOnDelivery-form-item").addClass("none");
			//全国店显示
			$("#refundAddress-form-item").removeClass("none");
		</script>
	@else
		<script type="text/javascript">
			$("#mapPos-form-item").removeClass("none");
			$("#_address-form-item").addClass("none");
			//周边店显示
			$("#serviceFee-form-item,#deliveryFee-form-item,#isCashOnDelivery-form-item").removeClass("none");
			//周边店不显示
			$("#refundAddress-form-item").addClass("none");
		</script>
	@endif
</yz:form>


@yizan_end
@stop
@section('js')
    <script type="text/tpl" id="serviceContent">
	<div style="width:500px;padding:10px;">
	    <div class="g-szzllst pt10">
						<div class="u-fitem clearfix ">
				            <div class="">
                                <span class="f-tt span-name">
                                     卡　　号：
                                </span>
				                  <input type="text" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" name="bankNo" id="bankNo" class="u-ipttext" maxlength="20">
				            </div>
				        </div>
						<div class="u-fitem clearfix ">

				            <div class="">
				            <span class="f-tt span-name">
				                 开  户  行：
				            </span>
				                  <input type="text" name="bank" id="bank" class="u-ipttext">
				            </div>
				        </div>
						<div class="u-fitem clearfix ">

				            <div class="">
				            <span class="f-tt span-name">
				                 户　　主：
				            </span>
				                  <input type="text" name="bank_name" id="bank_name" class="u-ipttext" value="{{ $data['name'] }}">
				            </div>
				        </div>
						<div class="u-fitem clearfix ">

				            <div class="">
				            <span class="f-tt span-name">
				                手机号码：
				            </span>
				                  <input type="text" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" name="bank_mobile" id="bank_mobile" class="u-ipttext" maxlength="11">
				            </div>
				        </div>
				        <div class="u-fitem clearfix ">

				            <div class="">
				            <span class="f-tt span-name">
				                验  证  码：
				            </span>
				                <input type="text" name="verifyCode" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" id="verifyCode" class="u-ipttext" maxlength="6"> <td class="tdtr">
								<button href="javascript:;" class="btn verify" style="line-height:28px;" onclick="$.getVerify();">获取验证码</button>
				            </div>
				        </div>
					</div>

	</div>
</script>
<script type="text/javascript">
	/**
	* 实时动态强制更改用户录入
	* arg1 inputObject
	**/
	function amount(th){
	    var regStrs = [
	        ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
	        ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
	        ['\\.(\\d?)\\.+', '.$1'], //禁止录入两个以上的点
	        ['^(\\d+\\.\\d{2}).+', '$1'] //禁止录入小数点后两位以上
	    ];
	    for(i=0; i<regStrs.length; i++){
	        var reg = new RegExp(regStrs[i][0]);
	        th.value = th.value.replace(reg, regStrs[i][1]);
	    }
	}

	/**
	* 录入完成后，输入模式失去焦点后对录入进行判断并强制更改，并对小数点进行0补全
	* arg1 inputObject
	* 这个函数写得很傻，是我很早以前写的了，没有进行优化，但功能十分齐全，你尝试着使用
	* 其实有一种可以更快速的JavaScript内置函数可以提取杂乱数据中的数字：
	* parseFloat('10');
	**/
	function overFormat(th){
	    var v = th.value;
	    if(v === ''){
	        v = '0.00';
	    }else if(v === '0'){
	        v = '0.00';
	    }else if(v === '0.'){
	        v = '0.00';
	    }else if(/^0+\d+\.?\d*.*$/.test(v)){
	        v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
	        v = inp.getRightPriceFormat(v).val;
	    }else if(/^0\.\d$/.test(v)){
	        v = v + '0';
	    }else if(!/^\d+\.\d{2}$/.test(v)){
	        if(/^\d+\.\d{2}.+/.test(v)){
	            v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
	        }else if(/^\d+$/.test(v)){
	            v = v + '.00';
	        }else if(/^\d+\.$/.test(v)){
	            v = v + '00';
	        }else if(/^\d+\.\d$/.test(v)){
	            v = v + '0';
	        }else if(/^[^\d]+\d+\.?\d*$/.test(v)){
	            v = v.replace(/^[^\d]+(\d+\.?\d*)$/, '$1');
	        }else if(/\d+/.test(v)){
	            v = v.replace(/^[^\d]*(\d+\.?\d*).*$/, '$1');
	            ty = false;
	        }else if(/^0+\d+\.?\d*$/.test(v)){
	            v = v.replace(/^0+(\d+\.?\d*)$/, '$1');
	            ty = false;
	        }else{
	            v = '0.00';
	        }
	    }
	    th.value = v;
	}
	// function txt_onchange(sender, min)
	// {
	// 	var value = parseFloat(sender.value);

	// 	if(isNaN(value))
	// 	{
	// 		sender.value = "0";
	// 	}
	// 	else
	// 	{
	// 		if(value < min)
	// 		{
	// 			sender.value = min;
	// 		}

	// 		sender.value = value.toFixed(2);
	// 	}
	// }
	// function txt_onchanges(sender, 0, 100)
	// {
	// 	var value = parseFloat(sender.value);

	// 	if(isNaN(value))
	// 	{
	// 		sender.value = "0";
	// 	}
	// 	else
	// 	{
	// 		if(value < 0)
	// 		{
	// 			sender.value = 0;
	// 		}

	// 		if(value > 100)
	// 		{
	// 			sender.value = 100;
	// 		}

	// 		sender.value = value.toFixed(2);
	// 	}
	// }
	$(function(){
		$(".addbank").click(function() {
            var dialog = $.zydialogs.open($("#serviceContent").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:'银行卡信息',
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确认',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    var bank = $("#bank").val();
                    var bankNo = $("#bankNo").val();
                    var mobile = $("#bank_mobile").val();
                    var name = $("#bank_name").val();
                    var verifyCode = $("#verifyCode").val();
                    var id = "{{$data['id']}}";
                    $.post("{{ u('Service/banksave') }}", {'bank':bank,'bankNo':bankNo,'mobile':mobile,'id':id,'name':name,'verifyCode':verifyCode} ,function(res){
                        if(res.code == 0) {
                            $("#banks").empty();
                            if (res.data.length > 0) {
                                var banksinfo = res.data;
                                for (var i = 0; i < banksinfo.length; i++) {
                                	url = "{{ u('Service/delbank') }}?id="+banksinfo[i].id+"&sellerId="+banksinfo[i].sellerId;
                                    var htmls = '<tr class="tr-68 tr-even" ><td class="" code="bank">'+banksinfo[i].bank +'</td><td class="" style="text-align:left;" code="bankNo">'+banksinfo[i].bankNo +'</td><td class="" style="text-align:left;" code="name">'+banksinfo[i].name +'</td><td class="" code="mobile">'+banksinfo[i].mobile +'</td><td class=""><p><a href="javascript:;" class=" red" onclick="$.RemoveItem(this, '+url+', "你确定要删除该数据吗？")" target="_self">删除</a></p></td></tr>';
                                    $("#banks").append(htmls);
                                };
                            };
                            $.zydialogs.close("SET_GROUP_WEEBOX");
                            $(".add_bank_btn").remove();
                        }else{
                            $.ShowAlert(res.msg);
                        }
                    },"json");
                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
		})



		$("input:radio[name='type']").change(function(){
			if( $(this).val() == 2 ) {
				$('.sertype').show();
				$('.pertype').hide();
			}else{
				$('.sertype').hide();
				$('.pertype').show();
			}
		});
        $.getVerify = function(){
            var mobile = $("#bank_mobile").val();
            var sellerId = "{{$data['id']}}";
            if(sellerId !=　""){
                var reg = /^1[\d+]{10}$/;
                if(!reg.test(mobile)){
                    $.ShowAlert('请输入正确的手机号码');
                    return false;
                }
                time();
                $.post("{{ u('Service/userverify') }}",{mobile:mobile},function(result){
                    $(".system_msg").text(result.msg);
                },'json');
            }else{
                $.ShowAlert('服务人员ID不能为空');
                return false;
            }
        }

		var wait = 60;
		function time() {
		    if (wait == 0) {
				$(".verify").removeAttr("disabled") ;
		        $(".verify").text("免费获取验证码");
		        $(".system_msg").text("");
		        wait = 60;
		    } else {
		        $(".verify").attr('disabled',"true");
		        $(".verify").text(wait + "秒后重新获取验证码");
		        wait--;
		        setTimeout(function () {
		            time();
		        },
		        1000)
		    }
		}
	})

	$("input[name='isCashPay']").click(function(){
		if(this.checked){
			$(this).val(1);
		} else {
			$(this).val(0);
		}
	});
	@if($data && $data['isCheck'] !== 0)
	$(".u-addspbtn").hide();
	@endif
</script>
@include('seller._layouts.alert')
<script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script>
<script type="text/tpl" id="deliveryrow">
	<div class="x-gebox" style="margin-top:3px;">
		开始时间：<input type="text" name="_stime[]" class="u-ipttext" placeholder="00:00"/>
		结束时间：<input type="text" name="_etime[]" class="u-ipttext" placeholder="12:00"/>
		<i class="closege"></i>
    </div>
</script>
<script type="text/javascript">
	$(".add_delivery").click(function(){
		if ($(".x-gebox").length == 3) {
			$.ShowAlert('配送时间段最多添加3个');
			return false;
		};
		$(".delivery_panel").append($("#deliveryrow").html());
		if($(".x-gebox").length > 0){
			$(".delivery_panel").parent().show();
		}
	});
	$(document).on('click','.closege',function(){
		$(this).parent().remove();
		if($(".x-gebox").length <= 0){
			$(".delivery_panel").parent().hide();
		}
	});
	$(function(){
		$("input[name='deduct']").attr("maxlength","6");

		//全国周边店切换
		$("#storeType").change(function(){
			//切换经营类型
			$.optionMove('cate_1', 'cate_2', 1);

			//重置经营类型
			$("select#cate_2 option").removeClass("none");
			if( $("#storeType").val() == 1 )
			{
				$("select#cate_2 option").each(function(k, v){
					if( $(this).data('type') == 2 )
					{
						$(this).addClass('none');
					}
				});
			}
			
			if($(this).val() == 1)
			{
				$("#mapPos-form-item").addClass('none');
				$("#_address-form-item").removeClass("none");
				//全国店不显示
				$("#serviceFee-form-item,#deliveryFee-form-item,#isCashOnDelivery-form-item").addClass("none");
				//全国店显示
				$("#refundAddress-form-item").removeClass("none");

			}
			else
			{
				$("#mapPos-form-item").removeClass('none');
				$("#_address-form-item").addClass("none");
				//周边店显示
				$("#serviceFee-form-item,#deliveryFee-form-item,#isCashOnDelivery-form-item").removeClass("none");
				//周边店不显示
				$("#refundAddress-form-item").addClass("none");
			}
		});
	})

	//检测经营类型
	$.checkeCate = function() {
		if( $("#storeType").val() == 1 )
		{
			$("select#cate_2 option").each(function(k, v){
				if( $(this).data('type') == 2 )
				{
					$(this).addClass('none');
				}
			});
		}
	}
	
	$.checkeCate();
</script>
@stop