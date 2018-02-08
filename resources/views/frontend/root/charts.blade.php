@extends('frontend.layout.layout')

@section('title','图')
@section('header','图')
@section('description','图')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div style="display:none;">
    <input type="hidden" id="chart_id" value="{{$chart_encode or ''}}" readonly>
    <input type="hidden" id="table_id" value="{{$table_encode or ''}}" readonly>
</div>

{{--图--}}
@foreach($charts as $num => $chart)
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box box-info">

            <div class="box-header with-border" style="margin:16px 0;">
                <h3 class="box-title">{{$chart->title}}</h3>
                <small> （来自 <a href="{{url('/u/'.$chart->user->id)}}" target="_blank">{{$chart->user->name}}</a> 的表：{{$chart->table->title}} ）</small>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        @if($chart->type == 3)
                            @foreach($chart->chart_datas->rows as $k => $v)
                                <div id="echart-container-{{$num}}-{{$k}}" style="width:100%;height:320px;margin-bottom:48px;"></div>
                            @endforeach
                        @else
                            <div id="echart-container-{{$num}}" style="width:100%;height:500px;"></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-footer">
            </div>

        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endforeach

@endsection



@section('js')
<script src="https://cdn.bootcss.com/echarts/3.7.2/echarts.min.js"></script>
<script>
    $(function() {

        @foreach($charts as $num => $chart)
        @if($chart->type == 1 || $chart->type == 2)
            var option = {
//                tooltip: {
//                    trigger: 'axis',
//                    axisPointer: {
//                        type: 'cross',
//                        crossStyle: {
//                            color: '#999'
//                        }
//                    }
//                },
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar', 'tiled', 'stack']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:[
                        @foreach($chart->chart_datas->columns as $v)
                            @if (!$loop->last)  '{{$v->column->title}}',
                            @else '{{$v->column->title}}'
                            @endif
                        @endforeach
                    ]
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($chart->chart_datas->rows as $v)
                                    @if (!$loop->last) '{{$v->row_title}}', @else '{{$v->row_title}}' @endif
                            @endforeach
                        ]
                    }
                ],
                yAxis: [
                    {
                        type : 'value'
                    }
                ],
                series: [
                    @foreach($chart->chart_datas->columns as $k => $v)
                        @if (!$loop->last)
                        {
                            name : '{{$v->column->title}}',
                            type: @if($chart->type == 1) 'line' @elseif($chart->type == 2) 'bar' @endif ,
                            label: 'labelOption',
                            data : [
                                @foreach($chart->chart_datas->rows as $val)
                                @if (!$loop->last) {{$val->datas[$k]->content}},
                                @else {{$val->datas[$k]->content}}
                                @endif
                                @endforeach
                            ]
                        },
                        @else
                        {
                            name : '{{$v->column->title}}',
                            type: @if($chart->type == 1) 'line' @elseif($chart->type == 2) 'bar' @endif ,
                            label: 'labelOption',
                            data : [
                                @foreach($chart->chart_datas->rows as $val)
                                @if (!$loop->last) {{$val->datas[$k]->content}},
                                @else {{$val->datas[$k]->content}}
                                @endif
                                @endforeach
                            ]
                        }
                        @endif
                    @endforeach

                ]
            };
            var myChart = echarts.init(document.getElementById('echart-container-{{$num}}'));
            myChart.setOption(option);
        @elseif($chart->type == 3)
            var option;
            var myChart;
            @foreach($chart->chart_datas->rows as $k => $v)
            option = {
                title : {
                    text: '{{$v->row_title}}',
                    subtext: '纯属虚构',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: [
                        @foreach($chart->chart_datas->columns as $va)
                            @if (!$loop->last) '{{$va->column->title}}',
                            @else '{{$va->column->title}}'
                            @endif
                        @endforeach
                    ]
                },
                series : [
                    {
                        name: '{{$v->row_title}}',
                        type: 'pie',
                        radius : '50%',
                        center: ['50%', '55%'],
                        data:[
                            @foreach($v->datas as $key => $val)
                                @if (!$loop->last)
                                { value: '{{$val->content}}', name: '{{$chart->chart_datas->columns[$key]->column->title}}' },
                                @else
                                { value: '{{$val->content}}', name: '{{$chart->chart_datas->columns[$key]->column->title}}' }
                                @endif
                            @endforeach
                        ]
                    }
                ]
            };
            myChart = echarts.init(document.getElementById('echart-container-{{$num}}-{{$k}}'));
            myChart.setOption(option);
            @endforeach
        @elseif($chart->type == 4)
            var option = {
                title: {
                    text: '雷达图'
                },
                tooltip: {},
                legend: {
                    data: [
                        @foreach($chart->chart_datas->rows as $v)
                                @if (!$loop->last) '{{$v->row_title}}', @else '{{$v->row_title}}' @endif
                        @endforeach
                    ]
                },
                toolbox: {
                    feature: {
                        dataView: {show: true, readOnly: false},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                radar: {
                    // shape: 'circle',
                    name: {
                        textStyle: {
                            color: '#fff',
                            backgroundColor: '#999',
                            borderRadius: 3,
                            padding: [3, 5]
                        }
                    },
                    indicator: [
                        @foreach($chart->chart_datas->columns as $v)
                            @if (!$loop->last) { name: '{{$v->column->title}}', max: '{{$v->max}}' },
                            @else { name: '{{$v->column->title}}', max: '{{$v->max}}' }
                            @endif
                        @endforeach
                    ]
                },
                series: [{
                    name: '{{$chart->title or ""}}',
                    type: 'radar',
                    areaStyle: {
                        normal: {
                            opacity: 0.4
                        }
                    },
                    data : [
                            @foreach($chart->chart_datas->rows as $v)
                            @if (!$loop->last)
                        {
                            value : [
                                @foreach($v->datas as $val)
                                @if (!$loop->last) {{$val->content}},
                                @else {{$val->content}}
                                @endif
                                @endforeach
                            ],
                            name : '{{$v->row_title}}'
                        },
                            @else
                        {
                            value : [
                                @foreach($v->datas as $val)
                                @if (!$loop->last) {{$val->content}},
                                @else {{$val->content}}
                                @endif
                                @endforeach
                            ],
                            name : '{{$v->row_title}}'
                        }
                        @endif
                        @endforeach
                    ]
                }]
            };
            var myChart = echarts.init(document.getElementById('echart-container-{{$num}}'));
            myChart.setOption(option);
        @endif
        @endforeach

    });

</script>
@endsection
