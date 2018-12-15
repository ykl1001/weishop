@foreach($list as $v)
    <div class="col-50"   onclick="$.href('{{u('Integral/detail',['id' => $v['id']])}}')">
        <p class="f13">{{$v['name']}}</p>
        <p class="f12"><span class="c-yellow f13">{{$v['exchangeIntegral']}}</span>积分</p>
        <div class="y-jfimg"><img src="{{ formatImage($v['image'],320) }}"></div>
    </div>
@endforeach