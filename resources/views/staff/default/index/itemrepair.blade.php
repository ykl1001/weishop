@foreach($list['list'] as $v)
    <div class="content-block-title f12 m10">{{$v['createTime']}}</div>
    <div class="y-xwxdbox" onclick="JumpURL('{{u('Repair/detail',['id'=>$v['id']])}}','#repair_detail_view',2)">
        <p class="bold f14 y-ell">{{$v['build']['name']}} {{$v['room']['roomNum']}}</p>
        <p class="f12 f_999 mt5">报修人：{{$v['puser']['name']}}<span class="ml10">{{$v['puser']['mobile']}}</span></p>
        <p class="f12 f_999 mt5">报修类型：{{$v['repairType']}}</p>
        <p class="f12 mt5">维修时间：{{$v['apiTime']}}</p>
    </div>
@endforeach