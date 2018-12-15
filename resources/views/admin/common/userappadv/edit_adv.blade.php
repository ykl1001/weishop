@extends('admin._layouts.base')

<?php
$type = [
	['key'=>'1','name'=>'商家分类'],
	['key'=>'3','name'=>'普通商品'],
    ['key'=>'4','name'=>'商家'],
    ['key'=>'5','name'=>'自定义URL'],
    ['key'=>'6','name'=>'服务商品'],
    ['key'=>'7','name'=>'文章'],
    ['key'=>'8','name'=>'签到送积分'],
    ['key'=>'9','name'=>'积分商城'],
    ['key'=>'10','name'=>'自营商城'],
    ['key'=>'11','name'=>'物业管理'],
    ['key'=>'12','name'=>'生活缴费']
];
$data['type'] = isset($data['type']) ? $data['type'] : 1;

if ((int)$data['position']['id'] > 0) {
    $sellerCateStyle = $bsAdvPositionId == $data['position']['id'] ? '' : 'display:none';
} else {
    $sellerCateStyle = $bsAdvPositionId == $positions[0]['id'] ? '' : 'display:none';
}

?>

@section('right_content')

@section('css')
    <style>
        .m-tpyllst{width: 150px;}
        .m-spboxlst2 .f-tt {
            float: left;
            display: block;
            width: 100px;
            text-align: right;
            padding: 0 5px 0 0;
        }
        .m-spboxlst2 .f-boxr {
            float: left;
            display: inline-block;
        }
        .template img{
            margin-right:10px;
            background-size: cover;
            background-position: center;
        }
        .template .templatebig{
            width:185px;
        }
        .template{
            display:none;
        }
    </style>
@stop

