@foreach($integral as $v)
    <li>
        <a href="#" class="item-link item-content">
            <div class="item-inner pr10">
                <div class="item-title-row">
                    <div class="item-title f13">签到获得<span class="f12 c-gray2">({{ yztime($v['createTime'], 'Y-m-d H:i') }})</span></div>
                    <div class="item-after f13 c-red">+{{$v['integral']}}</div>
                </div>
            </div>
        </a>
    </li>
@endforeach
