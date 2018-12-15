@extends('proxy._layouts.base')
@section('css') 
@stop
@section('right_content')
	@yizan_begin 
		<yz:form id="yz_form" action="save"> 
			<yz:fitem name="name" label="代理账户" tip="请输入6-15位字母或数字"></yz:fitem> 
			<yz:fitem name="pwd" label="密码" tip="不修改请保留为空"></yz:fitem> 
			<yz:fitem name="realName" label="真实姓名"></yz:fitem> 
			<yz:fitem name="mobile" label="联系电话"></yz:fitem> 
			<php>
			 	if($login_proxy['level'] == 1) {
			 		$levelArr = [
			 			['val'=>2,'name'=>'二级'],
			 			['val'=>3,'name'=>'三级']
			 		];
			 	} elseif($login_proxy['level'] == 2){
			 		$levelArr = [ 
			 			['val'=>3,'name'=>'三级']
			 		];
			 	} 
			</php>
			<yz:fitem label="代理级别">
				@if(count($data['childs']) > 0 || $data['pid'] > 0)
				<yz:select name="level" options="$levelArr"  textfield="name" valuefield="val" attr="disabled=disabled" selected="$data['level']">
				</yz:select>
				<span class="ts ts1">（代理级别不能修改）</span>
				@else
				<yz:select name="level" options="$levelArr"  textfield="name" valuefield="val" selected="$data['level']">
				</yz:select>
				<span class="ts ts1"></span>
				@endif
			</yz:fitem>
			<div id="pid_proxy" class="u-fitem clearfix none">
	            <span class="f-tt">
	                 父代理:
	            </span>
                @if($login_proxy['level'] == 2)
                    <div class="f-boxr">
                        <select id="pid_sle" class="sle" name="pid"><option value="{{$login_proxy['id']}}"></option></select>
                    </div>
                @else
                    <div class="f-boxr">
                        <select id="pid_sle" class="sle" name="pid" disabled="disabled"></select>
                    </div>
                @endif

	            <input id="search_key" style="margin-left: 10px;width: 140px;" class="u-ipttext" type="input" placeholder="请输入要搜索的代理账户" /> 
	            <button type="button" id="proxy_search" class="btn "><i class="fa fa-search"></i> 搜索</button>
	        </div>
			<yz:fitem name="provinceId" label="所在地区">
				<yz:region pname="provinceId" pval="$data['province']['id']" showtip="1" cname="cityId" new="1" cval="$data['city']['id']" aname="areaId" aval="$data['area']['id']"></yz:region> 
			</yz:fitem>
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
            var curentProvinceId = "{{$login_proxy['provinceId']}}";
            var curentCityId = "{{$login_proxy['cityId']}}";
            var curentAreaId = "{{$login_proxy['areaId']}}";
            var curentMyLevelId = "{{$login_proxy['level']}}";
            var id = "{{$data['id']}}";
            var zx = ['1','18','795','2250'];
            @if($data['level'] == 3)
                $("#provinceId-form-item .f-boxr").append('<input id="third_area" class="u-ipttext" name="thirdArea" type="input" placeholder="请输入三级代理位置信息" value="{{$data["thirdArea"]}}" />');
                $("#provinceId").attr("disabled", "disabled");
                $("#cityId").attr("disabled", "disabled");
            @elseif($data['level'] == 2)
                $("#provinceId").attr("disabled", "disabled");
                $("#cityId").attr("disabled", "disabled");
            @elseif($data['level'] == 1)
                $("#areaId").attr("disabled", "disabled");
            @endif

            function refreshData(data){
				$.post("{{ u('Proxy/lists') }}",data,function(res){
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
				},'json');
			} 
			$("#level").change(function(){
				var level = $(this).val();
                if(level > 2){
                    if(curentMyLevelId < 2){
                        $("#pid_proxy").removeClass("none");
                        $("#pid_sle").removeAttr('disabled');
                        $("#areaId").removeAttr('disabled');

                        var obj = new Object();
                        obj.level = level;
                        refreshData(obj);
                    }else{
                        $("#pid_proxy").addClass("none");
                        $("#provinceId option[value='"+curentProvinceId+"']").attr("selected", true).trigger("change");
                        $("#provinceId").attr('disabled', 'disabled');

                        $("#cityId option[value='"+curentCityId+"']").attr("selected", true).trigger("change");
                        $("#cityId").attr('disabled', 'disabled');

                        $("#areaId option[value='"+curentAreaId+"']").attr("selected", true).trigger("change");
                        $("#areaId").attr('disabled', 'disabled');
                    }
                    if(level == 3){
                        if(id > 0){
                        }else{
                            $("#provinceId-form-item .f-boxr").append('<input id="third_area" class="u-ipttext" name="thirdArea" type="input" placeholder="请输入三级代理位置信息" />');
                        }
                    } else {
                        $("#third_area").remove();
                    }
				} else {
					$("#pid_proxy").addClass("none");
					$("#provinceId option[value='"+curentProvinceId+"']").attr("selected", true).trigger("change");
					$("#provinceId").attr('disabled', 'disabled');

                    $("#cityId option[value='"+curentCityId+"']").attr("selected", true).trigger("change");
                   	if($.inArray(curentProvinceId, zx) == -1){
                    	$("#cityId").attr('disabled', 'disabled');
                    }
					$("#third_area").remove();
				}
			}).trigger('change');
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
				refreshData(obj);
			});
		})
	</script>
@stop