@yizan_begin
<yz:form id="yz_form" action="update_adv">
	<yz:fitem name="name" label="名称"></yz:fitem>
    @yizan_yield('adv_wapmodule')
        <div id="positionId-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                 广告位编号:
            </span>
            <div class="f-boxr">
                <select id="positionId" name="positionId" class="sle ">
                    <option value="3">买家APP首页广告</option>
                </select>
            </div>
        </div>
        <yz:fitem name="cityId" label="城市">
            <yz:select name="cityId" css="type" options="$citys" textfield="name" valuefield="id" selected="$data['cityId']"></yz:select>
        </yz:fitem>

        <div id="mouldId-form-item" class="u-fitem clearfix ">
                <span class="f-tt">
                     广告位模板:
                </span>
            <div class="f-boxr">
                <select id="mouldId" name="mouldId" class="sle ">
                    <option value="1" @if($data['mouldId'] == 1) selected @endif>模板1</option>
                    <option value="2" @if($data['mouldId'] == 2) selected @endif>模板2</option>
                    <option value="3" @if($data['mouldId'] == 3) selected @endif>模板3</option>
                    <option value="4" @if($data['mouldId'] == 4) selected @endif>模板4</option>
                </select>
            </div>
        </div>

        <div id="mouldId-form-item" class="u-fitem clearfix ">
            <span class="f-tt">
                 模板样式:
            </span>
            <div class="f-boxr template">
                <div class="template" id="template1" @if($data['mouldId'] == 1 || empty($data['mouldId'])) style="display: block;margin-left: 105px;" @else  style="display: none;margin-left: 105px;"  @endif >
                    <img src="@if(!empty($data['dataJson'][1]['img']) && $data['mouldId'] == 1) {{ $data['dataJson'][1]['img'] }} @else {{ asset('images/upload-bg.jpg') }} @endif" class="templatt" id="template-1-1" data-module="1" data-wei="1" data-arg="{{ $data['dataJson'][1]['arg'] }}" style="width: 20%;">
                    例:<img src="{{ asset('images/mod1.png') }}" style="width: 40%">
                </div>
                <div class="template" id="template2" @if($data['mouldId'] == 2) style="display: block;margin-left: 105px;" @else  style="display: none;margin-left: 105px;"  @endif>
                    <img src="@if(!empty($data['dataJson'][1]['img']) && $data['mouldId'] == 2) {{ $data['dataJson'][1]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-2-1" data-module="2" data-wei="1" data-arg="{{ $data['dataJson'][1]['arg'] }}" style="width: 185px; height: 150px;">
                    <img src="@if(!empty($data['dataJson'][2]['img']) && $data['mouldId'] == 2) {{ $data['dataJson'][2]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-2-2" data-module="2" data-wei="2" data-arg="{{ $data['dataJson'][2]['arg'] }}" style="width: 185px; height: 150px;">
                    例:<img src="{{ asset('images/mod2.png') }}" style="width: 40%">
                </div>
                <div class="template" id="template3" @if($data['mouldId'] == 3) style="display: block;margin-left: 105px;" @else  style="display: none;margin-left: 105px;"  @endif>
                    <img src="@if(!empty($data['dataJson'][1]['img']) && $data['mouldId'] == 3) {{ $data['dataJson'][1]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="fl templatt" id="template-3-1" data-module="3" data-wei="1" data-arg="{{ $data['dataJson'][1]['arg'] }}" style="width: 185px; height: 150px;">
                    <div class="fl templatebig" style="line-height: 10px;">
                        <img src="@if(!empty($data['dataJson'][2]['img']) && $data['mouldId'] == 3) {{ $data['dataJson'][2]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-3-2" data-module="3" data-wei="2" data-arg="{{ $data['dataJson'][1]['arg'] }}" style="width: 185px; height: 75px;">
                        <img src="@if(!empty($data['dataJson'][3]['img']) && $data['mouldId'] == 3) {{ $data['dataJson'][3]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-3-3" data-module="3" data-wei="3" data-arg="{{ $data['dataJson'][3]['arg'] }}" style="width: 185px; height: 75px;">
                    </div>
                    例:<img src="{{ asset('images/mod3.png') }}" style="width: 40%">
                </div>
                <div class="template" id="template4" @if($data['mouldId'] == 4) style="display: block;margin-left: 105px;" @else  style="display: none;margin-left: 105px;"  @endif>
                    <img src="@if(!empty($data['dataJson'][1]['img']) && $data['mouldId'] == 4) {{ $data['dataJson'][1]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-4-1" data-module="4" data-wei="1" data-arg="{{ $data['dataJson'][1]['arg'] }}" style="width: 10%">
                    <img src="@if(!empty($data['dataJson'][2]['img']) && $data['mouldId'] == 4) {{ $data['dataJson'][2]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-4-2" data-module="4" data-wei="2" data-arg="{{ $data['dataJson'][2]['arg'] }}" style="width: 10%">
                    <img src="@if(!empty($data['dataJson'][3]['img']) && $data['mouldId'] == 4) {{ $data['dataJson'][3]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-4-3" data-module="4" data-wei="3" data-arg="{{ $data['dataJson'][3]['arg'] }}" style="width: 10%">
                    <img src="@if(!empty($data['dataJson'][4]['img']) && $data['mouldId'] == 4) {{ $data['dataJson'][4]['img'] }} @else {{ asset('images/upload-bg2.jpg') }} @endif" class="templatt" id="template-4-4" data-module="4" data-wei="4" data-arg="{{ $data['dataJson'][4]['arg'] }}" style="width: 10%">
                    例:<img src="{{ asset('images/mod4.png') }}" style="width: 40%">
                </div>
            </div>
        </div>

        <div style="display: none" id="have_html">
            @if(!empty($data['dataJson']))
                @foreach($data['dataJson'] as $k=>$val)
                    <div class="que_html" que-module="{{$data['mouldId']}}" que-wei="{{$k}}">
                        <input type="hidden" name="upData[{{$k}}][img]" class="img" value="{{$val['img']}}">
                        <input type="hidden" name="upData[{{$k}}][type]" class="type" value="{{$val['type']}}">
                        <input type="hidden" name="upData[{{$k}}][arg]" class="arg" value="{{$val['arg']}}">
                    </div>
                @endforeach
            @endif
        </div>
        @yizan_stop
    	<fitem type="script">
        <script type="text/tpl" id="createForm">
            <div style="width:550px;padding:10px;" class="m-spboxlst2 ">
                <yz:fitem name="image" label="图片" type="image"></yz:fitem>
                <yz:fitem name="type" label="广告链接类型">
                    <yz:select name="type" css="type" options="$type" textfield="name" valuefield="key" selected="1"></yz:select>
                </yz:fitem>
                <yz:fitem pstyle="display:none;"  name="sellerCate" label="选择商家分类">
                    <select id="sellerCate" name="sellerCate" style="min-width:200px;width:auto" class="sle ">
                        @foreach($sellerCate as $item)
                            <option value="{{$item['id']}}" @if($data['arg'] == $item['id'])selected="selected"@endif>{{$item['name']}}</option>
                            @if($item['childs'])
                                @foreach($item['childs'] as $child)
                                    <option value="{{$child['id']}}" @if($data['arg'] == $child['id'])selected="selected"@endif style="margin-left: 10px;">----{{$child['name']}}</option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    <span class="ts ts1"></span>
                </yz:fitem>
                <yz:fitem pstyle="display:none;"  name="sellerGoods" label="商品编号参数">
                    <input type="text" name="sellerGoods" id="sellerGoods" class="u-ipttext" value="">
                    <span class="ts ts1">请到商家页面查看商品编号后填写</span>
                </yz:fitem>
                <yz:fitem pstyle="display:none;"  name="serviceGoods" label="服务编号参数">
                    <input type="text" name="serviceGoods" id="serviceGoods" class="u-ipttext" value="">
                    <span class="ts ts1">请到商家页面查看服务编号后填写</span>
                </yz:fitem>
                <yz:fitem  pstyle="display:none;" name="systemSellers" label="商家编号参数">
                    <input type="text" name="systemSellers" id="systemSellers" class="u-ipttext" value="">
                    <span class="ts ts1">请到商家页面查看商家编号后填写</span>
                </yz:fitem>
                <yz:fitem pstyle="display:none;"  name="article" label="选择文章">
                    <select id="article" name="article" style="min-width:200px;width:auto" class="sle ">
                        <option value="0" >请选择</option>
                        @foreach($article as $item)
                            <option value="{{$item['id']}}" @if($data['arg'] == $item['id'])selected="selected"@endif>{{$item['title']}}</option>
                        @endforeach
                    </select>
                    <span class="ts ts1"></span>
                </yz:fitem>
                <yz:fitem  pstyle="display:none;" name="url" label="输入路径"></yz:fitem>
                <yz:fitem name="arg" type="hidden" val=""></yz:fitem>
            </div>
        </script>
    	<script type="text/javascript">
            $.updateImg = function(othis,module,wei,arg){
                var dialog = $.zydialogs.open($("#createForm").html(), {
                    boxid:'SET_GROUP_WEEBOX',
                    width:300,
                    title:'添加内容',
                    showClose:true,
                    showButton:true,
                    showOk:true,
                    showCancel:true,
                    okBtnName: '确定',
                    cancelBtnName: '取消',
                    contentType:'content',
                    onOk: function(){
                        if(arg != "" && arg != undefined && arg != null){
                            $("input[name='arg']").val(arg);
                        }

                        var img = $("#image").val();
                        if(img == ""){
                            $.ShowAlert("请上传图片!");
                            return false;
                        }
                        var type = $("select[name=type]").val();
                        if(type == ""){
                            $.ShowAlert("请选择广告链接类型!");
                            return false;
                        }
                        arg = $("#arg").val();
                        if(type == 1 || type == 3 || type == 4 || type == 5 || type == 6 || type == 7){
                            if(arg == ""){
                                $.ShowAlert("请填写或者选择第三栏!");
                                return false;
                            }
                        }

                        var html = '<div class="que_html" que-module="'+module+'" que-wei="'+wei+'">';
                                html += '<input type="hidden" name="upData['+wei+'][img]" class="img" value='+img+'>';
                                html += '<input type="hidden" name="upData['+wei+'][type]" class="type" value='+type+'>';
                                html += '<input type="hidden" name="upData['+wei+'][arg]" class="arg" value='+arg+'>';
                            html += '</div>';

                        $("#have_html div").each(function(){
                            if($(this).attr("que-wei") == wei){
                                $(this).remove();
                            }
                        })

                        $("#have_html").append(html);
                        othis.attr('src',img);
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    },
                    onCancel:function(){
                        $.zydialogs.close("SET_GROUP_WEEBOX");
                    }
                });

                var have_html = $.trim($("#have_html").html());
                var no_url = 0;
                var types = 1;
                if(have_html == "" || have_html == undefined || have_html == null){
                    $("input[name='arg']").val("");
                    types = $("select[name='type']").val();
                }else{
                    $(".que_html").each(function(){
                        if($(this).attr('que-module') == module && $(this).attr('que-wei') == wei){
                            $("#image").val($(this).find('.img').val());
                            $("#img-preview-image").attr('href',$(this).find('.img').val());
                            $("#img-preview-image").find("img").attr('src',$(this).find('.img').val()).show();

                            $("select[name='type']").val($(this).find('.type').val());
                            var type = $(this).find('.type').val();
                            if(type == 1){
                                $("select[name='sellerCate']").val($(this).find('.arg').val());
                            }else if(type == 7){
                                $("select[name='article']").val($(this).find('.arg').val());
                            }else if(type == 3){
                                $('#sellerGoods').val($(this).find('.arg').val());
                            }else if(type == 4){
                                $('#systemSellers').val($(this).find('.arg').val());
                            }else if(type == 5){
                                $('#url').val($(this).find('.arg').val());
                            }else if(type == 6){
                                $('#serviceGoods').val($(this).find('.arg').val());
                            }

                            $("input[name='arg']").val();

                        }
                    })
                    no_url = 1;
                    types = $("select[name='type']").val();
                }

                if(types == 1){
                    $('#sellerCate-form-item').show();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                }else if(types == 7){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').show();
                    $('#url-form-item').hide();
                    if(no_url == 0){
                        window.open("{!! u('Service/index') !!}");
                    }
                }else if(types == 3){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').show();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    if(no_url == 0){
                        window.open("{!! u('Service/index') !!}");
                    }
                }else if(types == 4){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').show();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    if(no_url == 0){
                        window.open("{!! u('Service/index') !!}");
                    }
                }else if(types == 5){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').show();
                }else if(types == 6){
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').show();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                    if(no_url == 0){
                        window.open("{!! u('Service/index') !!}");
                    }
                }else {
                    $('#sellerCate-form-item').hide();
                    $('#sellerGoods-form-item').hide();
                    $('#serviceGoods-form-item').hide();
                    $('#systemSellers-form-item').hide();
                    $('#article-form-item').hide();
                    $('#url-form-item').hide();
                }
            }

    		jQuery(function($){
                $(".templatt").click(function(){
                    var othis = $(this);
                    var data_module = $(this).attr('data-module');
                    var data_wei = $(this).attr('data-wei');
                    var data_arg = $(this).attr('data-arg');
                    $.updateImg(othis,data_module,data_wei,data_arg);
                })

                $("select[name=mouldId]").change(function(){
                    var have_html = $.trim($("#have_html").html());
                    var val = $(this).val();
                    if(have_html == "" || have_html == undefined || have_html == null){
                        for(var i=1;i<=4;i++){
                            $("#template"+i).hide();
                        }
                        $("#template"+val).show();
                    }else{
                        var bg1 = "{{ asset('images/upload-bg.jpg') }}";
                        var bg2 = "{{ asset('images/upload-bg2.jpg') }}";
                        $.ShowConfirm('您选择其他模板，刚才选择的将会作废！', function(){
                            var module = 0;
                            var wei = 0;
                            $(".que_html").each(function(i){
                                module = $(this).attr('que-module');
                                wei = $(this).attr('que-wei');
                                if(module == 3 && wei == 1){
                                    $("#template-"+module+"-"+wei).attr('src',bg2);
                                }else{
                                    $("#template-"+module+"-"+wei).attr('src',bg1);
                                }
                            });
                            $("#have_html").html('');
                            for(var i=1;i<=4;i++){
                                $("#template"+i).hide();
                            }
                            $("#template"+val).show();
                            $.zydialogs.close("CONFIRM_WEEBOX");
                        });
                    }
                });

                $("select[name=positionId]").change(function() {
                    var bsAdvPositionId = "{{ $bsAdvPositionId }}";
                    var val = $(this).val();
                    if(val == bsAdvPositionId){
                        $("#sellerCateId-form-item").css("display", "block");
                    }else{
                        $("#sellerCateId-form-item").css("display", "none");
                    }
                })

                $(document).on('change','select[name=type]',function(){
                    $("input[name='arg']").val("");
                    var type = $("select[name='type'] option:selected").val();
                    if(type == 1){
                        $('#sellerCate-form-item').show();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').hide();
                        $('#url-form-item').hide();
                    }else if(type == 7){
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').show();
                        $('#url-form-item').hide();
                    }else if(type == 3){
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').show();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').hide();
                        $('#url-form-item').hide();
                        window.open("{!! u('Service/index') !!}");
                    }else if(type == 4){
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').show();
                        $('#article-form-item').hide();
                        $('#url-form-item').hide();
                        window.open("{!! u('Service/index') !!}");
                    }else if(type == 5){
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').hide();
                        $('#url-form-item').show();
                    }else if(type == 6){
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').show();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').hide();
                        $('#url-form-item').hide();
                        window.open("{!! u('Service/index') !!}");
                    }else {
                        $('#sellerCate-form-item').hide();
                        $('#sellerGoods-form-item').hide();
                        $('#serviceGoods-form-item').hide();
                        $('#systemSellers-form-item').hide();
                        $('#article-form-item').hide();
                        $('#url-form-item').hide();
                    }

                    if(type != 5){
                        $("#url").val("");
                    }
                    
                })

