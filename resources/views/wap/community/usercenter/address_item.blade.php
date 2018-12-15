@foreach($list as $v)
    <div class="card y-card y-address y-address{{ $v['id'] }} yz-address @if($v['isDefault']) active @endif  @if($v['isCanServer'] != 1 && Input::get('sellerId') > 0)y-opacity @endif" data-id="{{ $v['id'] }}">
        @if($v['isCanServer'] != 1 && Input::get('sellerId') > 0)
        <div class="y-judgmentaddr"><img src="{{ asset('wap/community/newclient/images/y26.png') }}"></div>
        @endif

        <div class="card-content">
            <div class="card-content-inner"   @if($v['isCanServer'] != 1 && Input::get('sellerId') > 0) data-id="0"  @else data-id="{{ $v['id'] }} @endif">
                <p class="clearfix"><span>{{ $v['name'] }}</span><span class="fr">{{ $v['mobile'] }}</span></p>
                <p class="mt5">{{ $v['province']['name'] }} {{ $v['city']['name'] }} {{ $v['address'] }}</p>
            </div>
        </div>
            @if($v['isCanServer'] != 1 && Input::get('sellerId') > 0)

            @else
            <div class="card-footer c-gray2 f12">
                <div><i class="icon iconfont mr5 f20 vat c-red y-addron x-setDuf">&#xe612;</i>@if($v['isDefault']) 默认 @else 设为默认 @endif</div>
                <div>
                    <span class="mr10 urlte pageloading"><i class="icon iconfont mr5 f18 vat">&#xe63c;</i>编辑</span>
                    <span class="y-del"><i class="icon iconfont mr5 f18 vat">&#xe630;</i>删除</span>
                </div>
            </div>
           @endif
    </div>
@endforeach
