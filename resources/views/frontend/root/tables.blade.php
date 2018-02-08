@extends('frontend.layout.layout')

@section('title','表格 - 图表站')
@section('header','图表站')
@section('description','表格')
@section('breadcrumb')
    <li><a href="{{url('/')}}"><i class="fa fa-home"></i>首页</a></li>
    <li><a href="#"><i class="fa "></i>Here</a></li>
@endsection


@section('content')
<div style="display:none;">
    <input type="hidden" id="table_id" value="{{$table_encode or ''}}" readonly>
</div>

{{--表格--}}
@foreach($datas as $data)
<div class="row" style="margin-top:24px;">
    <div class="col-md-12">
        <!-- BEGIN PORTLET-->
        <div class="box
            @if($loop->index % 7 == 0) box-info
            @elseif($loop->index % 7 == 1) box-danger
            @elseif($loop->index % 7 == 2) box-success
            @elseif($loop->index % 7 == 3) box-default
            @elseif($loop->index % 7 == 4) box-orange
            @elseif($loop->index % 7 == 5) box-primary
            @elseif($loop->index % 7 == 6) box-navy
            @endif
        ">

            <div class="box-header with-border" style="margin:16px 0 8px;">
                <h3 class="box-title">{{ $data->title or '' }}</h3>
                <small>（ 来自 <a href="{{url('/u/'.$data->user->id)}}" target="_blank">{{$data->user->name}}</a> ）</small>
            </div>

            @if(!empty($data->description))
            <div class="box-body">
                <div class="colo-md-12 text-muted"> {{ $data->description or '' }} </div>
            </div>
            @endif

            @if(!empty($data->content))
                <div class="box-body">
                    <div class="colo-md-12"> {{ $data->content or '' }} </div>
                </div>
            @endif

            <div class="box-body" id="content-main-body">
                <!-- datatable start -->
                <table class='table table-striped table-bordered' id='datatable_ajax'>
                    <thead>
                    <tr role='row' class='heading'>
                        @foreach($data->columns as $column)
                            <th>{{$column->title}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data->rows as $row)
                    <tr class="row-option" data-id="{{encode($row->id)}}">
                        @foreach($row->datas as $content)
                            <td>{{$content->content or ''}}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- datatable end -->
            </div>

            <div class="box-footer">
                &nbsp;
            </div>

        </div>
        <!-- END PORTLET-->
    </div>
</div>
@endforeach

@endsection



