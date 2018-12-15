@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/img3.png') }}">
@stop
@section('right_content')
    <div class="main">
        <div class="x-shouc x-bor">
        </div>
        <div class="showbnt">
            <p class="mt20 tc">
                <span class="btn btn2">开始安装</span>
            </p>
        </div>
    </div>
@stop
@section('js')
    <script>
        jQuery(function($){
            var wait = 30;
            $(".btn2").text('开始安装');
            $.runsql = function(){
                $.post("{{u('Index/insert')}}",{status:"true"}, function(result){
                    if(result.status){
                        html = ' <p class="mt20 tc">\
                        <span class="btn btn2">正在处理...</span>\
                        </p>';
                        $(".showbnt").html('').html(html);
                        $(".x-shouc").append("<p>"+result.msg+"</p>");
                        if(result.data.status){
                            $(".x-shouc").append("<p>系统正在处理请稍候...</p>");
                            setTimeout(func,1000);
                            $.ajax({
                                type:'POST',
                                url:'{{u('Index/runsql')}}',
                                dataType:'json',
                                data:{index:0},
                                timeout:10*60*1000,
                                success:function(res){
                                    if(res.status){
                                        var data = res.msg.split(',');
                                        $.each(data,function(i,v){
                                            $(".x-shouc").append("<p>"+v+"</p>");
                                        });
                                    }else{
                                        $(".x-shouc").append("<p>"+res+"</p>");
                                    }
                                    $.post("{{u('Index/runsql')}}",{index:"1"}, function(v){
                                        $(".x-shouc").append("<p>正在检查数据库...</p>");
                                        if(v.status){
                                            wait = 0;
                                            $(".x-shouc").append("<p>"+v.msg+"</p>");
                                            var html ='<p class="tc mt20 mb0">\
                                    <a href="{{ u('Index/successOk') }}" class="btn nextbnt">完成安装</a>\
                                    </p>';
                                            $(".showbnt").html('').html(html);
                                        }else{
                                            $(".x-shouc").append("<p>"+v.msg+"</p>");
                                        }

                                    },'json');
                                },
                                error:function(res){
                                    wait = 0;
                                    $(".x-shouc").append("<p>"+res.responseText+"</p>");
                                }
                            });
                        }
                    } else {
                        $(".x-shouc").append("<p>"+result.msg+"</p>");
                        html = '<p class="tc mt20 mb0">\
                            <a href="{{ u('Index/database') }}" class="btn nextbnt">上一步</a>\
                        </p>';
                        $(".showbnt").html('').html(html);
                    }
                },'json');
            }
            var func = function(){
                wait--;
                if(wait == 30){
                    $(".x-shouc").append("<p>注:安装时间过长,不要刷新页面!</p>");
                    setTimeout(func,1000);
                }
                if(wait == 15){
                    $(".x-shouc").append("<p>请耐心等待...</p>");
                    setTimeout(func,1000);
                }
                if(wait == 5){
                    $(".x-shouc").append("<p>程序正在执行</p>");
                    setTimeout(func,1000);
                }
                if(wait == 0){
                    $(".x-shouc").append("<p>正在创建数据表...</p>");
                }else{
                    setTimeout(func,1000);
                }
            }

            $.runsql();
        });
    </script>
@stop