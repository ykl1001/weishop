@extends('wap.community._layouts.base')

@section('show_top')
    <header class="bar bar-nav">
        <a class="button button-link button-nav pull-left pageloading"  onclick="$.href(' @if(!empty($nav_back_url) && strpos($nav_back_url, u('Goods/index')) === false && strpos($nav_back_url, u('Goods/detail')) === false && strpos($nav_back_url, u('Goods/comment')) === false) {{$nav_back_url}} @else {{ u('Index/index') }} @endif')" data-transition='slide-out'>
            <span class="icon iconfont">&#xe600;</span>返回
        </a>
        <!-- <h1 class="title f16">{{$seller_data['name']}}</h1> -->
        <h1 class="title f16">商家详情</h1>
        <a class="button button-link button-nav pull-right open-popup collect_opration @if($seller_data['isCollect'] == 1) on @endif" data-popup=".popup-about">
            <!-- 分享 -->
            <i class="icon share iconfont c-black">&#xe616;</i>
            @if($seller_data['isCollect'] == 1)
                <i class="icon collect iconfont c-red m0">&#xe654;</i><!-- 已收藏图片  -->
            @else
                <i class="icon collect iconfont c-black m0">&#xe653;</i><!-- 未收藏图标 -->
            @endif
        </a>
    </header>
    @if($seller_data['countService'] > 0 || $seller_data['countGoods'] > 0)
    <nav class="bar bar-tab">
        <p class="buttons-row x-bombtn">
            @if($seller_data['countService'] > 0 && $seller_data['countGoods'] < 1)
                <a href="#" class="button" onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}')" @if($seller_data['isDelivery'] != 1) style="background:#ddd;" @endif>选择服务</a>
            @elseif($seller_data['countGoods'] > 0 && $seller_data['countService'] > 0)
                <a href="#" class="button" onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>2])}}')" @if($seller_data['isDelivery'] != 1) style="background:#ddd;" @endif>选择服务</a>
                <a href="#" class="button pr" onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}')" @if($seller_data['isDelivery'] != 1) style="background:#ddd;"
            @endif>购买商品</a>
            @elseif($seller_data['countService'] < 1 && $seller_data['countGoods'] > 0)
                <a href="#" class="button pr" onclick="$.href('{{ u('Goods/index',['id'=>$seller_data['id'],'type'=>1])}}')" @if($seller_data['isDelivery'] != 1) style="background:#ddd;"
            @endif>购买商品</a>
            @endif
        </p>
    </nav>
    @endif
@stop

@section('content')
<script type="text/javascript">
    BACK_URL = "@if(!empty($nav_back_url) && strpos($nav_back_url, u('Goods/index')) === false && strpos($nav_back_url, u('Goods/detail')) === false && strpos($nav_back_url, u('Goods/comment')) === false) {{$nav_back_url}} @else {{ u('Index/index') }} @endif";
