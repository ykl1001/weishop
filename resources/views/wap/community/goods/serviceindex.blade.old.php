@extends('wap.community._layouts.base')

@section('cs')
@stop

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading" href="{{ $nav_back_url}}" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <h1 class="title f16">{{$seller['name']}}</h1>
        <a class="button button-link button-nav pull-right open-popup pageloading" href="{{ u('Seller/search')}}" data-popup=".popup-about"><i class="icon iconfont c-gray x-searchico">&#xe65e;</i></a>
    </header>
@stop

@section('content')
    <!-- new -->
    @include('wap.community._layouts.bottom')
    <div class="content pull-to-refresh-content infinite-scroll infinite-scroll-bottom" data-ptr-distance="55">

        <!-- <div class="x-bigpic">
            <img src="images/x1.png" class="w100 vab" />
        </div> -->
        <div class="pull-to-refresh-layer">
            <div class="preloader"></div>
            <div class="pull-to-refresh-arrow"></div>
        </div>

        @if(count($adv) > 1)
            <div class="swiper-container" data-space-between='10'>
                <div class="swiper-wrapper">

                    @foreach($adv as $key => $value)
                        <div class="swiper-slide">
                            @if($value['type'] == 1)
                                <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                                </a>
                            @elseif ($value['type'] == 2 || $value['type'] == 3)
                                <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                                </a>
                            @elseif ($value['type'] == 4)
                                <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                                    <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                                </a>
                            @else
                                <a href="{{ $value['arg'] }}">
                                    <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        @else
            <div class="x-bigpic pr">
                @foreach($adv as $key => $value)
                    @if($value['type'] == 1)
                        <a href="{{u('Seller/index',['id'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                        </a>
                    @elseif ($value['type'] == 2 || $value['type'] == 3)
                        <a href="{{u('Goods/detail',['goodsId'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                        </a>
                    @elseif ($value['type'] == 4)
                        <a href="{{u('Seller/detail',['id'=>$value['arg']])}}">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                        </a>
                    @else
                        <a href="{{ $value['arg'] }}">
                            <img _src="{{ formatImage($value['image'],640) }}" src="{{ formatImage($value['image'],640) }}" class="w100 vab"/>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="list-block media-list x-service nobor">
            <ul>
                <li>
                    <a href="{{u('Seller/detail',['id'=>$seller['id']])}}" class="item-link item-content pageloading">
                        <div class="item-media"><img src="{{ formatImage($seller['logo'], 100, 100)}}" width="80"></div>
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f16 mt5">{{$seller['name']}}</div>
                            </div>
                            <div class="item-subtitle"><i class="icon iconfont c-gray fr f13 vat">&#xe602;</i></div>
                            <div class="item-text f12 c-gray ha">营业时间：{{$seller['businessHours']}}</div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

        @if(!empty($cate))
        <ul class="x-fwlst pl5 pr5 clearfix" id="list">
            @include('wap.community.goods.service_item')
        </ul>
        @endif

        <!-- 加载完毕提示 -->
        <div class="pa w100 tc allEnd none">
            <p class="f12 c-gray mt5 mb5">数据加载完毕</p>
        </div>
        <!-- 加载提示符 -->
        <div class="infinite-scroll-preloader none">
            <div class="preloader"></div>
        </div>
    </div>
@stop

@section($js)
    <!-- <script src="{{ asset('static/infinite-scroll/jquery.infinitescroll.js') }}"></script> -->


    <script type="text/javascript">
        $(function() {
            // 加载开始
            // 上拉加载
            var serviceIndexLoading = false;
            var serviceIndexNum = 2;
            $(document).off('infinite', '.infinite-scroll-bottom');
            $(document).on('infinite', '.infinite-scroll-bottom', function() {
                // 如果正在加载，则退出
                if (serviceIndexLoading) {
                    return false;
                }
                //隐藏加载完毕显示
                $(".allEnd").addClass('none');

                serviceIndexLoading = true;

                $('.infinite-scroll-preloader').removeClass('none');
                $.pullToRefreshDone('.pull-to-refresh-content');

                var data = new Object;
                data.page = serviceIndexNum;
                data.id = {{$option['id']}};
                data.type = {{$option['type']}};

                $.post("{{ u('Goods/indexList') }}", data, function(result){
                    serviceIndexLoading = false;
                    $('.infinite-scroll-preloader').addClass('none');
                    result  = $.trim(result);
                    if (result != '') {
                        serviceIndexNum++;
                        $('#list').append(result);
                        $.refreshScroller();
                    }else{
                        $(".allEnd").removeClass('none');
                    }
                });
            });

            // 下拉刷新
            $(document).off('refresh', '.pull-to-refresh-content');
            $(document).on('refresh', '.pull-to-refresh-content',function(e) {
                // 如果正在加载，则退出
                if (serviceIndexLoading) {
                    return false;
                }
                serviceIndexLoading = true;
                window.location.reload(true);
            });
            // 加载结束
        });
    </script>
@stop 