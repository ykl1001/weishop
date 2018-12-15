@extends('staff.default._layouts.base')
@section('title'){{$title}}@stop
@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="#" onclick="JumpURL('{{u('Order/deliver',$args)}}','#order_deliver_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">{{$title}}</h1>
    </header>
    <div class="bar bar-header-secondary heightauto y-backgroundnone">
        <div class="searchbar bg_fff y-searchbar">
            <div class="search-input">
                <i class="icon iconfont">&#xe674;</i>
                <input type="search" class="y-wlgs" placeholder='输入物流公司查询'/>
            </div>
        </div>
    </div>
@stop
@section('content')
    <div class="content-block-title m10 f_999 f12">公司列表</div>
    <div class="list-block media-list">
        <ul class="y-wlgs-list">
            @foreach($couriercompany as $k=>$v)
                <li>
                    <a href="#" onclick="$.clickjsjump('{{$k}}')" class="item-link item-content pr10">
                        <div class="item-inner pr10">
                            <div class="item-title f_5e f13 name">{{$k}}</div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        $("#{{$id_action.$ajaxurl_page}} #chosedate-input").calendar({
            onClose:function(){
                $("#{{$id_action.$ajaxurl_page}} .picker-calendar").remove();
            }
        });

        $.clickjsjump = function(keycode){
            JumpURL('{!! u('Order/deliver',['id' => $args['id'],'number'=>$args['number']]) !!}&keycode='+keycode,'#order_deliver_view',2)
        }

        $(function(){
            //搜索
            $(".y-wlgs").on('input paste', function() {
                var keywords = $.trim($(this).val());
                if(keywords == ''){
                    $("ul.y-wlgs-list li").removeClass("none");
                    return false;
                }
                $("ul.y-wlgs-list li").removeClass("none");
                $("ul.y-wlgs-list li").each(function(k, v){
                    var wlgsName = $(this).find('div.name').text();
                    if( wlgsName.indexOf(keywords) == -1)
                    {
                        $(this).addClass("none");
                    }
                })
            });
        })
    </script>
@stop