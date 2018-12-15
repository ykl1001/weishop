@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Repair/detail',['id'=>$args['id'],'districtId'=>$args['districtId']])}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>
        </a>
        <h1 class="title f16">评价</h1>
        <a class="button button-link button-nav pull-right c-red" id="submit" data-transition='slide-out'>提交</a>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="content-block-title x-pjstar" style="line-height:0.8rem;">
            <span class="f14 c-black mr5">评分</span>
            <div class="y-starcont c-red">
                <div class="y-star">
                    <i class="icon iconfont vat mr10 f16">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe653;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe653;</i>
                </div>
                <div class="y-startwo" style="width: 100%">
                    <i class="icon iconfont vat mr10 f16">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe654;</i>
                    <i class="icon iconfont vat mr10 f16">&#xe654;</i>
                </div>
            </div>
        </div>
        <div class="c-bgfff p10">
            <textarea class="x-pjtxt f14 y-minh7" placeholder="您的意见很重要！来点评一下吧..." id="content"></textarea>
        </div>
    </div>
@stop

@section($js)
    <script>
        var id = "{{ Input::get('id') }}";
        var districtId = "{{ Input::get('districtId') }}";
        var star = 5;

        //评价星级选择
        $(document).on("uploadsucc",".x-pjpic form",function(){
            $("#" + this.id + "-li .delete").removeClass('none');
        });
        $(document).off("touchend",".x-pjpic");
        $(document).on("touchend",".x-pjna",function(){
            if($(this).children("i").hasClass("on")){
                $(this).children("i").removeClass("on");
            }else{
                $(this).children("i").addClass("on");
            }
        });

        // 评价
        $(document).off("touchend",".x-pjstar .y-star i, .x-pjstar .y-startwo i");
        $(document).on("touchend",".x-pjstar .y-star i, .x-pjstar .y-startwo i",function(){
            var arri = $(this).parent().children();
            var index = $(this).parent().children().index(this);
            var redstar_w = (index+1) / 5 * 100;
            star = parseInt(index)+1;
            $(".x-pjstar .y-star i").removeClass("on");
            $(".x-pjstar .y-startwo").css("width", "0");
            $(".x-pjstar .y-startwo").css("width", redstar_w + "%");
            for (i = 0; i < arri.length; i++){
                arri[i].className = i < index+1 ? "icon iconfont vat mr10 f16 on" : "icon iconfont vat mr10 f16";
            }
        });

        $(document).off("touchend","#submit");
        var is_post = 0;
        $(document).on("touchend","#submit",function(){
            if(is_post == 1){
                return false;
            }
            $("#content").blur();
            var images = new Array();
            $("input[name=images]").each(function(index,val){
                if($(this).val() != "" ){
                    images.push($(this).val());
                }
            })
            var content = $("#content").val();
            var data = {
                id: id,
                content: content,
                star: star
            };
            is_post = 1;
            $.post("{{ u('Repair/dorate') }}", data, function(res){
                if(res.code == 0) {
                    $.toast(res.msg);
                    setTimeout(function(){
                        $.href("{!! u('Repair/detail',['id'=>Input::get('id'),'districtId'=>Input::get('districtId')]) !!}");
                    },2000);
                }else if(res.code == '99996'){
                    $.href("{{ u('User/login') }}");
                }else{
                    is_post = 0;
                    $.toast(res.msg);
                }
            },"json");
            return false;
        })
    </script>
@stop