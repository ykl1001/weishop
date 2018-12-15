@foreach($list['list'] as $v)
    <li class="item-content">
        <div class="item-inner f14">
            <div class="item-title-row">
                <div class="item-title f15 c-black">{{ $v['desc'] }}</div>
                @if($v['type'] == 1)
                    <div class="item-after f13 mt10 c-red">+{{$v['integral']}}</div>
                @else
                    <div class="item-after f13 mt10">-{{$v['integral']}}</div>
                @endif
            </div>
            <div class="item-title-row mt-10">
                <div class="item-title f12 c-gray">{{ yztime($v['createTime'],'Y-m-d') }}</div>
            </div>
        </div>
    </li>
@endforeach

