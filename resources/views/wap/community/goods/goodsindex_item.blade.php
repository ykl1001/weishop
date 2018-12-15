
<?php $leftsort = 0;
$i = 0;
?>
@foreach($cate as $ckey => $item)
    @if($item['goodscounts'] > 0)
        <div id="tab_{{$ckey}}" data-tabid = "{{$ckey}}" class="y-tab tab @if($item['id'] == Input::get('cateId')) active @else @if(Input::get('cateId') == "" && $leftsort == 0) active @endif  @endif">
            @if(empty($page))
            <div class="x-goodstit">
                <div class="item-title f15 c-gray">
                    @if($item['id'] == Input::get('cateId')) {{$item['name']}}({{$item['goodscounts']}}) @else {{$item['name']}}({{$item['goodscounts']}}) @endif
                </div>
            </div>
            @endif

            <div class="list-block media-list x-sortlst f14 nobor pr "><!--y-pull pull-to-refresh-content-->
                <ul>
                    @foreach($item['goods'] as $k=>$v)
                        <?php
                        //存在规格和折扣 获取规格最低价 根据折扣结算出新的特价
                        if(count($v['norms']) > 0 && !empty($v['activity']))
                        {
                            $f = true;
                            foreach ($v['norms'] as $key => $value) {
                                $salePrice = $value['price'] * $v['activity']['sale'] / 10;

                                $v['norms'][$key]['salePrice'] = $salePrice;

                                if($f)
                                {
                                    $v['activity']['minNormsPrice'] = $salePrice;
                                    $v['price'] = $value['price'];
                                    $f = false;
                                }
                                elseif(!$f && $salePrice <= $v['activity']['minNormsPrice'])
                                {
                                    $v['activity']['minNormsPrice'] = $salePrice; //最低折扣价
                                    $v['price'] = $value['price']; //最低原价
                                }

                            }
                        }

                        ?>
                        <li class="item-content">
                            <div class="item-inner pl0">
                                <div class="item-title">
                                    <div onclick="$.href('{{u('Goods/detail',['goodsId'=>$v['id'],'type'=>$v['type']])}}')">
                                        <div class="goodspic fl mr5">
                                            @if($ajax == 1)
                                                <img src="{{$v['images'][0]}}">
                                            @else
                                                <img class="lazyload" data-original="@if($v['images'][0]) {{ formatImage($v['images'][0],150,150) }} @else {{ asset('wap/community/client/images/wykdimg.png') }} @endif">
                                            @endif
                                        </div>
                                        <span class="goodstit">{{$v['name']}}</span>
                                    </div>
                                    <div class="mt5">
                                                    <span class="c-red f15">
                                                        @if(empty($v['activity']))
                                                            ￥{{number_format($v['price'], 2)}}
                                                        @else
                                                            @if(empty($v['activity']['minNormsPrice']))
                                                                ￥{{number_format($v['activity']['salePrice'], 2)}} <!-- 折扣价 -->
                                                            @else
                                                                ￥{{number_format($v['activity']['minNormsPrice'], 2)}} <!-- 规格最低价 -->
                                                            @endif
                                                            <del class="f12 c-gray ml5">￥{{number_format($v['price'], 2)}}</del>
                                                        @endif
                                                    </span>
                                        @if($seller['serviceTimesCount'] > 0)
                                            @if($v['stockTypeId'] < 1)
                                                <div class="x-num fr  goodsId_show{{$v['id']}}">
                                                    <i class="icon iconfont c-gray subtract fl <?php if(empty($cartgoods[$v['id']][0]['num'])) echo "none"; ?>">&#xe622;</i>
                                                    <span class="val tc pl0 fl <?php if(empty($cartgoods[$v['id']][0]['num'])) echo "none"; ?>" data-goodsid="{{$v['id']}}" data-normsid="0" data-price="{{ round($v['price'], 2) }}" data-saleprice="{{ round($v['activity']['salePrice'], 2) }}"><?php if(empty($cartgoods[$v['id']][0]['num'])) echo "0"; else echo$cartgoods[$v['id']][0]['num']; ?></span>
                                                    <i class="icon iconfont c-red add fl">&#xe61f;</i>
                                                </div>
                                            @else
                                                <div class="fr c-red f12 y-xgg totalPrice" onclick="$.showGoodsSkus({{$v['id']}}, 'cartByShow');"><i class="icon iconfont c-red fl">&#xe61f;</i></div>
                                            @endif
                                        @else
                                            <span class="c-gray f12 fr">商家休息中</span>
                                        @endif

                                        @if(!empty($v['activity']))
                                            <div class="y-specialprice f12"><a href="" class="f12">{{$v['activity']['sale']}}折特价</a></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <?php $leftsort++; ?>
    @endif
@endforeach
