@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" href="@if(!empty($nav_back_url)) {!! $nav_back_url !!} @else javascript:$.back(); @endif" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a href="{{ u('Index/cityservice') }}"><h1 class='title'>城市选择</h1></a>
    </header>
@stop

@section('content')
    <div class="bar bar-header-secondary heightauto y-backgroundnone">
        <div class="searchbar c-bgfff y-searchbar">
            <div class="search-input">
                <i class="icon iconfont">&#xe61c;</i>
                <input type="search" id='search' placeholder='输入城市名查询'/>
            </div>
        </div>
    </div>
    <div class="content" id=''>
        <div class="p10 f14 c-black c-bgfff mb10">当前定位城市：{{$cityinfo['name']}}</div>
        <div class="list-block nobor x-hislst">
            <ul class="y-backgroundnone x-hislst2">
                @foreach($city as $k=>$s)
                    <div class="content-block-title mt5 mb5" id="{{$k}}">{{$k}}</div>
                    @foreach($s as $k2=>$v)
                        @if($v['citylocation']['lat'])
                        <li data-name="{{$v['name']}}" data-mappoint="{{ preg_replace("/\s+/","",$v['citylocation']['lat']).",".preg_replace("/\s+/","",$v['citylocation']['lng']) }}" data-city="{{$v['name']}}">
                            <a href="javascript:;" class="item-content c-black pr20 active" external>
                                <div class="item-inner pr0 @if($k2 == 0) y-nobor2 @endif">
                                    <div class="item-title-row">
                                        <div class="item-title">{{$v['name']}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                    @endforeach
                @endforeach
            </ul>

        </div>

        <div class="y-zmlist f12">
            <ul>
                <li><a href="">#</a></li>
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
        </div>
    </div>
@stop

@section($js)
    <script>
        var cartIds = "{{ Input::get('cartIds') }}";
        //首字母排序筛选
        var toppdg = $(".bar-nav").height();
        toppdg += $(".bar-header-secondary").height();
        $(".y-zmlist").css("top",toppdg);
        var liH = $(".y-zmlist").height();
        liH = liH/27;
        $(".y-zmlist ul li").css({"height":liH+"px","line-height":liH+"px"});
        $(".y-zmlist ul li").click(function(){
            $(".y-zmlist ul li a").removeClass('c-red');
            $(this).children("a").addClass('c-red');
        });

        $(document).on('click','.x-hislst2 li',function () {
            var address = $(this).attr('data-name')
            var mapPointStr = $(this).attr('data-mappoint');
            var city = $(this).attr('data-city');
            var type = "{{$args['type']}}";
            var type2 = "{{$args['type2']}}";
            var oneself = "{{ $args['oneself'] }}";
            var data = {
                "address":address,
                "mapPointStr":mapPointStr,
                "city":city
            };
            $.post("{{ u('Index/relocation2') }}",data,function(res){
                if(res.code == 1){
                    $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                }else{
                     if(type == 1){
                         $.router.load("{{u('UserCenter/addressdetail',['SetNoCity'=>Input::get('SetNoCity'),'newadd'=>Input::get('newadd')])}}&address="+data.address+"&mapPointStr="+data.mapPointStr+"&cityId="+res.cityId+"&gps=1&cartIds="+cartIds, true)
                    }else if(type == 2){
                        $.router.load("{{u('Index/addressmap')}}?address="+data.address+"&mapPointStr="+data.mapPointStr+"&cityId="+res.cityId+"&cartIds="+cartIds, true)
                    }else if(type == 3){
                        $.router.load("{{u('Property/typepay')}}?type="+type2+"&address="+data.address+"&mapPointStr="+data.mapPointStr+"&cityId="+res.cityId+"&cartIds="+cartIds, true)
                    }else if(type == 4){
                         $.router.load("{{u('Index/district')}}?SetNoCity=1&address="+data.address+"&location="+data.mapPointStr+"&cityId="+res.cityId, true)
                    }
                }
            },"json");
        });

        $("#search").on('input paste', function() {
            var keywords = $(this).val();
            $.post("{{ u('Index/citysearch') }}",{keywords:keywords},function(res){
                if(res.code == 1){
                    $.toast("抱歉，当前城市未开通服务，请选择其他城市吧");
                }else{
                    var _html = '';
                    if(res.data){
                        $.each(res.data,function(i){
                            _html +='<div class="content-block-title mt5 mb5" id="'+i+'">'+i+'</div>';
                            $.each(this,function(s){
                                _html +='<li data-name="'+ this.name+'" data-mappoint="'+this.mappoint+'" data-city="'+ this.name+'">';
                                _html +='<a href="javascript:;" class="item-content c-black pr20 active" external>';
                                _html +='<div class="item-inner pr0">';
                                _html +=' <div class="item-title-row">';
                                _html +='<div class="item-title">'+ this.name+'</div>';
                                _html +='</div>';
                                _html +=' </div>';
                                _html +=' </a>';
                                _html +=' </li>';
                            });
                        });
                        $('.x-hislst2').html(_html);
                    }
                }
            },"json");

        });
    </script>
@stop