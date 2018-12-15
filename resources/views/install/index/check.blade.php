@extends('install._layouts.base')
@section('images')
    <img src="{{ asset('install/images/img1.png') }}">
@stop
@section('right_content')
    <div class="y-maintop">
        <h4 class="f14">环境检查</h4>
        <form>
            <table>
                <tr>
                    <th>项目</th>
                    <th>最低配置</th>
                    <th>最佳配置</th>
                    <th class="y-w180">当前服务器</th>
                </tr>
                @foreach($result['systems'] as $system)
                    <tr class="system">
                        <td>{{ $system['name'] }}</td>
                        <td>{{ $system['minask'] }}</td>
                        <td>{{ $system['maxask'] }}</td>
                        <td class="@if($system['status'] == 1) y-iconok @else y-iconbad @endif">{{ $system['msg'] }}</td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
    <div class="y-mainbtm">
        <h4 class="f14">目录、文件权限检查</h4>
        <form>
            <table>
                <tr>
                    <th>项目</th>
                    <th></th>
                    <th>所需状态</th>
                    <th class="y-w180">当前状态</th>
                </tr>
                @foreach($result['files'] as $k =>$file)
                    <tr>
                        <td colspan="2">{{ $file['name'] }}</td>
                        <td>{{ $file['ask'] }}</td>
                        <td class="@if($file['status'] == 1) y-iconok @else y-iconbad @endif">{{ $file['msg'] }}</td>
                    </tr>
                @endforeach
            </table>
        </form>
    </div>
    <p class="mt20 tc">
        <a href="{{ u('Index/index') }}" class="btn btn2 mr15">上一步</a>
        <a href="{{ u('Index/database') }}" class="btn nextbnt">下一步</a>
    </p>
    <script type="text/javascript" >
        $(function(){
            $("td").each(function(){
                if($(this).hasClass("y-iconbad")){
                    $(".nextbnt").addClass("none");
                }
            });
        })
    </script>
@stop
