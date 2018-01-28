
<div class="box-body chart-container chart-option">

    <form action="" method="post" class="form-horizontal form-bordered form-edit-column">
        {{csrf_field()}}
        <input type="hidden" readonly name="operate" value="create">
        <input type="hidden" readonly name="container" value="column">
        <input type="hidden" readonly name="table_id" value="{{$table_encode or encode(0)}}">
        <input type="hidden" readonly name="id" value="{{encode(0)}}">

        <div class="form-group">
            <label class="col-md-8 col-md-offset-2 question-title">添加图</label>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">选择图类型</label>
            <div class="col-md-8">
                <select class="form-control chart-type" name="type" onchange="select_chart_type()">
                    <option value="1">折线图</option>
                    <option value="2">柱状图</option>
                    <option value="3">饼图</option>
                    <option value="4">雷达图</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">后台名称</label>
            <div class="col-md-8">
                <div><input type="text" class="form-control" name="name" placeholder="请输入后台名称"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">标题</label>
            <div class="col-md-8">
                <div><input type="text" class="form-control" name="title" placeholder="请输入标题"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">备注</label>
            <div class="col-md-8">
                <div><input type="text" class="form-control" name="description" placeholder="请输入备注"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">选择列作为图标题</label>
            <div class="col-md-8">
                <select class="form-control" name="format_title[value]">
                    @foreach($data->columns as $column)
                        <option value="{{$column->id}}">{{$column->title}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="format_title[id]" value="0">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-md-2">选择数据列</label>
            <div class="col-md-8">
                @foreach($data->columns as $column)
                <div class="form-group chart-data-option">
                    <div class="col-md-9">
                        <input type="hidden" name="formats[{{$column->id or ''}}][id]" value="0">
                        <input type="checkbox" name="datas[{{$column->id or ''}}][value]" placeholder="" value="{{$column->id or ''}}" class="select-this-chart-data"> {{$column->title or ''}}
                    </div>
                    <div class="col-md-9 chart-max" style="display:none;">
                        参考值 <input type="text" name="maxs[{{$column->id or ''}}][value]" placeholder="参考最大值">
                    </div>
                </div>
                @endforeach 
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-8 col-md-offset-2">
                <button type="button" class="btn btn-sm btn-primary add-this-chart">保存</button>
                <button type="button" class="btn btn-sm cansel-this-modal">取消</button>
            </div>
        </div>
    </form>

</div>
