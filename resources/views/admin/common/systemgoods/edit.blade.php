@extends('admin._layouts.base')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.tagsinput.css') }}">
    <style type="text/css">
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        .m-spboxlst li{margin-bottom: 0px;}
        #tags_goods-form-item .f-boxr {width:550px;}
        #cateSave{display: none;}
        .page_2,.page_3{display: none;}
        .m-spboxlst li{margin-bottom: 0px;}
        #tags_goods-form-item .f-boxr {width:550px;}
        .f-boxr .btn{background: #efefef; border-color: #dfdfdf; width: 80px; color: #555;}
        .x-gebox{border: 1px solid #ddd; padding: 5px 20px;}
        .x-gebox .u-ipttext{width: 100px; margin-right: 10px;}
        .closege{width: 20px; height: 20px; background: url("{{ asset('wap/community/client/images/ico/close.png') }}"); background-size: 100% 100%; display: inline-block; cursor: pointer; vertical-align: middle; margin-top: -2px;}
        .add_norms{width: 100px !important;}
    </style>
@stop
@section('right_content')
    @yizan_begin
    <yz:form id="yz_form" action="save">
        <yz:fitem name="name" label="商品名称" required="true"></yz:fitem>
        <yz:fitem label="商品标签">
            <yz:select name="systemTagListPid" options="$systemTagListPid" textfield="name" valuefield="id" selected="$data['systemTagListPid']"></yz:select>
            <yz:select name="systemTagListId" options="$systemTagListId" textfield="name" valuefield="id" selected="$data['systemTagListId']" css="@if(count($systemTagListId) == 1) none @endif"></yz:select>
        </yz:fitem>
        <yz:fitem name="price" label="价格"  required="true"></yz:fitem>
        <yz:fitem name="stock" label="库存" val="0"></yz:fitem>
        <!--yz:fitem name="totalStock" attr="readonly" label="总库存" val="0"></yz:fitem-->
        <div id="norms-form-item" class="u-fitem clearfix">
            @include('_layouts.stock')
        </div>
        <div id="norms-form-item" class="u-fitem clearfix x-addge">
            <span class="f-tt">&nbsp;</span>
            <div class="f-boxr norms_panel">
                @foreach($data['norms'] as $item)
                    <div class="x-gebox">
                        <input type="hidden" name="norms[ids][]" value="{{$item['id']}}" >
                        型号：<input type="text" name="norms[name][]" value="{{$item['name']}}" class="u-ipttext" />
                        价格：<input type="text" name="norms[price][]" value="{{$item['price']}}" class="u-ipttext" />
                        库存：<input type="text" name="norms[stock][]" value="{{$item['stock']}}" class="u-ipttext" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
                        <i class="closege"></i>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="-form-item" class="u-fitem clearfix ">
            <yz:fitem label="商品图片">
                <yz:imageList name="images." images="$data['images']" required="true" tip = "推荐图片大小380*380px"></yz:imageList>
                <attrs>
                    <btip><![CDATA[380*380]]></btip>
                </attrs>
            </yz:fitem>
            <yz:fitem name="brief" label="商品描述"  required="true">
                <yz:Editor name="brief" value="{{ $data['brief'] }}" required="true"></yz:Editor>
            </yz:fitem>

            </yz:fitem>
            <yz:fitem label="商品状态">
                <php> $status = (int)$data['status'] </php>
                <yz:radio name="status" options="0,1" texts="禁用,启用" checked="$status"></yz:radio>
            </yz:fitem>
            <yz:fitem name="sort" label="排序"></yz:fitem>
        </div>
    </yz:form>
    @yizan_end
@stop
@section('js')
    @include('seller._layouts.alert')
    <script src="{{ asset('js/jquery.tagsinput.min.js') }}"></script>
    <script type="text/tpl" id="normsrow">
	<div class="x-gebox" style="margin-top:3px;">
		型号：<input type="text" name="norms[name][]" class="u-ipttext" />
		价格：<input type="text" name="norms[price][]" class="u-ipttext" />
		库存：<input type="text" name="norms[stock][]" class="u-ipttext" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
		<i class="closege"></i>
    </div>
</script>
    <script type="text/javascript">
        $(".add_norms").click(function(){
            $(".norms_panel").append($("#normsrow").html());
            if($(".x-gebox").length > 0){
                $(".norms_panel").parent().show();
            }
        });
        $(document).on('click','.closege',function(){
            $(this).parent().remove();
            if($(".x-gebox").length <= 0){
                $(".norms_panel").parent().hide();
            }
        });
        $(function(){
            $("input[name='stock']").attr("maxlength","11").attr("onkeyup", "this.value=this.value.replace(/\\D/g,'')").attr("onafterpaste", "this.value=this.value.replace(/\\D/g,'')");

            //标签
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
                            $.ShowAlert("当前分类暂无二级分类，请重新选择！");
                        }

                    });
                }
            });
        })
    </script>
@stop