@foreach($list as $v)
<li onclick="$.href('{{u('Repair/detail', ['id'=>$v['id'], 'districtId'=> $v['districtId']])}}')">
    <label class="label-checkbox item-content">
        <div class="item-media"></div>
        <div class="item-inner">
            <div class="item-subtitle mb5">
                <span class="f14 y-w50 y-ell">故障类型:{{$v['repairType']}}</span>
                <span class="f14 c-gray fr">{{ $v['createTime'] }}</span>
            </div>
            <div class="item-text ha f14">故障简介:{{mb_substr($v['content'], 0, 20) . '......'}}</div>
            @if($v['images'][0])
                <ul class="x-postpic clearfix">
                    @foreach($v['images'] as $val)
                        <li class="m0">
                            <img src="{{ $val }}">
                        </li>
                    @endforeach
                </ul>
            @endif
            @if($v['status'] != 0)
                <div class="item-text ha f14 pt10 cb">维修人员:{{ $v['staff']['name'] }} {{ $v['staff']['mobile'] }}</div>
            @endif
            <div class="tr c-red cb f12 mt10">
                {{ $v['statusStr'] }}
            </div>
        </div>
    </label>
</li>
@endforeach