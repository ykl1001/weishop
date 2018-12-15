@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <btns>
            <linkbtn label="添加分类" url="{{ u('OneselfTagServicesList/create') }}" css="btn-gray"></linkbtn>
        </btns>
        <table pager="no" css="goodstable" relmodule="GoodsSeller">
            <columns>
                <column code="id" label="分类编号" align="center"  ></column>
                <column code="name" label="分类名称" align="center"></column>
                <column code="isWapStatus" label="推荐到首页">
				  <!-- @if( $list_item['isWapStatus'] == 0 ) -->
                    <i class="fa fa-arrow-down _red" id="isWapStatus" data-isWapStatus= "{{$list_item['isWapStatus']}}" data-id="{{$list_item['id']}}" title="不推荐"></i>
                    <!-- @else if( $list_item['isWapStatus'] == 1 ) -->
                    <i class="fa fa-arrow-up _green" id="isWapStatus" data-isWapStatus= "{{$list_item['isWapStatus']}}"  data-id="{{$list_item['id']}}" title="推荐"></i>
                    <!-- @endif -->
				</column>
                <column code="sort" label="排序" css="sort"></column>
                <column code="status" label="状态" type="status"></column>
                <actions>
                    <action type="edit" css="blu"></action>
                    <!-- @if( !in_array($list_item['id'],$pids[0]) ) -->
                    <action type="destroy" css="red"></action>
                    <!-- @else -->
                    <action type="destroy" click="javascript:;" style="color:#ccc;cursor:default"></action>
                    <!-- @endif -->
                </actions>
            </columns>
        </table>
    </yz:list>
    @yizan_end
@stop
@section('js')
<script type="text/javascript">
	$(function(){
		
		$('#isWapStatus').click(function(){
			var $this = $(this);
			var id = $(this).attr("data-id");
			var isWapStatus = $(this).attr("data-isWapStatus");
			if(isWapStatus == 1){
				isWapStatus = 0;
				oldcss = "fa-arrow-up";
                newcss = "fa-arrow-down";
                title = "不推荐";
			}else{
				isWapStatus = 1;
				oldcss = "fa-arrow-down";
				newcss = "fa-arrow-up";
                title = "推荐";
			}
			$.post("{{ u('OneselfTagServicesList/isWapStatus') }}", {id:id,isWapStatus:isWapStatus}, function(result){
				if(result.status){
					$this.removeClass(oldcss);
					$this.addClass(newcss);
                    $this.attr("data-isWapStatus",isWapStatus);
                    $this.attr("title",title);
				}			
			});
			
		});
		
	});
</script>
@stop