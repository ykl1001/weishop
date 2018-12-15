@extends('admin._layouts.base')

@section('css')
    <style>
        fieldset {
            border: solid 1px #999;
            border-radius: 4px;
            width: 100%;
            font-size:14px;
        }

        fieldset legend {
            padding: 0 5px;
            width:auto;
            border:none;
            margin:0;
            font-size:14px;
        }

        fieldset div.actions {
            width: 96%;
            margin: 5px 10px;
        }

        fieldset div label{display:inline-block; margin-right:15px;}

        .blank15 {
            height: 15px;
            line-height: 10px;
            clear: both;
            visibility: hidden;
        }
        .actions label{margin-right:10px!important;}
        .actions span{ font-size:12px;}

        .my_fieldset{width: 100%;}
    </style>
@stop
@section('right_content')
    @yizan_begin
    <form id="yz_form" name="yz_form" class="validate-form ajax-form" method="post" action="{{ u('MsgModel/save') }}" enctype="application/x-www-form-urlencoded" target="_self" novalidate="novalidate">
        <div class="formdiv" style="width:100%;">
            <div class="f-boxr">
                <div class="blank15"></div>
                请选择消息模板名称：<select id="msgModel" name="msgModel" style="min-width:200px;width:auto" class="sle ">
                    <option value="0" selected="selected">请选择需要编辑的模板</option>
                    @foreach($list as $k =>$v)
                        <optgroup label="{{$data[$k]}}">
                            @foreach($v as $item)
                                <option @if($item['is_writable'] == 0) disabled="disabled" @endif value="{{$item['id']}}">{{$item['name']}} @if($item['is_writable'] == 0) (文件无权限) @endif</option>
                            @endforeach
                            </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="blank15"></div>
                <div id="checkList">
                    <div class="m-tab">
                    <table id="checkListTable" class="">
                        <thead>
                            <tr>
                                <td style="width: 120px;"><span>类型</span></td>
                                <td style="width: 300px;"><span>标题</span></td>
                                <td><span>说明</span></td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $k =>$v)
                            <tr class="">
                                <td colspan="3"  style="text-align:left;font-weight:bold">{{$data[$k]}}</td>
                            </tr>
                            @foreach($v as $item)

                            <tr class="">
                                <td colspan="1" rowspan="4"   style="text-align: left;padding-left: 30px;">
                                        <span style="color: #B40001">➤&nbsp;&nbsp;</span>
                                        {{$item['name']}}
                                            @if($item['is_writable'] == 0)
                                            <p class="red f10">
                                                (没有写入权限)
                                            </p>
                                            @endif
                                </td>
                                <td colspan="2" style="text-align:left;"><span style="color: #B40001;font-weight:bold">消息标题</span>：{{$item["title"]}}</td>
                            </tr>
                            <tr class="">
                                <td colspan="2" style="text-align:left;"><span style="color: #B40001;font-weight:bold">消息内容</span>：{{$item["content"]}}</td>
                            </tr>
                            <tr class="">
                                <td colspan="2" style="text-align:left;color: #9c9c9c;"><span>消息说明</span>：{{$item["tip"]}}</td>
                            </tr>
                            <tr class="">
                                <td colspan="2" style="text-align:left;color: #9c9c9c;"><span style="color: #666;font-weight:bold">是否发送短信通知</span>：
                                    @if($item["isSendMsg"]==1) <span style="color: green;font-weight:bold">是</span> @else <span style="color: red;font-weight:bold">否</span> @endif</td>
                            </tr>

                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            <div id="showmodel">

            </div>
        </div>
        <div class="tc list-btns none">
            <button type="submit" class="u-addspbtn  fl btn-green">提 交</button>
            <a type="button" class="u-addspbtn  fl btn-gray show_item" style="margin-left: 20px">返回</a>
            <div class="clearfix"></div>
        </div>
    </form>
    @yizan_end
@stop

@section('js')
    <script type="text/javascript">
        jQuery(function($){
            $(".show_item").click(function(){
                $("#showmodel").addClass("none");
                $(".list-btns").addClass("none");
                $("#checkList").removeClass("none");
                $("#msgModel").html($("#msgModel").html())
            });
            $("select[name=msgModel]").change(function() {
                var id = $(this).find("option:selected").val();
                var text = $(this).find("option:selected").text();
                if(id == 0){
                    $("#showmodel").addClass("none");
                    $(".list-btns").addClass("none");
                    $("#checkList").removeClass("none");
                }else{
                    $.zydialogs.open("<p style='margin: 30px'>正在加载“"+text+"”···<br><br><br><br></p>",{
                        width:300,
                        title:text,
                        showButton:false,
                        showClose:false,
                        showLoading:true
                    }).setLoading();
                    $("#showmodel").removeClass("none");
                    $(".list-btns").removeClass("none");
                    $("#checkList").addClass("none");

                    $.post("{{ u('MsgModel/getId') }}",{'id':id},function(res){
                        $.zydialogs.close();
                        if(res == ""){
                            $("#showmodel").addClass("none");
                            $(".list-btns").addClass("none");
                            $("#checkList").removeClass("none");
                        }else{
                            $("#showmodel").html(res);
                            $(".validate-form").removeClass("sumit-loading");
                        }
                    });
                }
            })
        });
    </script>
@stop