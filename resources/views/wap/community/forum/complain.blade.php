@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left back" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">举报帖子</h1>
    </header>
@stop

@section('content')
    <div class="content" id=''>
        <div class="y-yjtxt f14">
            <textarea name="content" id="content" placeholder="请输入举报理由，我们会尽快核实…"></textarea>
        </div>
        <p class="y-bgnone" id="submit"><a href="javascript:addcomplain();" class="y-paybtn f16">提交意见</a></p>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        function addcomplain() {
            var content = $("#content").val();
            var id = "{{$id}}";
            $.post("{{ u('Forum/addcomplain') }}",{'content':content,'id': id},function(res){
                if(res.code == 0) {
                    $.alert(res.msg, function(){
                        $.router.load("{!! u('Forum/detail',['id'=>$id]) !!}", true);
                    });
                }else{
                    $.alert(res.msg);
                }
            },"json");
        }
    </script>
@stop

