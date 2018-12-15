@extends('admin._layouts.base')

@section('css')
<style type="text/css">
</style>
@stop
<?php

$type = [
        '1'=>'满减',
        '2'=>'满免',
        '3'=>'免运费',
        '4'=>'折扣商品',
];

?>

@section('right_content')
	@yizan_begin
		<yz:list>
			{{--<btns>--}}
				{{--<linkbtn label="添加专题" type="add"></linkbtn>--}}
			{{--</btns>--}}
			<table>
				<columns>
					<column label="编号" align="center" code="id" width="180"></column>
                    <column label="专题类型" align="center"  width="180">{{ $type[$list_item['type']] }}</column>
                    <column label="专题名称" align="center" code="name" width="180">{{  $list_item['name'] }}</column>
                    <column label="专题链接" align="center" code="url"  width="250">
                        <p id="fe_text{{$list_item['id']}}">
                            <php>
                                $url = str_replace('http:','',u('wap#Index/special',['id'=>$list_item['id']]));
                                $url = str_replace('https:','',$url);
                                echo $url;
                            </php>
                        </p>
                        <a class=" blu" id="copy_{{$list_item['id']}}"  data-clipboard-target="fe_text{{$list_item['id']}}"  target="_self" >复制链接</a>
                    </column>
                    <column code="status" label="状态" type="status"  width="30"></column>

                    {{--<column label="创建时间" align="center" code="createTime" type="time" width="180"></column>--}}

					<actions width="50" align="left">
					<action label="编辑" css="blu" type="edit"></action>&nbsp;&nbsp;
					</actions>
				</columns>
			</table>
		</yz:list>
	@yizan_end
@stop

@section('js')
    <script src="{{ asset('static/zeroclipboard/ZeroClipboard.js') }}?{{ TPL_VERSION }}"></script>
    <script type="text/javascript">
        $('.m-fyct ').css('display','none');
        $(document).ready(function() {
            ZeroClipboard.setDefaults({
                moviePath: "{{asset('static/zeroclipboard/ZeroClipboard.swf')}}"
            });

            @for($i=1;$i<=4;$i++)
                var clip = new ZeroClipboard($("#copy_{{$i}}"));
            @endfor

            clip.on("load", function(client) {
                client.on("complete", function(client, args) {
                    $.ShowAlert("已复制");
                });
            });
        });

</script>


@stop