</script>
<div class="content" id=''> 
    <div class="list-block media-list y-sylist y-sjxq">
        <ul>
            <li>
                <a href="#" class="item-link item-content">
                    <div class="item-media"><img src="{{formatImage($seller_data['logo'],55,55)}}" width="55"></div>
                    <div class="item-inner">
                        <div class="item-subtitle y-sytitle"><p>{{$seller_data['name']}}

                            </p></div>
                        <div class="item-title-row f12 c-gray">
                            <div class="item-title">
                                <div class="y-starcont">
                                    <div class="c-gray4 y-star">
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    </div>
                                    <div class="c-red f12 y-startwo" style="width:{{ $seller_data['score'] * 20 }}%">
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                        <i class="icon iconfont vat mr2 f12">&#xe654;</i>
                                    </div>
                                </div>
                                <span class="c-gray f12">已售{{$seller_data['orderCount']}}</span>
                            </div>
                        </div>
                        <div class="item-subtitle">
                            起送<span class="c-red mr5">￥{{ number_format($seller_data['serviceFee'], 2) }}</span>
                            <span class="mr5">|</span>
                            配送<span class="c-red">￥{{ number_format($seller_data['deliveryFee'], 2) }}</span>
                            @if($seller_data['isAvoidFee'] == 1)
                                <span class="c-gray">(满{{ number_format($seller_data['avoidFee'], 2) }}免)</span>
                            @endif

                        </div>
                    </div>
                </a>
                <div class="c-bgfff y-tag">
                    <div class="c-orange f12">
                        @if($seller_data['storeType'] == 0)周边店@else全国店@endif<img src="{{ asset('wap/community/newclient/images/y15.png')}}" class="va-1 ml2" width="12">
                            @foreach($seller_data['sellerAuthIcon'] as $val)
                                {{ $val['icon']['name'] }}  <img src="{{ $val['icon']['icon'] }}" class="ml5 va-3" width="12">
                            @endforeach


                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="list-block media-list x-store">
        <ul>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl">&#xe639;</i>
                                <span class="x-storer db">{{$seller_data['businessHours']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f17">&#xe608;</i>
                                <?php $seller_data['tel'] = !empty($seller_data['tel']) ? $seller_data['tel'] : $seller_data['mobile']; ?>
                                <span class="x-storer db"><a href="tel:{{$seller_data['tel']}}">{{$seller_data['tel']}}</a></span>
                            </div>
                            <a href="tel:{{$seller_data['tel']}}" class="icon iconfont c-white c-bg x-storephone f17">&#xe60a;</a>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f20">&#xe60d;</i>
                                <span class="x-storer db">{{$seller_data['address']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="item-content">
                    <div class="item-inner">
                        <div class="item-title-row">
                            <div class="item-title f14">
                                <i class="icon iconfont c-gray fl f18">&#xe647;</i>
                                <span class="x-storer db">
                                    @if(count($articles)>0)
                                        {!!$articles[0]['content']!!}
                                    @else 
                                        <span>暂无最新公告信息</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!-- 满减 -->
    @if(!empty($seller_data['activity']['full']) || !empty($seller_data['activity']['special']) || !empty($seller_data['activity']['new']))
        <div class="list-block media-list x-store">
            <ul>
                <?php $first = true; ?>
                @if(!empty($seller_data['activity']['full']))
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/jian.png') }}" width="20" class="fl">
                                    <span class="x-storer db">
                                        在线支付
                                        @foreach($seller_data['activity']['full'] as $key => $value)
                                            @if($first)
                                                <?php $first = false; ?>
                                                满{{ number_format($value['fullMoney'], 2) }}减{{ number_format($value['cutMoney'], 2) }}元
                                            @else
                                                ,满{{ number_format($value['fullMoney'], 2) }}减{{ number_format($value['cutMoney'], 2) }}元
                                            @endif
                                        @endforeach
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endif
                @if(count($seller_data['activity']['special']) > 0)
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/tei.png') }}" width="20" class="fl">
                                    <span class="x-storer db">商家特价优惠</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endif
                @if(!empty($seller_data['activity']['new']))
                <li>
                    <div class="item-content">
                        <div class="item-inner">
                            <div class="item-title-row">
                                <div class="item-title f14">
                                    <img src="{{ asset('wap/community/newclient/images/ico/xin.png') }}" width="20" class="fl">
                                    <span class="x-storer db">新用户在线支付立减{{ number_format($seller_data['activity']['new']['cutMoney'], 2) }}元</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    @endif
    <div class="content-block-title f14 c-gray">商家介绍</div>
    <div class="card m0">
        <div class="card-content">
            <div class="card-content-inner p10">{{ $seller_data['detail'] ? $seller_data['detail'] : '暂无介绍'}}</div>
        </div>
    </div>
</div>
@include('wap.community.goods.share')
@stop

@section($js)
    <script>
        $(document).off("touchend", ".collect_opration .collect");
        $(document).on("touchend", ".collect_opration .collect", function(){
            var obj = new Object();
            var collect = $(this);
            obj.id = "{{$seller_data['id']}}";
            obj.type = 2;
            if(collect.hasClass("c-red")){
                $.post("{{u('UserCenter/delcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.removeClass("on");
                        $.alert(result.msg,function(){
                            collect.removeClass('c-red').addClass('c-black').html('&#xe653;');
                        });
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.alert(result.msg);
                    }
                },'json');
            }else{
                $.post("{{u('UserCenter/addcollect')}}",obj,function(result){
                    if(result.code == 0){
                        collect.addClass("on");
                       $.alert(result.msg,function(){
                            collect.removeClass('c-black').addClass('c-red').html('&#xe654;');
                        });
                    } else if(result.code == 99996){
                        $.router.load("{{u('User/login')}}", true);
                    } else {
                        $.alert(result.msg);
                    }
                },'json');
            }
        });

    </script>
@stop

 