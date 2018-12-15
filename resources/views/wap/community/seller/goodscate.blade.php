@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left" onclick="$.href('{{u('Seller/detail',['id'=>$args['sellerId']])}}')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <a class="button button-link button-nav pull-right showmore c-black" href="###" data-popup=".popup-about" external>
            <span class="icon iconfont">&#xe692;</span>
            <span class="y-redc"></span>
        </a>
        <h1 class="title f16">商品分类</h1>
    </header>
    <ul class="x-ltmore f12 c-gray none">
        <link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
        @foreach($indexnav as $key => $i_nav)
            <li class="pl20" onclick="$.href('{{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }}')"><i class="icon iconfont mr5 vat">{{explode(",",$i_nav['icon'])[0].";"}}</i>
                {{$i_nav['name']}}
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine' && (int)$counts['newMsgCount'] > 0)
                    <span class="x-dot f12 none">{{(int)$counts['newMsgCount'] > 99? '99+' : (int)$counts['newMsgCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
                @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'goodscart' && (int)$counts['cartGoodsCount'] > 0)
                    <span class="x-dot f12 none" id="tpGoodsCart">{{(int)$counts['cartGoodsCount'] > 99 ? '99+' : (int)$counts['cartGoodsCount']}}</span>
                    <script type="text/javascript">
                        $(".y-redc").removeClass("none");
                    </script>
                @endif
            </li>
        @endforeach
    </ul>
@stop

@section('content')
    <div class="content" id=''>
        <div class="list-block media-list mt10">
            <ul class="y-wd">
                <li>
                    <a href="#" class="item-link item-content" external>
                        <div class="item-inner pr10">
                            <div class="item-title-row">
                                <div class="item-title f14">全部宝贝</div>
                                <div class="item-after f12 c-gray">
                                    <span>({{ $count }}件商品)</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>

            <ul class="y-wd">
                @foreach($cate as $ckey => $item)
                    @if(count($item['goods']) > 0)
                    <li>
                        <a href="{{u('Seller/categoods',['id'=>$item['id'],'sellerId'=>$args['sellerId']])}}" class="item-link item-content" external>
                            <div class="item-inner pr10">
                                <div class="item-title-row">
                                    <div class="item-title f14">{{$item['name']}}</div>
                                    <div class="item-after f12 c-gray">
                                        <span>({{ count($item['goods']) }}件商品)</span>
                                        <i class="icon iconfont c-gray2 vat">&#xe602;</i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@stop

@section($js)
    <script type="text/javascript">
        //右上角更多
        $(".showmore").click(function(){
            if($(".x-ltmore").hasClass("none")){
                $(".x-ltmore").removeClass("none");
            }else{
                $(".x-ltmore").addClass("none");
            }
        });
    </script>
@stop

 