@extends('admin._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="代理账户" tip="请输入6-15位字母或数字"></yz:fitem> 
			<yz:fitem name="pwd" label="密码" tip="不修改请保留为空"></yz:fitem> 
			<yz:fitem name="realName" label="真实姓名"></yz:fitem> 
			<yz:fitem name="mobile" label="联系电话"></yz:fitem> 
			<yz:fitem label="代理级别">
				@if(count($data['childs']) > 0 || $data['pid'] > 0)
				<yz:select name="level" options="1,2,3" texts="一级,二级,三级" attr="disabled=disabled" selected="$data['level']">
				</yz:select>
				<span class="ts ts1">（代理级别不能修改）</span>
				@else
				<yz:select name="level" options="1,2,3" texts="一级,二级,三级" selected="$data['level']">
				</yz:select>
				<span class="ts ts1"></span>
				@endif
			</yz:fitem>
			<div id="pid_proxy" class="u-fitem clearfix none">
	            <span class="f-tt">
	                 父代理:
	            </span>
	            <div class="f-boxr">
	                <select id="pid_sle" class="sle" name="pid" disabled="disabled"></select>
	            </div>
	            <input id="search_key" style="margin-left: 10px;width: 140px;" class="u-ipttext" type="input" placeholder="请输入要搜索的代理账户" /> 
	            <button type="button" id="proxy_search" class="btn "><i class="fa fa-search"></i> 搜索</button>
	        </div>
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" showtip="1" cname="cityId" new="1" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region> 
			</yz:fitem>
			<input name="" id="provinceIdsy" type="hidden" value="{{$data['province']['id']}}" /> 
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
			<yz:fitem label="状态">
				<php> $status = isset($data['status']) ? $data['status'] : 1 </php>
				<yz:radio name="status" options="1,0" texts="开启,关闭" checked="$status"></yz:radio>
			</yz:fitem>
		</yz:form> 
	@yizan_end
	<script type="text/javascript"> 
		$(function(){

			//$("#provinceId").parent().find('.sle').attr('disabled', 'disabled');
			@if($data['level'] == 3) 
				$("#provinceId-form-item .f-boxr").append('<input id="third_area" class="u-ipttext" name="thirdArea" type="input" placeholder="请输入三级代理位置信息" value="{{$data["thirdArea"]}}" />');
				$("#provinceId").attr("disabled", "disabled");
				$("#cityId").attr("disabled", "disabled");
			@elseif($data['level'] == 2)
				$("#provinceId").attr("disabled", "disabled");
                $("#cityId").attr("disabled", "disabled");
                $("#areaId").attr("disabled", "disabled");
				$("#provinceIdsy").attr("name","provinceId").val($("#provinceId option:selected").val());
            @elseif($data['level'] == 1)
                $("#areaId").attr("disabled", "disabled");
			@endif
			function refreshData(data,level){
				$.post("{{ u('Proxy/index') }}",data,function(res){
					var optionsStr = "";
					if(res.length > 0){
						for(var i=0; i<res.length;i++){
							optionsStr += "<option value='"+res[i].id+"' data-provinceid="+res[i].provinceId+" data-cityid="+res[i].cityId+" data-areaid="+res[i].areaId+">"+res[i].name+"</option>";
						}
					} else {
						optionsStr += "<option value='0'>未找到代理信息</option>"; 
						$('#provinceId option:first').attr("selected", true);
						$("#provinceId").removeAttr("disabled").trigger("change");
					}
					$("#pid_sle").html(optionsStr).trigger("change");
					if(level == 2){
						$("#provinceIdsy").attr("name","provinceId").val($("#provinceId option:selected").val());
					}
				},'json');
			} 
			$("#level").change(function(){
				var level = $(this).val();
				if(level > 1){
					$("#pid_proxy").removeClass("none");
                    $("#pid_sle").removeAttr('disabled');
                    $("#areaId").removeAttr('disabled');
					var obj = new Object();
					obj.level = level; 
					refreshData(obj,level);
					if(level == 3){
						$("#provinceId-form-item .f-boxr").append('<input id="third_area" class="u-ipttext" name="thirdArea" type="input" placeholder="请输入三级代理位置信息" />');
					} else {
						$("#third_area").remove();
					}
				} else {
					$("#pid_proxy").addClass("none");
					$("#pid_sle").attr('disabled', 'disabled');
					$("#provinceId").removeAttr('disabled');

                    var zx = ['1','18','795','2250'];
                    var provinceId = $(this).val();
                    if($.inArray(provinceId, zx) == -1){
                        //不是4个直辖市
                        $("#cityId").removeAttr("disabled");
                        $("#areaId").attr("disabled",true);
                    }else{
                        $("#cityId").attr("disabled", "disabled");
                    }

					$("#third_area").remove();
				}
			});
			$("#pid_sle").change(function(){
				var obj = $(this).find("option:selected");
				var provinceId = obj.data('provinceid');
				var cityId = obj.data('cityid');
				var areaId = obj.data('areaid');
				if(provinceId != 0){
					$("#provinceId option[value='"+provinceId+"']").attr("selected", true).trigger("change");
					$("#provinceId").attr("disabled", "disabled");
				} 
				if(cityId != 0){
					$("#cityId option[value='"+cityId+"']").attr("selected", true).trigger("change");
					$("#cityId").attr("disabled", "disabled");
				}
                if(areaId != 0){
                    $("#areaId option[value='"+areaId+"']").attr("selected", true).trigger("change");
                    $("#areaId").attr("disabled", "disabled");
                }
			});
			$("#provinceId").change(function(){
				if($("#level").val() == 1){
                    var zx = ['1','18','795','2250'];
                    var provinceId = $(this).val();

                    if($.inArray(provinceId, zx) == -1){
                        //不是4个直辖市
                        $("#cityId").removeAttr("disabled");
                        $("#areaId").attr("disabled",true);
                    }else{
                        $("#cityId").attr("disabled", "disabled");
                    }
				} else {
					$("#cityId").removeAttr("disabled");
				}
			});
            $("#cityId").change(function(){
                if($("#level").val() == 1){
                    $("#areaId").attr("disabled", "disabled");
                } else {
                    $("#areaId").removeAttr("disabled");
                }
            });
			$("#proxy_search").click(function(){
				var obj = new Object();
				obj.level = $("#level").val();
				obj.name = $("#search_key").val(); 
				refreshData(obj,0);
			});
		})
	</script>
@stop