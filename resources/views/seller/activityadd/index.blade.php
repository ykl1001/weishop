@extends('seller._layouts.base')
@section('css')
<style type="text/css">
	.ml12{margin-left: 12px;}
	.p-acttopbtn{padding: 7px 30px;font-size: 16px;color: #666;border: solid #ccc;border-width: 1px 1px 0 1px;background-color: #ccc;}
	.g-szzllst{margin-top: -1px;position: relative;z-index: 0;}
	.bfff{background: #fff;position: relative;z-index: 1;}

	/*特价商品*/
	.w195{width: 195px;}
    .ml25{margin-left: 25px;}
    .w150{width: 150px;}
    .w80{width: 80px;}

    .mt8{margin-top: 8px;}
    .p-addbtn{padding: 5px 10px;cursor: pointer;}
    .p-sellerlist{width: 600px;margin-top: 5px;cursor: pointer;border: solid #ccc;border-width: 1px 1px 0;}
    .p-sellerlist li:hover{background-color: #eee;}
    .p-sellerlist li{border-bottom: 1px solid #ccc;margin: 0;padding: 5px 10px;}

    .p-sellerlist i.fa{float: right;font-size: 16px;}
    .p-sellerlist i.fa:hover{color: red;}

    button.zfbtn{cursor: pointer;}
</style>
@stop
@section('content')
	<div>
		<div class="m-zjgltbg">					
			<div class="p10">
				<!-- 服务表格 -->
				<div class="m-tab m-smfw-ser">
					@yizan_begin
	                    <yz:form id="yz_form" action="save">
					        <div class="pageBox page_1">
					            <div class="m-zjgltbg">
					                <div class="p10">
					                	<!-- tab选项 -->
					                    <div class="clearfix">
						                   	<a href="javascript:$.show('full');" class="p-acttopbtn full fl">满减活动</a>
						                   	<a href="javascript:$.show('special');" class="p-acttopbtn special fl">特价商品</a>
						                </div>
					                    <div class="g-szzllst pt20">
					                    	<!-- 满减活动 -->
					                    	<div id="full" style="display:none">
						                        <yz:fitem name="full[startTime]" label="开始时间" type="date"></yz:fitem>
								                <yz:fitem name="full[endTime]" label="结束时间" type="date"></yz:fitem>
								                <yz:fitem label="活动内容">
								                    <p>&nbsp;满&nbsp;&nbsp;<input type="text" name="full[fullMoney]" class="u-ipttext w195" placeholder="请输入金额" value="{{$data['full[fullMoney]']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元</p>
								                    <p class="gray ml25">此金额为商品总价，不包含其它费用</p>
								                    <p class="mt10">&nbsp;减&nbsp;&nbsp;<input type="text" name="full[cutMoney]" class="u-ipttext w195" placeholder="请输入金额" value="{{$data['full[cutMoney]']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value<0)this.value='';else this.value2=this.value;">&nbsp;元</p>
								                </yz:fitem>
								                <yz:fitem label="参与次数" name="full[joinNumber]" append="1"><p class="gray">用户每天可参与活动次数</p></yz:fitem>
							                </div>
							                <!-- 特价商品 -->
							                <div id="special" style="display:none">
							                	<yz:fitem name="special[startTime]" label="开始时间" type="date"></yz:fitem>
								                <yz:fitem name="special[endTime]" label="结束时间" type="date"></yz:fitem>
								                <yz:fitem label="活动内容">
								                    <input type="button" value="+添加商品" class="p-addbtn" id="useSellerBtn">
								                    <span class="ml5"><span class="goodsListsLength">{{count($goodsLists) > 0 ? count($goodsLists) : '0'}}</span>&nbsp;件特价商品</span>
								                    <div id="useGoodsList" @if(count($goodsLists) <= 0) style="display:none" @endif>
								                        @if(count($goodsLists) > 0)
								                            <ul class="p-sellerlist">
								                                @foreach($goodsLists as $key => $value)
								                                    <li>
								                                        商品名称：
								                                        <input type="text" value="{{$value['name']}}" class="u-ipttext addgoods w150" data-goodsId="{{$value['id']}}" disabled="true">
								                                        
								                                        原价：
								                                        <input type="text" value="{{$value['price']}}" class="u-ipttext w80 price" disabled="true">
								                                        
								                                        <span class="salePrice">
								                                        现价：
								                                        <input type="text" name="special[salePrice][]" value="{{ $data['special[sale]'] > 0 ? number_format(($data['special[sale]'] / 10) * $value['price'], 2) : '0.00'}}" class="u-ipttext w80 salePrice" readonly="true">
								                                        </span>
								                                        
								                                        <i class="fa fa-times mt8" aria-hidden="true"></i>
								                                        <input type="hidden" value="{{$value['id']}}" name="special[ids][]">
								                                    </li>
								                                @endforeach
								                            </ul>
								                        @endif
								                    </div>
								                </yz:fitem>
								                <yz:fitem label="商品折扣">
								                	<input type="text"  value="{{$data['special[sale]']}}" onkeyup="if(this.value==this.value2)return;if(this.value.search(/^\d*(?:\.\d{0,2})?$/)==-1)this.value=(this.value2)?this.value2:'';if(this.value>10 || this.value<0)this.value='';else this.value2=this.value;" class="u-ipttext sale" placeholder="请输入折扣率，例如8.8">
								                	<!-- 记录已选折扣  防止修改提交-->
								                	<input type="hidden" name="special[sale]" value="{{$data['special[sale]']}}">
													<span class="ml5">
														<span class="ml5 mr10">折</span>
														<input type="button" value="确认" class="p-addbtn" id="discountBtn">
													</span>
								                </yz:fitem>
								                <yz:fitem label="参与次数" name="special[joinNumber]" append="1"><p class="gray">单件商品可参与活动次数</p></yz:fitem>
							                </div>
							                <!-- 记录选择的活动类型 -->
							                <input type="hidden" name="checkType" id="checkType">
					                    </div>
					                </div>
					            </div>
					        </div>
					    </yz:form>
	                @yizan_end
				</div>
			</div>
		</div>
	</div> 
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		var checkType = "{{ $_GET['checkType'] or 'full' }}";

		$.show = function(id){
			$(".full,.special").removeClass('bfff');
			$("#full,#special").hide();

			$("."+id).addClass('bfff');
			$("#"+id).show();

			$("#checkType").val(id);
		}

		$.show(checkType);

		//添加指定商品时，保存现有数据
        $("input#useSellerBtn").click(function(){

            var saveData = new Object();
            var goodsIds = [];
            var salePrice = [];

            $.each($("#useGoodsList ul li"), function(k, v){
                goodsIds[k] = $(this).find('input.addgoods').attr('data-goodsId');
                salePrice[k] = $(this).find('input.salePrice').val();
            });

            saveData.form = $("#yz_form").serializeArray();
            saveData.goodsIds = goodsIds;
            saveData.salePrice = salePrice;

            $.post("{{ u('ActivityAdd/save_special_data') }}", saveData, function(res){
                window.location.href = "{{ u('ActivityAdd/addGoods') }}";
            });
        });

        //删除指定商品
        $(".p-sellerlist li i.fa").click(function(){
            var s = $(this);
            var id = s.siblings("input.addgoods").attr('data-goodsId');

            //异步请求
            $.post("{{ u('ActivityAdd/deleteGoodsIds') }}", {'id':id}, function(res){
                //动画移除
                if(res == 1){
                    s.parents("li").fadeOut(700,function(){
                        $(this).remove();
                    });
                }
                //数量减1
                $("span.goodsListsLength").text($("span.goodsListsLength").text() * 1 - 1);
                
            });

        });

        //计算折扣
        $("#discountBtn").click(function(){
        	var sale = $(".sale").val() / 10;
        	if(sale <= 0 || sale > 10)
        	{
        		alert('请填写正确的折扣参数');
        		$('span.salePrice').hide();
        		return false;
        	}
        	//记录当前折扣（防止修改后再次修改折扣比例）
        	$("input[name='special[sale]']").val($(".sale").val());

        	//显示折扣价格
        	$('div#useGoodsList ul li').each(function(k, v){
        		var price = ( parseFloat($(this).find('input.price').val()) * sale ).toFixed(2);  //现价 = 原价 * 折扣比例 * 10/100 = 原价 * 折扣比例 / 10
        		$(this).find('span.salePrice').show().find('input.salePrice').val(price); 
        	});
        });


	});
</script>
@stop