@section('js')
<script>
    var TableDatatablesAjax = function () {
        var datatableAjax = function () {

            var dt = $('#datatable_ajax');
            var ajax_datatable = dt.DataTable({
                "aLengthMenu": [[20, 50, 200, 500, -1], ["20", "50", "200", "500", "全部"]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    'url': '/home/table/data/list',
                    "type": 'POST',
                    "dataType" : 'json',
                    "data": function (d) {
                        d._token = $('meta[name="_token"]').attr('content');
                    },
                },
                "pagingType": "simple_numbers",
                "order": [],
                "orderCellsTop": true,
                "columns": [
                    {
                        "data": "encode_id",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            return '<a target="_blank" href="/table?id='+data+'">'+row.name+'</a>';
                        }
                    },
                    {
                        'data': 'title',
                        'orderable': false,
                        render: function(val) {
                            return val == null ? '' : val;
                        }
                    },
                    {
                        'data': 'type',
                        'orderable': true,
                        render: function(type) {
                            return type == null ? '' : type;
                        }
                    },
                    {
                        'data': 'menu_id',
                        'orderable': true,
                        render: function(data, type, row, meta) {
                            return row.menu == null ? '未分类' : row.menu.name;
                        }
                    },
                    {
                        "data": "id",
                        'orderable': false,
                        render: function(data, type, row, meta) {
                            return row.admin == null ? '未知' : row.admin.nickname;
                        }
                    },
                    {
                        'data': 'visit_num',
                        'orderable': false,
                        render: function(val) {
                            return val == null ? 0 : val;
                        }
                    },
                    {
                        'data': 'active',
                        'orderable': false,
                        render: function(val) {
                            if(val == 0) return '<small class="label bg-teal">未启用</small>';
                            else if(val == 1) return '<small class="label bg-green">启</small>';
                            else return '<small class="label bg-red">禁</small>';
                        }
                    },
                    {
                        'data': 'created_at',
                        'orderable': true,
                        render: function(data) {
                            newDate = new Date();
                            newDate.setTime(data * 1000);
                            return newDate.toLocaleString('chinese',{hour12:false});
                        }
                    },
                    {
                        'data': 'updated_at',
                        'orderable': true,
                        render: function(data) {
                            newDate = new Date();
                            newDate.setTime(data * 1000);
                            return newDate.toLocaleString('chinese',{hour12:false});
                        }
                    },
                    {
                        'data': 'encode_id',
                        'orderable': false,
                        render: function(value) {
                            var html =
                                    '<div class="btn-group">'+
                                    '<button type="button" class="btn btn-sm btn-primary">操作</button>'+
                                    '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">'+
                                    '<span class="caret"></span>'+
                                    '<span class="sr-only">Toggle Dropdown</span>'+
                                    '</button>'+
                                    '<ul class="dropdown-menu" role="menu">'+
                                    '<li><a href="/home/table/edit?id='+value+'">编辑</a></li>'+
                                    '<li><a href="/home/table/data?id='+value+'">数据管理</a></li>'+
                                    '<li><a class="article-delete-submit" data-id="'+value+'" >删除</a></li>'+
                                    '<li><a class="article-enable-submit" data-id="'+value+'">启用</a></li>'+
                                    '<li><a class="article-disable-submit" data-id="'+value+'">禁用</a></li>'+
                                    '<li><a href="/admin/statistics/page?module=2&id='+value+'">流量统计</a></li>'+
                                    '<li><a class="download-qrcode" data-id="'+value+'">下载二维码</a></li>'+
                                    '<li class="divider"></li>'+
                                    '<li><a href="#">Separated link</a></li>'+
                                    '</ul>'+
                                    '</div>';
                            return html;
                        }
                    }
                ],
                "drawCallback": function (settings) {
                    ajax_datatable.$('.tooltips').tooltip({placement: 'top', html: true});
                    $("a.verify").click(function(event){
                        event.preventDefault();
                        var node = $(this);
                        var tr = node.closest('tr');
                        var nickname = tr.find('span.nickname').text();
                        var cert_name = tr.find('span.certificate_type_name').text();
                        var action = node.attr('data-action');
                        var certificate_id = node.attr('data-id');
                        var action_name = node.text();

                        var tpl = "{{trans('labels.crc.verify_user_certificate_tpl')}}";
                        layer.open({
                            'title': '警告',
                            content: tpl
                                    .replace('@action_name', action_name)
                                    .replace('@nickname', nickname)
                                    .replace('@certificate_type_name', cert_name),
                            btn: ['Yes', 'No'],
                            yes: function(index) {
                                layer.close(index);
                                $.post(
                                        '/admin/medsci/certificate/user/verify',
                                        {
                                            action: action,
                                            id: certificate_id,
                                            _token: '{{csrf_token()}}'
                                        },
                                        function(json){
                                            if(json['response_code'] == 'success') {
                                                layer.msg('操作成功!', {time: 3500});
                                                ajax_datatable.ajax.reload();
                                            } else {
                                                layer.alert(json['response_data'], {time: 10000});
                                            }
                                        }, 'json');
                            }
                        });
                    });
                },
                "language": { url: '/admin/i18n' },
            });


            dt.on('click', '.filter-submit', function () {
                ajax_datatable.ajax.reload();
            });

            dt.on('click', '.filter-cancel', function () {
                $('textarea.form-filter, select.form-filter, input.form-filter', dt).each(function () {
                    $(this).val("");
                });

                $('select.form-filter').selectpicker('refresh');

                ajax_datatable.ajax.reload();
            });

        };
        return {
            init: datatableAjax
        }
    }();
    $(function () {
//        TableDatatablesAjax.init();
    });
