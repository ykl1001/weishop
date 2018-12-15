@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search>
            <row>
                <btn type="click" css="btn-green one_channel_show" click="$.oneChannel()" label="一键导入"></btn>
            </row>
            <row>
                <item name="name" label="商品名称"></item>
                <btn type="search" css="btn-gray"></btn>
            </row>
        </search>
        <table css="goodstable" relmodule="" checkbox="1">
            <columns>
                <column label="图片"  width="80">
                    <a href="{{ $list_item['image'] }}" target="_blank" class="goodstable_img fl">
                        <img src="{{formatImage($list_item['image'],80,80)}}" alt="">
                    </a>
                </column>
                <column label="商品名称" align="center" width="200">
                    <div class="goods_name">{{ $list_item['name'] }}</div>
                </column>
                <column label="商品标签" align="center" width="120">
                    <p class="pl5">{{$list_item['systemTagListPid']['name'] or '无'}}|{{$list_item['systemTagListId']['name'] or '无'}}</p>
                </column>
                <column label="价格" width="50">
                    ￥{{ $list_item['price'] }}
                </column>
                <column code="status" label="状态" type="status" width="50">
                    @if($list_item['status']  == 1)
                        可选
                    @else
                        不可选
                    @endif
                </column>
                <actions width="100">
                    @if($list_item['status']  == 1)
                        <action css="blu" url="javascript:$.urls({{ $list_item['id'] }});" label="选择商品"></action>
                    @else
                        不可选
                    @endif
                </actions>
            </columns>
        </table>
        <div>
            <input type="hidden" name="systemTagListPid" value="{{$args['systemTagListPid']}}">
            <input type="hidden" name="systemTagListId" value="{{$args['systemTagListId']}}">
            <input type="hidden" name="sellerId" value="{{$args['sellerId']}}">
        </div>
    </yz:list>
    @yizan_end
@stop

@section('js')
    <script type="text/tpl" id="oneChannel">
        <div id="-form-item" class="u-fitem " style="padding:10px;">
            <div class="f-boxr show_item_cate">
                商品分类： <select name="cateId" class="sle" id="cateId" style="width:180px">
                    <option value="-1">请选择</option>
                    @foreach($cate as $val)
                        <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                    @endforeach
        </select>
    </div>
    <div class="f-boxr show_item_cate_msg none">

    </div>
