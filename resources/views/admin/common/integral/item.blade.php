@foreach($list as $list_item)
    <tr class="tr-{{$list_item['id']}} tr-even" key="{{$list_item['id']}}" primary="id" data-page={{$page}}>
        <td style="width:20px; text-align:center;" class="show_bnt_input">
            @if($ajax == true)
                <div class="checker">
                    <span class="show_checker_{{$list_item['id']}}" >
                        <input type="checkbox" name="key" value="{{$list_item['id']}}"  />
                    </span>
                </div>
            @else
                <input type="checkbox" name="key" value="{{$list_item['id']}}"  />
            @endif
        </td>
        <td class="" style="text-align:left;" code="name">
            <p>编号：{{$list_item['id']}}</p>
            <p class="name_show">名称：{{$list_item['name']}}</p>
            <p>分类：{{$list_item['cate']['name']}}</p>
        </td>
        <td class="" code="name"><p class="dx">可兑换积分</p>
            <input type="text" maxlength="7" value="{{$list_item['exchangeIntegral'] == 0 ? "" : $list_item['exchangeIntegral']}}" name="integral" data-key="{{$list_item['id']}}" class="u-ipttext show_integral_{{$list_item['id']}}" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" />
        </td>
    </tr>
@endforeach
<input type="hidden" name="prec" value="{{$page - 1}}"/>
<input type="hidden" name="next" value="{{$page + 1}}"/>
<input type="hidden" name="page" value="{{$page}}"/>
