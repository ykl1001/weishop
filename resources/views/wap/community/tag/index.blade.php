@extends('wap.community._layouts.base')
@section('show_top')
    <header class="bar bar-nav">
        <div class="searchbar x-tsearch y-search">
            <div class="search-input pr dib"  style="width: 100%">
                <input type="search" id='search' placeholder='搜索附近商品或门店' onclick="$.href('{{u('Seller/search')}}')" readonly/>
            </div>
            <!-- <a class="button button-fill button-primary c-bg">搜索</a> -->
        </div>
    </header>
@stop

@section('content') 
<?php 
    $one = $two = true;
 ?>
    @include('wap.community._layouts.bottom')
    <div class="content tagpage" id=''>
        @if($cityIsService == 0)
            <!-- 未开通物业提示 -->
            <div class="x-null pa w100 tc">
                <img src="{{ asset('wap/community/newclient/images/nothing.png') }}" width="108">
                <p class="f12 c-gray mt10">附近没有发现其他门店，我们正在努力覆盖中</p>
                <a class="f14 c-white x-btn mt15" href="{{ u('Index/addressmap')}}">切换地址</a>
            </div>
        @else
            <div class="x-sjfltab x-goodstab clearfix">
                <div class="buttons-tab fl pr">
                    @foreach($data as $key => $value)
                        @if($value['id'] > 0)
                            <a href="#tab1_{{$key}}" class="tab-link button @if($one) active {{$one=false}} @endif">{{$value['name']}}</a>
                        @endif
                    @endforeach
                </div>
                <div class="tabs c-bgfff fl y-tabs">
                    @foreach($data as $key => $value)
                        @if($value['id'] > 0)
                            <div id="tab1_{{$key}}" class="tab @if($two) active {{$two=false}} @endif">
                                @foreach($value['twoLevel'] as $k2 => $v2)
                                <div>
                                    <div class="content-block-title y-fltitle">{{$v2['name']}}</div>
                                    <ul class="row no-gutter y-flnav c-bgfff">
                                        @foreach($v2['threeLevel'] as $k3 => $v3)
                                            <li class="col-50">
                                                <a href="{{u('Tag/goodsLists',['pid'=>$value['id'],'id'=>$v3['id']])}}" class="db" data-no-cache="true">
                                                    @if(!empty($v3['img']))
                                                        <span class="y-flimg">
                                                            <img src="{{$v3['img']}}">
                                                        </span>
                                                    @endif
                                                    <p class="f13 mt5 tc">{{$v3['name']}}</p>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@stop 

@section($js)
<script>
    $(function(){
        var height = $(".bar-nav").height();
        height += $(".bar-tab").height();
        $(".x-goodstab .tab").css("height",$(window).height()-height);
        // $(".x-goodstab .buttons-tab").css("height",$(window).height()-height);
        $(".x-goodstab .buttons-tab").css({"height":$(window).height()-height,"overflow": "scroll"});
    })
    $(".tagpage").css({"bottom":0,"overflow": "hidden"});
</script>
@stop
