@foreach($list as $key => $value)
    @if($value['checked_disabled'] == 1)
    <!-- 已经选择的商品 -->
    <li data-id="{{$value['id']}}" class="false">
        <a href="#" class="item-link item-content y-gray">
            <div class="item-media"><img src="{{$value['image']}}" width="52"></div>
            <div class="item-inner">
                <div class="item-title-row">
                    <div class="item-title f_333 f14">{{$value['name']}}</div>
                    <div class="item-after">
                        <i class="icon iconfont f_ccc f20 f24 mt10 none">&#xe677;</i>
                        <i class="icon iconfont f_ccc f20 f24 mt10 choose">&#xe638;</i>
                    </div>
                </div>
                <div class="item-subtitle">
                    <span class="f_red">￥{{$value['price']}}</span>
                </div>
            </div>
        </a>
    </li>
    @else
    <li data-id="{{$value['id']}}" class="true">
        <a href="#" class="item-link item-content">
            <div class="item-media"><img src="{{$value['image']}}" width="52"></div>
            <div class="item-inner">
                <div class="item-title-row">
                    <div class="item-title f_333 f14">{{$value['name']}}</div>
                    <div class="item-after">
                        <i class="icon iconfont f_red f24 mt7">&#xe677;</i>
                        <i class="icon iconfont f_red f24 mt7 choose none">&#xe638;</i>
                    </div>
                </div>
                <div class="item-subtitle">
                    <span class="f_red">￥{{$value['price']}}</span>
                </div>
            </div>
        </a>
    </li>
    @endif
@endforeach