//                $("select[name=type]").trigger('change');
//                $("#sellerGoods option[value='-1']").attr("disabled","disabled");
    		});
    	</script>
    	</fitem>

    	<script type="text/javascript">
    		jQuery(function($){
                $("input[name='arg']").val("{{$data['arg']}}");
                $("#url").val("{{$data['arg']}}");
                $("#sellerGoods").val("{{$data['arg']}}");
                $("#serviceGoods").val("{{$data['arg']}}");
                $("#systemSellers").val("{{$data['arg']}}");
                $("#article").val("{{$data['arg']}}");

                $(document).on('change','#sellerCate',function(){
        			$("input[name='arg']").val($("select[name='sellerCate'] option:selected").val());
        		});
                $(document).on('blur','#sellerGoods',function(){
                    $("input[name='arg']").val($("input[name='sellerGoods']").val());
                });
                $(document).on('blur','#serviceGoods',function(){
                    $("input[name='arg']").val($("input[name='serviceGoods']").val());
                });
                $(document).on('blur','#systemSellers',function(){
                    $("input[name='arg']").val($("input[name='systemSellers']").val());
                });
                $(document).on('change','#article',function(){
                    $("input[name='arg']").val($("select[name='article'] option:selected").val());
                });
                $(document).on('keyup','#url',function(){
        			$("input[name='arg']").val($("input[name='url']").val());
        		});
            });
    	</script>
	<yz:fitem name="sort" label="排序"></yz:fitem>
	<yz:fitem name="status" label="状态">
		<yz:radio name="status" options="0,1" texts="禁用,启用" checked="$data['status']"></yz:radio>
	</yz:fitem>
</yz:form>
@yizan_end
@stop

