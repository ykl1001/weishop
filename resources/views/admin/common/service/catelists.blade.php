@extends('admin._layouts.base')
@section('css')
<style type="text/css"></style>
@stop
@section('right_content')
	@yizan_begin
		<yz:list>
			<btns>
                <linkbtn label="添加分类" url="{{ u('Service/creategoodscate',$args) }}" css="btn-gray"></linkbtn>
                <linkbtn label="删除" type="destroy" click="btnDestroy()"></linkbtn>
			</btns> 
			<table pager="no" relmodule="GoodsCate" checkbox="1">
				<columns>
					<column code="id" label="分类编号" align="center"  ></column> 
					<column code="name" label="分类名称" align="center" css="name"></column> 
					<!--column code="levelrel" label="层级视图" css="sort" align="center"></column -->
					<!-- <column code="img" label="图标">
						<img src="{{$list_item['img']}}" style="max-width:32px;"/>
					</column>  -->
					<column code="sort" label="排序" css="sort"></column> 
					<column code="status" label="状态" type="status"></column>
					<actions> 
						<action type="edit" css="blu">
							<attrs>
								<url>{{ u('Service/cateedit',['id'=>$list_item['id'],'sellerId'=>$list_item['sellerId'],'type'=>$list_item['type']]) }}</url>
							</attrs>
						</action>
						<action type="destroy" css="red" >
							<attrs>
								<url>{{ u('GoodsCate/destroy',['id'=>$list_item['id'], 'sellerId'=>$list_item['sellerId'],'type'=>$list_item['type']]) }}</url>
							</attrs>
						</action>
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
<script type="text/javascript">
	$(function(){
		$('#cate_id').prepend("<option value='0' selected>全部分类</option>");
	});

	function btnDestroy() {
        var id = new Array();
        var sellerId = "{{ $list[0]['sellerId'] ?  $list[0]['sellerId'] : 0}}";
        $("div.checker span.checked").each(function(k, v){
            id[k] = $(this).find("input[name='key']").val();
        });

        if(id.length < 1)
        {
            $.ShowAlert('请选择要删除的项');
            return false;
        }
        $.ShowConfirm('确认删除吗？', function(){
            $.post("{{ u('GoodsCate/destroy') }}", {'id':id, 'sellerId':sellerId}, function(res){
                if(res.status)
                {
                    window.location.reload();
                }
            });
        });        
    }
</script>
@stop
 