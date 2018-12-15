<nav class="bar bar-tab"> 
<link rel="stylesheet" href="{{ asset('wap/community/newclient/index_iconfont/iconfont.css') }}?{{ TPL_VERSION }}">
    <?php
        $keyVal = -1;
        $nowUrl = CONTROLLER_NAME.'/'.ACTION_NAME;
        foreach($indexnav as $k=>$v) {
            $indexnav[$k]['isActive'] = 0;
            $url = Lang::get('api_system.index_link.'.$v['type']);
            $navType = Lang::get('api_system.index_nav.'.$v['type']);
            if($nowUrl == $url) {
                $keyVal = $k;
            } elseif ($nav == $navType && $keyVal == -1) {
                $keyVal = $k;
            }
        }
        if($keyVal != -1) {
            $indexnav[$keyVal]['isActive'] = 1;
        }
    ?>

    @foreach($indexnav as $key => $i_nav)
        @if($i_nav['type'] == 11 && !IS_OPEN_FX)
            <php>
                continue;
            </php>
        @endif
    <a class="tab-item  @if($i_nav['isActive'] == 1) active @endif pageloading" href="@if($i_nav['type'] == 1) {{ u('Index/index',['nourl'=>1]) }} @else  {{ u(Lang::get('api_system.index_link.'.$i_nav['type'])) }} @endif" data-no-cache="true">
        <php>
        $current_icon = explode(',', $i_nav['icon']);
        </php>
        <span class="icon iconfont" style="padding-right:15px;"> 
            @if($i_nav['isActive'] == 1)
                {!! $current_icon[0].';' !!} 
            @else 
                {!! $current_icon[1].';' !!} 
            @endif
            @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'mine')
               {{--<span class="x-dot f12">{{(int)$counts['newMsgCount'] > 99? '99+' : (int)$counts['newMsgCount']}}</span>--}}
            @endif
            @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'forum' && (int)$counts['systemCount'] > 0 )
                <span class="x-dot f12">{{(int)$counts['systemCount'] > 99? '99+' : (int)$counts['systemCount']}}</span>
            @endif
            @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'msg' && ((int)$counts['systemCount'] > 0 || (int)$counts['newMsgCount']))
                <span class="y-redc"></span>
            @endif
            @if(Lang::get('api_system.index_nav.'.$i_nav['type']) == 'goodscart' && (int)$counts['cartGoodsCount'] > 0)
                    <span class="x-dot f12" id="tpGoodsCart">{{(int)$counts['cartGoodsCount'] > 99 ? '99+' : (int)$counts['cartGoodsCount']}}</span>
            @endif
        </span>
        <span class="tab-label">{{$i_nav['name']}}</span>
    </a>
    @endforeach  
</nav>
