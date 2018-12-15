@extends('admin._layouts.base')
@section('css')
<style type="text/css">
	#searchSeller{margin-left: 5px;}
	#mobile{width: 100px;}
	.setprice{width: 60px;margin: 0px 5px;}
	.allprice{margin-left: 20px; color: #999;}
	.ts,.ts3{color: #999;margin-left: 5px;vertical-align:middle;}
</style>
@stop
@section('right_content')
	@yizan_begin
		<yz:form id="yz_form" action="serviceSave"> 
			<input type="hidden" name="type" value="{{$data['type']}}" />
			<yz:fitem name="name" label="服务标题"></yz:fitem> 
			<yz:fitem label="服务分类">
				<yz:select name="cateId" options="$cate" textfield="name" valuefield="id" selected="$data['cate']['id']"></yz:select>
			</yz:fitem>
			<yz:fitem label="商品标签">
				<yz:select name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
				<yz:select name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
			</yz:fitem>
			<div id="price-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                价格:
	            </span>
	            <div class="f-boxr">
	                <input type="text" name="price" id="price" class="u-ipttext" value="{{$data['price']}}"  onKeyUp="amount(this)" onBlur="overFormat(this)">
	            </div>
	        </div>
			<!-- <yz:fitem name="price" label="价格"></yz:fitem>  -->
			<div id="duration-form-item" class="u-fitem clearfix ">
	            <span class="f-tt">
	                服务时长:
	            </span>
	            <div class="f-boxr">
	                <input type="text" name="duration" id="duration" class="u-ipttext" value="{{$data['duration']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">
	            </div>
	        </div>
			<!-- <yz:fitem name="duration" label="服务时长"></yz:fitem>  -->
			<yz:fitem label="单位"> 
				<php> $unit = (int)$data['unit'] </php>
				<yz:radio name="unit" options="0,1" texts="分钟,小时" checked="$unit"></yz:radio>
			</yz:fitem>
			<yz:fitem label="服务图片">
				<yz:imageList name="images." images="$data['images']"></yz:imageList>
				<div><small class='cred pl10 gray'>建议尺寸：750px*750px，支持JPG/PNG格式</small></div>
			</yz:fitem>
            <yz:fitem name="brief" label="服务描述">
                <yz:Editor name="brief" value="{{ $data['brief'] }}"></yz:Editor>
            </yz:fitem>

			</yz:fitem>   
			<yz:fitem label="选择员工" pcss="send-user-type send-user-group hidden">
			    <div class="input-group">
			    	<table border="0">
		                 <tbody>
		                 	<tr>
			                    <td rowspan="2">
			                        <select id="user_1" name="staffIds" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:260px;">
			                        @foreach($data['staffIds'] as $item)
									<option value="{{$item['id']}}" >{{$item['name']}}</option>
			                        @endforeach
			                        </select>
			                    </td>
			                    <td width="60" align="center" rowspan="2">
			                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1', 1);">
			                            <span class="fa fa-2x fa-angle-double-left"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_2', 'user_1');">
			                            <span class="fa fa-2x fa-angle-left"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2');">
			                            <span class="fa fa-2x fa-angle-right"> </span>
			                        </button>
			                        <br><br>
			                        <button type="button" class="btn btn-gray" onclick="$.optionMove('user_1', 'user_2', 1);">
			                            <span class="fa fa-2x fa-angle-double-right"> </span>
			                        </button>
			                        <input type="hidden" name="staffIds" id="users">
			                    </td>
			                    <td width="230" style="padding:0; height:35px;">
			                        <input type="text" class="u-ipttext" placeholder="搜索员工" id="fansName" style="width:140px;hieght:30px;">
			                        <a href="javascript:;" id="fansNameBtn" class="btn btn-gray btn-success input-image-select">
			                        <i class="fa fa-search"></i></a>
			                    </td>
			                </tr>
			                <tr>
			                    <td>
			                       <select id="user_2" class="form-control" multiple="multiple" style="min-width:200px; *width:200px; height:220px;"> 
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
					var sellerId = {{ ONESELF_SELLER_ID }};
				    $("#yz_form").submit(function(){
				        var ids = new Array(); 
				        $("#user_1 option").each(function(){
				            ids.push(this.value);
				        })
				        $("#users").val(ids);
				    })
				    $.optionMove = function(from, to, isAll){
				        var from = $("#" + from);
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
				                to.append(this);
				            }
				        });
				    }
			
					$('input[name="userType"]').change(function(){ 
						var type = $("input[name='userType']:checked").val(); 
						if(type==0){
							$('.send-user-group').addClass("hidden");
						}else{
							$('.send-user-group').removeClass("hidden");
						}
					});

		             $('#fansNameBtn').click(function(){
						var u_id = new Array(); 
		                $.post("{{ u('OneselfService/search') }}",{"sellerId": sellerId,"name":$("#fansName").val()},function(result){
		                    if(!result || result.length < 1){ 
		                        $('#user_2').html("<option value='0' disabled='true'>未搜索到员工</option>");
		                    } else {
		                        var html = '';
								$("#user_1 option").each(function(){
									u_id.push(this.value);
								})
		                        $.each(result, function(index,e){
									// console.log(u_id.indexOf(result[index].id));
		                        	if (u_id.indexOf(result[index].id) == -1){
										html += " <option class='uid" + e.id + "' value=" + e.id + ">" + e.name + "</option>";													
									}
		                        });
		                        $('#user_2').html(html);
		                    }
		                },'json');
		            }); 
		            $('#fansNameBtn').click();

		            $("input[name=sendType]").change(function() {
		            	/* Act on the event */
		            	var type = $("input[name='sendType']:checked").val(); 
						if(type==1){
							$('#args-form-item').addClass("hidden");
						}else{
							$('#args-form-item').removeClass("hidden");
						}
		            });
		            $('#args-form-item').addClass("hidden");
				});
			</script>	
			</fitem> 
			<yz:fitem label="服务状态"> 
				<php> $status = (int)$data['status'] </php>
				<yz:radio name="status" options="0,1" texts="下架,上架" checked="$status"></yz:radio>
			</yz:fitem>
			<yz:fitem name="sort" label="排序"></yz:fitem>  
		</yz:form>
	@yizan_end
