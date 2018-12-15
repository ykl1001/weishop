@foreach($account as $v)
    <li class="item-content auto_height">
        <div class="item-inner">
            <div class="item-title">
                <time>{{$v['createTime']}}</time>
                <div>备注：{{$v['remark']}}</div>
                @if(!empty($v['refundInfo']))<div style="white-space:normal">拒绝理由：{{$v['refundInfo']['disposeRemark']}}</div>@endif
            </div>
            <div class="item-after">
                <div class="tr">
                    <div class="f_success {{$v['statusColor']}}">{{$v['money']}}</div>
                    <div class="f_success {{$v['statusColor']}}">{{$v['statusStr']}}</div>
                </div>
            </div>
        </div>
    </li>
@endforeach