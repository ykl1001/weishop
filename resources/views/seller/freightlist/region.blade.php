@yizan_begin
<div class="m10 clearfix">
    <div class="fl udb_div udb_div_province">
        选择支持的配送地区（可多选）
        <ul class="udb_c">
            @foreach($lists as $key => $value)
                @foreach($value as $k => $v)
                    <li class="udb_province_show{{$v['id']}} @if($v['selected']) udb_c_on @endif" data-id="{{$v['id']}}" data-id="{{$v['id']}}">
                        {{$v['name']}}
                    </li>
                @endforeach
            @endforeach
        </ul>
    </div>
    <div class="fl udb_div udb_div_city ml10">
        二级（可多选）
        <ul class="udb_c">

        </ul>
    </div>
</div>
@yizan_end


