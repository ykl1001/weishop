@foreach($lists as $key => $value)
    <div class="col-50">
        <div class="card demo-card-header-pic">
            <div  onclick="$.href('{{u('Goods/detail',['goodsId'=>$value['id']])}}')">
                <div valign="bottom" class="card-header color-white no-border no-padding">
                    <img class="card-cover" src="{{formatImage($value['logo'], 320, 320, 2)}}" alt="">
                </div>
                <div class="card-content">
                    <div class="card-content-inner">
                        <span class="f12 vat">{{$value['name']}}</span><span class="fr f15 c-red vat">￥{{$value['price']}}</span>
                    </div>
                </div>
            </div>
            <div class="card-footer f12 c-gray2 " onclick="$.href('{{u('Seller/detail',['id'=>$value['seller']['id']])}}')">
                <span>
                    <i class="icon iconfont vat mr5">&#xe632;</i>
                    {{$value['seller']['name']}}
                </span>
                <i class="icon iconfont f14">&#xe602;</i>
            </div>
        </div>
    </div>
@endforeach