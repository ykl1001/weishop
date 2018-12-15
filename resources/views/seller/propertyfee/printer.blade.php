<table border='0' id="yjiuShow">
    <!--rowspan colspan -->
    <tr class="tc">
        <td colspan="6" class="f16 spacing"><h2>{{ $seller['name'] }}管理有限公司</h2></td>
    </tr>
    <tr class="tc">
        <td colspan="6" class="f16  spacing"><h3>收&nbsp;款&nbsp;收&nbsp;据</h3></td>
    </tr>
    <tr class="pdyj p10 td">
        <td colspan="4"><h3>开票日期：{{$time}}</h3></td>
        <td colspan="2"><h3>单据号：{{$data['id']}}</h3></td>
    </tr>
    <tr class="pdyj p10 td">
        <td colspan="2"><h3>缴费单位（个人）：{{$data['room']['owner']}}</h3></td>
        <td colspan="2"><h3>房间号：{{$data['room']['roomNum']}}</h3></td>
        <td><h3>面积：{{$data['room']['structureArea']}}</h3></td>
        <td rowspan="2" class="tc"><h3>备注</h3></td>
    </tr>
    <tr class="tc td">
        <td width="100px"><h3>项目名称</h3></td>
        <td><h3>费用期间</h3></td>
        <td><h3>数量</h3></td>
        <td><h3>单价</h3></td>
        <td><h3>金额</h3></td>
    </tr>
    <tr class="tc wd20 td">
        <td>{{$data['roomfee']['payitem']['name']}}</td>
        <td>{{yztime($data['beginTime'], 'Y-m-d')}}/{{yztime($data['endTime'], 'Y-m-d')}}</td>
        <td>1</td>
        <td>{{$data['fee']}}</td>
        <td width="80px">{{$data['fee']}}</td>
        <td rowspan="4" class="tc f12 lh12 pr5" width="100px">
            {{ $data['roomfee']['remark'] }}
        </td>
    </tr>
    <tr class="tc wd20 td">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr class="tc wd20 td">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr class="pdyj p10 td">
        <td colspan="4"><h3>合计（大写）：{{$money}}</h3></td>
        <td colspan="1"><h3>￥：{{$data['fee'] or 0}}</h3></td>
    </tr>
    <tr class="pdyj p10">
        <td colspan="6">
            <h3>
                开票人：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                收费员：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                付款人：
            </h3>
        </td>
    </tr>
    <tr class="pdyj p10">
        <td colspan="6">
            <h3>
                白：存根&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                粉：客户&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                黄：财务
            </h3>
        </td>
    </tr>
</table>