</script>
<script src="https://cdn.bootcss.com/echarts/3.7.2/echarts.min.js"></script>
<script>
    $(function() {

        // 显示添加数据
        $(".show-add-data").on('click', function() {

            $.post(
                "/home/table/data/get/add",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 显示编辑数据
        $(".show-edit-data").on('click', function() {

            $.post(
                "/home/table/data/get/edit",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val(),
                    row_id:$(this).attr('data-id')
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 添加数据
        $("#edit-modal").on('click', '.add-this-data', function() {
            var options = {
                url: "/home/table/data/edit",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.reload();
                    }
                }
            };
            $("#edit-ctn .form-edit-column").ajaxSubmit(options);
        });

        // 显示创建图
        $(".show-add-chart").on('click', function() {

            $.post(
                "/home/table/chart/get/add",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    table_id:$('#table_id').val()
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 显示编辑数据
        $(".show-edit-chart").on('click', function() {

            $.post(
                "/home/table/chart/get/edit",
                {
                    _token: $('meta[name="_token"]').attr('content'),
                    chart_id:$(this).attr("data-id")
                },
                function(data){
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        var html = data.data.html;
                        $('#edit-ctn').html(html);
                        $('#edit-modal').modal('show');
                    }
                },
                'json'
            );

        });

        // 添加图
        $("#edit-modal").on('click', '.add-this-chart', function() {
            var options = {
                url: "/home/table/chart/edit",
                type: "post",
                dataType: "json",
                // target: "#div2",
                success: function (data) {
                    if(!data.success) layer.msg(data.msg);
                    else
                    {
                        layer.msg(data.msg);
                        location.reload();
                    }
                }
            };
            $("#edit-ctn .form-edit-column").ajaxSubmit(options);
        });

        // 取消添加or编辑
        $("#edit-modal").on('click', '.select-this-chart-data', function () {
            if($(".chart-type").val() == 4)
            {
                var chart_max = $(this).parents(".chart-data-option").find(".chart-max");
                if($(this).is(':checked')) chart_max.show();
                else chart_max.hide();
            }
        });

        // 取消添加or编辑
        $("#edit-modal").on('click', '.cansel-this-modal', function () {
            $('#edit-ctn').html('');
            $('#edit-modal').modal('hide');
        });

        // 显示编辑数据
        $(".delete-this-row").on('click', function() {
            var that = $(this);
            layer.msg('确定要删除该"记录"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "/home/table/data/delete",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            table_id:$('#table_id').val(),
                            row_id:that.parents('.row-option').attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg(data.msg);
                                location.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });

        // 显示编辑数据
        $(".delete-this-chart").on('click', function() {
            var that = $(this);
            layer.msg('确定要删除该"图"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                        "/home/table/chart/delete",
                        {
                            _token: $('meta[name="_token"]').attr('content'),
                            table_id:$('#table_id').val(),
                            chart_id:that.parents('.chart-option').attr('data-id')
                        },
                        function(data){
                            if(!data.success) layer.msg(data.msg);
                            else
                            {
                                layer.msg(data.msg);
                                location.reload();
                            }
                        },
                        'json'
                    );
                }
            });
        });





        // 【删除】 文章
        $("#article-main-body").on('click', ".article-delete-submit", function() {
            var that = $(this);
            layer.msg('确定要删除该"文章"么', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/home/table/delete",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
                            },
                            function(data){
                                if(!data.success) layer.msg(data.msg);
                                else location.reload();
                            },
                            'json'
                    );
                }
            });
        });

        // 【启用】 文章
        $("#article-main-body").on('click', ".article-enable-submit", function() {
            var that = $(this);
            layer.msg('确定启用该"文章"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/admin/article/enable",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
                            },
                            function(data){
                                if(!data.success) layer.msg(data.msg);
                                else location.reload();
                            },
                            'json'
                    );
                }
            });
        });

        // 【禁用】 文章
        $("#article-main-body").on('click', ".article-disable-submit", function() {
            var that = $(this);
            layer.msg('确定禁用该"文章"？', {
                time: 0
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    $.post(
                            "/admin/article/disable",
                            {
                                _token: $('meta[name="_token"]').attr('content'),
                                id:that.attr('data-id')
                            },
                            function(data){
                                if(!data.success) layer.msg(data.msg);
                                else location.reload();
                            },
                            'json'
                    );
                }
            });
        });

        // 【下载】 二维码
        $("#article-main-body").on('click', ".download-qrcode", function() {
            var that = $(this);
            window.open('/admin/download_qrcode?sort=article&id='+that.attr('data-id'));
        });

    });


    function select_chart_type()
    {
        var chart = $('#edit-modal .chart-container');
        var vs = chart.find('select option:selected').val();
        if(vs == 4)
        {
            var checkboxs = chart.find('input[type=checkbox]');
            checkboxs.each(function() {
                if($(this).is(':checked'))
                {
                    $(this).parents(".chart-data-option").find('.chart-max').show();
                }
            });
        }
        else chart.find(".chart-max").hide();
    }


</script>
@endsection
