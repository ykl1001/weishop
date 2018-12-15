@foreach($data as $v)
    <div class="col-50">
        <div class="card demo-card-header-pic " onclick="JumpURL('{{u('Seller/editnewsystem',['systemTagListPid'=>$v['systemTagListPid']['id'],'systemTagListId'=>$v['systemTagListId'],'type'=>1,'tradeId'=>$tradeId,'id'=>$v['id']])}}','#seller_editnewsystem_view',1)" >
            <div valign="bottom" class="card-header color-white no-border no-padding">
                <img class="card-cover" src="{{ formatImage($v['images'][0],320,320) }}" alt="{{$v['name']}}">
            </div>
            <div class="card-content">
                <div class="card-content-inner">
                    <span class="f12 vat">{{$v['name']}}</span><span class="fr f15 c-red vat">￥{{$v['price']}}</span>
                </div>
            </div>
        </div>
    </div>
@endforeach