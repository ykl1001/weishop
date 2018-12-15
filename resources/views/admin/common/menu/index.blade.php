@extends('admin._layouts.base')
@section('css')

@stop
<?php
$type = [
        '1'=>'商家分类',
        '3'=>'商品',
        '4'=>'商家',
        '5'=>'URL',
        '6'=>'服务',
        '7'=>'文章',
        '8'=>'物业',
        '9'=>'签到送积分',
        '10'=>'生活缴费',
        '11'=>'平台自营',
        '12'=>'积分商城',
];
?>

@section('right_content')
    @yizan_begin
        <yz:list>
            <btns>
                <linkbtn label="添加" url="{{ $url }}" css="btn"></linkbtn>
                <linkbtn label="删除" type="destroy"></linkbtn>
            </btns>
			<table relmodule="SystemGoods" checkbox="1">
				<columns>
                    <column code="name" label="名称" width="120" iscut="1"></column>
                    <column code="menuIcon" label="图片" type="menuIcon" width="60" iscut="1">
                        <php>
                            $color = empty($list_item['color']) ? '#ccc' : $list_item['color'];
                        </php>
                        <a href="{{ $list_item['menuIcon'] }}" target="_blank" style="display:block; width:60px; height:60px;border:solid 1px {{ $color }}; background:#ccc;">
                            <img src="{{ formatImage($list_item['menuIcon'],60,60,1) }}"/>
                        </a>
                    </column>
                    <column code="sort" width="50" label="排序"></column>
                    <column code="status" width="50" label="状态"  type="status"></column>
                    <column label="城市" type="time" width="80">
                        @if(!empty($list_item['city'])) {{ $list_item['city']['name'] }} @else 全部城市 @endif
                    </column>
                    <column label="链接类型" type="time" width="80">
                        <p>{{ $type[$list_item['type']] }}</p>
                    </column>
                    <actions width="70">
                        <action type="edit" css="blu"></action>
                        <action type="destroy" css="red"></action>
                    </actions>
                </columns>
			</table>
        </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		//设置抢购价格
		$(document).on('keypress','.shopping_price',function(e){
			var key = e.which;
            if (key == 13) {
                e.preventDefault();
                var id = $(this).data('id');
                var price = $(this).val();
                $.post('{{ u("ShoppingSpree/setPrice") }}',{'id':id,'price':price},function(res){
					if(res.code == 0){
						window.location.reload();
					}else{
						$.ShowAlert(res.msg);
					}
                },"json");
            }
		});
		//通过一级分类查找二级分类
		$("#catePid").change(function(){
			var pid = $(this).val();
			$("#cateId").html("<option value>全部</option>");
			if(pid < 1){
				return false;
			}
			$.post("{{ u('Goods/selectSecond') }}",{'pid':pid,'status':1},function(res){
				if(res.length > 0){
					var html = "<option value>全部</option>";
					$.each(res, function(k,v){
						html += "<option value='"+this.id+"'>"+this.name+"</option>";
					});
					$("#cateId").html(html);
				}
			},'json');
		});
		
		$(document).on('click','.y-qghd a',function(){
			var type = $(this).data('type');
			var url = '{{ u("ShoppingSpree/setStaus") }}';
			$.post(url,{'type':type},function(res){
				if(res.code == 0){
					window.location.reload();
				}else{
					$.ShowAlert(res.msg);
				}
			},"json");
		});
	});
</script>
@stop
