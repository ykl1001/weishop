@foreach($data as $k=>$v)
    <div class="content-block-title f12 tc">{{Time::toDate($v['sendTime'],'Y年m月d日 H:i:s')}}</div>
    <div class="card">
        <div class="card-content">
            <div class="card-content-inner f12 c-gray3"> {{$v['content']}}</div>
        </div>
        <div class="card-footer"><span class="c-gray3 f12">推荐人:{{$v['user']['name']}}</span>
            <a href="{{u('Invitation/userlists')}}" class="c-gray3 f12">查看详情</a>
        </div>
    </div>
@endforeach