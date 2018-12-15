@extends('admin._layouts.base')
@section('css')
@stop
@section('right_content')
    @yizan_begin
    <yz:list>
        <search url="index">
            <row>
                <linkbtn label="全部">
                    <attrs>
                        <url>{{ u('SendDataInfo/index') }}</url>
                    </attrs>   
                </linkbtn>
                <linkbtn label="今天">
                    <attrs>
                        <url>{{ u('SendDataInfo/index', ['time'=>1]) }}</url>
                    </attrs>   
                </linkbtn>
                <linkbtn label="7天">
                    <attrs>
                        <url>{{ u('SendDataInfo/index', ['time'=>7]) }}</url>
                    </attrs>   
                </linkbtn>
                <linkbtn label="30天">
                    <attrs>
                        <url>{{ u('SendDataInfo/index', ['time'=>30]) }}</url>
                    </attrs>   
                </linkbtn>
                <item name="beginTime" label="开始时间" type="date"></item>
                <item name="endTime" label="结束时间" type="date"></item>
                <item name="cityName" label="城市"></item>
                <btn type="search"></btn>
                <linkbtn label="导出到EXCEL" type="export" url="{{ u('SendDataInfo/export', $search_args) }}"></linkbtn>
            </row>
        </search>
        <php>
            $total_num = 0;
            $finish_num = 0;
            $abnormal_num = 0;
            $total_send_fee = 0;
            foreach($list as $v){
                $total_num += $v['total_num'];
                $finish_num += $v['finish_num'];
                $abnormal_num += $v['abnormal_num'];
                $total_send_fee += $v['total_send_fee'];
            }
        </php>
        <table>
            <thead>
                <tr>
                  <td rowspan="1">城市</td>
                  <td rowspan="1">订单总量</td>
                  <td rowspan="1">完成总量</td>
                  <td colspan="1">异常订单</td> 
                  <td colspan="1">服务费合计</td>
                </tr>
                <tr>   
                  <td>合计</td>
                  <td>{{$total_num}}</td>
                  <td>{{$finish_num}}</td>
                  <td>{{$abnormal_num}}</td>
                  <td>{{$total_send_fee}}</td>
                </tr>
            </thead>
            <tbody> 
                @foreach ($list as $l)
                <tr>
                  <td>{{$l['name']}}</td>
                  <td>{{$l['total_num']}}</td>
                  <td>{{$l['finish_num']}}</td>
                  <td>{{$l['abnormal_num']}}</td>
                  <td>{{$l['total_send_fee']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </yz:list>
    @yizan_end
@stop

@section('js')
<script type="text/javascript">
  $(function(){
    $('#yzForm').submit(function(){
      var beginTime = $("#beginTime").val();
      var endTime = $("#endTime").val();
      if(beginTime!='' || endTime!='') {
        if(beginTime==''){
          alert("开始时间不能为空");return false;
        }
        else if(endTime==''){
          alert("结束时间不能为空");return false;
        }
        else if(endTime < beginTime){
          alert("开始时间不能大于结束时间");return false;
        }
      }
    });
  })
</script>
@stop