</div>
</script>
    <script type="text/javascript">
        $.urls = function(id){
            window.location.href = '{{u("Service/systemgoodsedit")}}?id='+id+'&sellerId={{$args['sellerId']}}';
        }
       $(".goodstable thead input[type=checkbox]").click(function(){
                if($(this).attr('checked') === "checked"){
                    $(".one_channel_show").attr('onclick',"$.plChannel()");
                    $(".one_channel_show").text('批量导入');
                }else{
                    $(".one_channel_show").attr('onclick',"$.oneChannel()");
                    $(".one_channel_show").text('一键导入');
                }
           }
       );
       $(".goodstable tbody input[type=checkbox]").click(function(){

           if($(this).attr('checked') === "checked"){
               $(".one_channel_show").attr('onclick',"$.plChannel()");
               $(".one_channel_show").text('批量导入');
           }
           else{
               var bln = 0;
               $(".goodstable tbody input[type=checkbox]").each(function(){
                   if($(this).attr('checked') === "checked"){
                       bln += 1;
                   }
               });
                if(bln > 0){
                   $(".one_channel_show").attr('onclick',"$.plChannel()");
                   $(".one_channel_show").text('批量导入');
                }else{
                   $(".one_channel_show").attr('onclick',"$.oneChannel()");
                   $(".one_channel_show").text('一键导入');
                }
           }
       });
       $.plChannel = function(){
           var dialog = $.zydialogs.open($("#oneChannel").html(), {
               boxid:'SET_GROUP_WEEBOX',
               width:300,
               title:'请选择商品分类',
               showClose:true,
               showButton:true,
               showOk:true,
               showCancel:true,
               okBtnName: '确定批量导入',
               cancelBtnName: '取消',
               contentType:'content',
               onOk: function(){
                   var obj = {}
                   obj.systemTagListPid = $("input[name=systemTagListPid]").val();
                   obj.systemTagListId = $("input[name=systemTagListId]").val();
                   obj.cateId = $("select[name=cateId] option:selected").val();
                   obj.ids = [];
                    $(".goodstable tbody input[type=checkbox]").each(function(){
                        if($(this).attr('checked') === "checked"){
                            obj.ids.push($(this).val());
                        }
                    });
                   dialog.setLoading();
                   $.post('{{u("Service/oneChannelCk")}}',obj,function(res){
                       if(res.code == 0){
                           var msg = "共“"+res.data.count+"”条数据,正在执行请稍候...";
                           if(res.data.count >= 100){
                               var msg = "当前数据量较大,请耐心等待,请勿刷新页面...!";
                           }
                           $(".zydialog_title").html("请稍候...");
                           $(".show_item_cate_msg").html(msg).removeClass("none");
                           $(".show_item_cate").addClass("none");
                           obj.sellerId = $("input[name=sellerId]").val();
                           $.post('{{u("Service/oneChannel")}}',obj,function(res){
                               if(res.code == 0){
                                   $.ShowAlert("导入成功");
                                   $.zydialogs.close("SET_GROUP_WEEBOX");
                                   window.location.href = "{{u('Service/index')}}";

                               }else{
                                   $(".zydialog_title").html("请选择商品分类");
                                   $(".show_item_cate_msg").html("").addClass("none");
                                   $(".show_item_cate").removeClass("none");
                                   $.ShowAlert(res.msg);
                                   dialog.setLoading(false);
                               }
                           },'json');
                       }else{
                           $.ShowAlert(res.msg);
                           dialog.setLoading(false);
                       }
                   },'json');
               },
               onCancel:function(){
                   $.zydialogs.close("SET_GROUP_WEEBOX");
               }
           });
       }
        $.oneChannel = function(){
            var dialog = $.zydialogs.open($("#oneChannel").html(), {
                boxid:'SET_GROUP_WEEBOX',
                width:300,
                title:'请选择商品分类',
                showClose:true,
                showButton:true,
                showOk:true,
                showCancel:true,
                okBtnName: '确定一键导入',
                cancelBtnName: '取消',
                contentType:'content',
                onOk: function(){
                    var obj = {}
                    obj.systemTagListPid = $("input[name=systemTagListPid]").val();
                    obj.systemTagListId = $("input[name=systemTagListId]").val();
                    obj.cateId = $("select[name=cateId] option:selected").val();
                    dialog.setLoading();
                    $.post('{{u("Service/oneChannelCk")}}',obj,function(res){
                        if(res.code == 0){
                            var msg = "共“"+res.data.count+"”条数据,正在执行请稍候...";
                            if(res.data.count >= 100){
                                var msg = "当前数据量较大,请耐心等待,请勿刷新页面...!";
                            }
                            $(".zydialog_title").html("请稍候...");
                            $(".show_item_cate_msg").html(msg).removeClass("none");
                            $(".show_item_cate").addClass("none");
                            obj.sellerId = $("input[name=sellerId]").val();
                            $.post('{{u("Service/oneChannel")}}',obj,function(res){
                                if(res.code == 0){
                                    $.ShowAlert("导入成功");
                                    $.zydialogs.close("SET_GROUP_WEEBOX");
                                    window.location.href = "{{u('Service/index')}}";

                                }else{
                                    $(".zydialog_title").html("请选择商品分类");
                                    $(".show_item_cate_msg").html("").addClass("none");
                                    $(".show_item_cate").removeClass("none");
                                    $.ShowAlert(res.msg);
                                    dialog.setLoading(false);
                                }
                            },'json');
                        }else{
                            $.ShowAlert(res.msg);
                            dialog.setLoading(false);
                        }
                    },'json');
                },
                onCancel:function(){
                    $.zydialogs.close("SET_GROUP_WEEBOX");
                }
            });
        }
    </script>
@stop