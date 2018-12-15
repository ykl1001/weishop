@extends('staff.default._layouts.base')

@section('title'){{$title}}@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left "href="#" onclick="JumpURL('{{u('Seller/checkLocation',['modelId'=>$args['modelId']])}}','#seller_checkLocation_view',2)" data-transition='slide-out'>
            <span class="icon iconfont">&#xe64c;</span>
        </a>
        <h1 class="title">城市选择</h1>
        <a class="button button-link button-nav pull-right f14" href="#" id="regionId" data-transition='slide-out'>
            完成
        </a>
    </header>
    <div class="bar bar-header-secondary heightauto y-backgroundnone">
        <div class="searchbar bg_fff y-searchbar">
            <div class="search-input">
                <i class="icon iconfont">&#xe674;</i>
                <input type="search" id='search' placeholder='输入城市名查询'/>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('contentcss')infinite-scroll infinite-scroll-bottom @stop
@section('distance')data-distance="20" @stop

@section('show_nav') @stop

@section('content')
    <div class="content" id=''>
        <!-- <div class="p10 f14 f_333 bg_fff mt10 y-dqdwcs">当前定位城市：福州</div> -->
        <div class="list-block media-list nobor x-hislst mt0">
            <ul class="y-backgroundnone city_page_2">
            @foreach($lists as $key => $value)
                <div class="content-block-title mt5 mb5 a_z" id="{{$key}}">{{$key}}</div>
                @foreach($value as $k => $v)
                <li>
                    <i class="icon iconfont f_red f20 y-citychoice y-selectedico @if($v['selected']) active @endif" data-id="{{$v['id']}}">&#xe638;</i>
                    <a href="#" class="item-content c-black pr20 pl40 active">
                        <div class="item-inner pr0">
                            <div class="item-title-row">
                                <div class="item-title y-citychoice">{{$v['name']}}</div>
                            </div>
                        </div>
                    </a>
                </li>
                @endforeach
            @endforeach
            </ul>
        </div>
    </div>
@stop

@section('footer')
<!-- <div class="y-zmlist f12">
    <ul>
        <li><a href="" external>#</a></li>
        <li><a href="#A" external>A</a></li>
        <li><a href="#B" external>B</a></li>
        <li><a href="#C" external>C</a></li>
        <li><a href="#D" external>D</a></li>
        <li><a href="#E" external>E</a></li>
        <li><a href="#F" external>F</a></li>
        <li><a href="#G" external>G</a></li>
        <li><a href="#H" external>H</a></li>
        <li><a href="#I" external>I</a></li>
        <li><a href="#J" external>J</a></li>
        <li><a href="#K" external>K</a></li>
        <li><a href="#L" external>L</a></li>
        <li><a href="#M" external>M</a></li>
        <li><a href="#N" external>N</a></li>
        <li><a href="#O" external>O</a></li>
        <li><a href="#P" external>P</a></li>
        <li><a href="#Q" external>Q</a></li>
        <li><a href="#R" external>R</a></li>
        <li><a href="#S" external>S</a></li>
        <li><a href="#T" external>T</a></li>
        <li><a href="#U" external>U</a></li>
        <li><a href="#V" external>V</a></li>
        <li><a href="#W" external>W</a></li>
        <li><a href="#X" external>X</a></li>
        <li><a href="#Y" external>Y</a></li>
        <li><a href="#Z" external>Z</a></li>
    </ul>
</div> -->
@stop

@section($js)
<script type="text/javascript">
$(function(){
    //城市父级编号
    var pid = "{{$args['pid']}}";

    //首字母排序筛选
    var toppdg = $(".bar-nav").height();
    toppdg += $(".bar-header-secondary").height();
    $(".y-zmlist").css("top",toppdg);
    var liH = $(".y-zmlist").height();
    liH = liH/27;
    $(".y-zmlist ul li").css({"height":liH+"px","line-height":liH+"px"});

    //多选
    $(document).off("click",".y-citychoice");
    $(document).on("click",".y-citychoice",function(){
        if($(this).parents("li").find(".y-selectedico").hasClass("active")){
            $(this).parents("li").find(".y-selectedico").removeClass("active");
        }else{
            $(this).parents("li").find(".y-selectedico").addClass("active");
        }
    })

    //搜索
    $("#search").keyup(function(){
        var keywords = $.trim($(this).val());
        var obj = $("ul.y-backgroundnone li");
        obj.removeClass('none');

        if(keywords != "")
        {
            obj.each(function(k, v){
                var cityName = $(this).find("div.item-title").text();
                if( cityName.indexOf(keywords) == -1)
                {
                    $(this).addClass('none');
                }
            });
            $('.a_z').addClass('none');
        }
        else
        {
            $('.a_z').removeClass('none');
        }
    })

    //完成
    $(document).off("click", "#regionId");
    $(document).on("click", "#regionId", function(){
        $.showPreloader('数据提交中...');
        var regionIds = []; //保存城市编号
        
        $(".city_page_2 i.y-selectedico").each(function(k, v){
            if($(this).hasClass('active')){
                regionIds.push($(this).data('id'));
            }
        });

        $.post("{{ u('Seller/saveCheckLocation') }}", {'pid':pid, 'ids':regionIds, "modelId":"{{$args['modelId']}}"}, function(){
            $.hidePreloader();
            JumpURL("{{u('Seller/checkLocation',['modelId'=>$args['modelId']])}}",'#seller_checkLocation_view',2);
        });
    });
})
</script>
@stop