@stop
@section('js')
<script type="text/javascript">
var cate = eval( <?php echo json_encode($cate); ?> );
var editId = "<?php if(isset($data['seller']['id'])){ echo $data['seller']['id'];} ?>";
var cateId = "<?php if(isset($data['cate']['id'])){ echo $data['cate']['id'];} ?>";
var priceType = "{{ $priceType }}";
	$(function(){
		if( priceType == 1 ) {
			$("#ci").show();
		}
		else if(  priceType == 2 ) {
			$("#shi").show();
		}

		$("input[name='priceType']").change(function(){
			//按次计费
			if( $(this).val() == 1 ){
				$("#shi").hide();
				$("#ci").show();
			}
			//按时计费
			else{
				$("#ci").hide();
				$("#shi").show();
			}
		});

		$('#setprice_hour').blur(function(){
			var hour = $(this).val();
			if( hour > 0 ) {
				$('.ts4').text( hour );
			}else{
				$('.ts4').text( 0 );
			}
		});

		$('#setprice_money').blur(function(){
			var money = $(this).val();
			if( money > 0 ) {
				$('.ts5').text( money );
			}else{
				$('.ts5').text( 0 );
			}
			$('.city_price_box input.price').val(money);
		});
		
		$('#setprice_price').blur(function(){
			$('.city_price_box input.price').val( $(this).val() );
		});


		// if( editId > 0 ) {
			// $("#sellerId").html("<option value='"+editId+"' selected>{{$data['seller']['name']}}</option>");
			// $('.ts1').text( cate[cateId]['levelrel'] );
		// }

		$('#cateId').change(function(){
			$('.ts1').text( cate[$(this).val()]['levelrel'] );
		});

			$('#searchSeller').click(function(){
				clearts();
				var mobileName = $('#mobile').val();
				$.post("{{u('Order/getSellerInfo')}}",{"mobileName":mobileName},function(res){
					res = eval(res); 
					if(res.length>0){
						var html = "";
						$.each(res,function(n,value) {
							if(n<1){
								$('#mobile').val(value.mobile);
							}
							html += "<option value='"+value.id+"' data-mobile='"+value.mobile+"'>"+value.name+"</option>";  
						});
						$("#sellerId").html(html);
					}else{
						$("#sellerId").html("<option value='0'>请输入手机号或昵称</option>");
						$(".ts2").text('未查询到相关服务人员');
					}
					

				});
			});

			$("#sellerId").change(function(){
				$('#mobile').val( $("#sellerId option:checked").data('mobile') );
			});
	})

	function clearts() {
		$('.ts').text('');
	}

	$("#systemTagListPid").change(function(){
		var tagId = $(this).val();
		if(tagId == 0)
		{
			$("#systemTagListId").html('').addClass('none');
		}
		else
		{
			$.post("{{ u('SystemTagList/secondLevel') }}", {'pid': tagId}, function(res){

				if(res!='')
				{
					var html = '<option value=0>请选择</option>';
					$.each(res, function(k,v){
						html += "<option value='"+v.id+"'>"+v.name+"</option>";
					});
					$("#systemTagListId").html(html).removeClass('none');
				}
				else
				{
					$("#systemTagListId").html('').addClass('none');
					alert("当前分类暂无二级分类，请重新选择！");
				}
				
			});	
		}
	});
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

        function amounts(th){
            var regStrs = [
                ['^0(\\d+)$', '$1'], //禁止录入整数部分两位以上，但首位为0
                ['[^\\d\\.]+$', ''], //禁止录入任何非数字和点
                ['\\.(\\d?)+', '$1'], //禁止录入两个以上的点
                ['^(\\d+\\.\\d{0}).+', '$1'] //禁止录入小数点后两位以上
            ];
            for(i=0; i<regStrs.length; i++){
                var reg = new RegExp(regStrs[i][0]);
                th.value = th.value.replace(reg, regStrs[i][1]);
            }
        }

        function overFormats(th){
            var v = th.value;
            if(v === ''){
                v = '0';
            }else if(v === '0.'){
                v = '0';
            }else if(/^0+\d+\.?\d*.*$/.test(v)){
                v = v.replace(/^0+(\d+\.?\d*).*$/, '$1');
                v = inp.getRightPriceFormat(v).val;
            }else if(/^0\.\d$/.test(v)){
                 v = v + '0';
            }else if(!/^\d+\.\d{2}$/.test(v)){
                if(/^\d+\.\d{2}.+/.test(v)){
                    v = v.replace(/^(\d+\.\d{2}).*$/, '$1');
                }else if(/^\d+\.$/.test(v)){
                    v = v.substring(0, v.length-1);
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
                    v = '0';
                }
            }
            th.value = v;
        }
</script>
@stop
