@foreach($list as $v)
    <li>
        <a href="#" class="item-link item-content y-nobor2">
            <div class="item-media"><img src="{{ formatImage($v['images'],80,80) }}"></div>
            <div class="item-inner"  onclick="$.href('{{u('Integral/get',['id' => $v['id']])}}')">
                <div class="item-title-row">
                    <div class="item-title f14">{{ $v['name'] }}</div>
                    <div class="item-after mt20"><i class="icon iconfont c-gray2">&#xe602;</i></div>
                </div>
                <div class="item-subtitle mt10 y-f14">
                    <span class="c-gray5 f12">兑换时间：{{ $v['createTime'] }}</span>
                </div>
            </div>
        </a>
    </li>
@endforeach