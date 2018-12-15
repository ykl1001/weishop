@foreach($cate as $key => $goods)
    <li>
        <a href="#" onclick="$.href('{{u('Goods/detail',['goodsId'=>$goods['id'],'type'=>$goods['type']])}}')" class="c-bgfff pageloading">
            <div class="x-fwpic pr mb5">
                <img src="{{ formatImage($goods['logo'], 300, 300)}}" />
            </div>
            <p class="f12 c-black">
                <span class="fl na">{{$goods['name']}}</span>
                @if($goods['unit'] == 2)
                    <span class="time">1次</span>
                @else
                    <span class="time">{{$goods['duration']}}分钟</span>
                @endif
            </p>
            <p class="c-red f13 mb5">
                <span class="y-fwmaxw">
                    @if(empty($goods['activity']))
                        ￥{{number_format($goods['price'], 2)}}
                    @else
                        ￥{{number_format($goods['activity']['salePrice'], 2)}}
                        <del class="f12 c-gray">￥{{number_format($goods['price'], 2)}}</del>
                    @endif
                </span>
                @if(!empty($goods['activity']))
                    <!-- <span class="fr c-red f12">{{$goods['activity']['sale']}}折特价</span> -->
                @endif
            </p>
        </a>
    </li>
@endforeach