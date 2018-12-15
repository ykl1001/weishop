@foreach($data as $k=>$v)
    <div class="content-block-title f12 tc">{{Time::toDate($v['sendTime'],'Y年m月d H:i:s')}}</div>
    <div class="card">
        <div class="card-header">
             <span class="f13 pr">{{$v['title']}}
                 @if($v['readCount'] == 0)
                     <span class="y-redc none" style="top:18%;right:-8px;"></span>
                 @endif
			</span>
            <i class="icon iconfont f14 c-gray"></i></div>
        <div class="card-content">
            <div class="list-block media-list">
                <ul>
                    <li>
                        <a href='#'
                        @if($v['args'] != '' && $v['args'] != u('UserCenter/message') && $v['args'] != u('UserCenter/msgshow',['sellerId'=>0]))
                           onclick="$.href('{{ $v['args'] }}')"
                        @elseif($v['args'] = u('UserCenter/message') && !empty($v['orderId']))
                           onclick="$.href('{{ u('PropertyFee/index',['sellerId'=>$v['orderId']]) }}')"
                       @endif
                           class="item-link item-content">
                            <div class="item-media">
                                @if($v['sendType'] == 1)
                                    <img src="{{asset('wap/community/newclient/images/tz.png') }} " width="40">
                                @elseif($v['sendType'] == 5)
                                    <img src="{{asset('wap/community/newclient/images/y21.png') }} " width="40">
                                @endif
                            </div>
                            <div class="item-inner f12 pr10">
                                <div class="item-text f13">{{$v['content']}}</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endforeach