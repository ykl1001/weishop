@extends('admin._layouts.base')
@section('right_content')
    @yizan_begin
        <yz:form id="yz_form" action="save">
                <yz:fitem name="name" label="商品规格名称"></yz:fitem>
                <yz:fitem name="stock" label="规格属性名称">
                    @if($data['stock'])
                        @foreach($data['stock'] as $k=> $v)
                            <input type="text" class="u-ipttext" placeholder="@if($k == 0)必填@else非必填@endif"  name="stock[]"  value="{{$v}}">
                            @if(count($data['stock']) != $k+1)<br><br>@elseif($count > 0)<br><br>@endif
                        @endforeach
                        @if($count > 0 )
                            @for($i = 1;$i <= $count; $i++)
                                    <input type="text" class="u-ipttext" placeholder="非必填"  name="stock[]"  value="">
                                    @if($count != $i)<br><br>@endif
                            @endfor
                        @endif
                    @else
                        @for($i = 1;$i <= $count; $i++)
                            <input type="text" class="u-ipttext" placeholder="@if($i == 1)必填@else非必填@endif"  name="stock[]"  value="">
                            @if($count != $i)<br><br>@endif
                        @endfor
                    @endif
                </yz:fitem>
                <yz:fitem name="status" label="库存状态">
                    <yz:radio name="status" options="0,1" texts="不可用,可用" checked="$data['status']"></yz:radio>
                </yz:fitem>
        </yz:form>
    @yizan_end
@stop