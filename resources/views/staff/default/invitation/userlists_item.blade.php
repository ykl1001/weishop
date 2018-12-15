@if($lists)
    @foreach($lists as $item)
        <ul class="row no-gutter c-bgfff y-wdhylist">
            <li class="col-33 tc">
                <p class="c-black f14">{{$item['name']}}</p>
            </li>
            <li class="col-33 tc">
                <p class="c-black f14">{{$item['percent']}}</p>
            </li>
            <li class="col-33 tc">
                <p class="c-red f15">￥{{$item['commision']}}</p>
            </li>
        </ul>
    @endforeach
@endif