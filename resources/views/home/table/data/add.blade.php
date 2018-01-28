
<div class="box-body column-container column-option">

    <form action="" method="post" class="form-horizontal form-bordered form-edit-column">
        {{csrf_field()}}
        <input type="hidden" readonly name="operate" value="create">
        <input type="hidden" readonly name="container" value="column">
        <input type="hidden" readonly name="table_id" value="{{$table_encode or encode(0)}}">
        <input type="hidden" readonly name="id" value="{{encode(0)}}">
        <input type="hidden" readonly name="type" value="1" class="column-type">

        <div class="form-group">
            <label class="col-md-8 col-md-offset-2 question-title">添加数据</label>
        </div>

        @foreach($data->columns as $column)
        <div class="form-group">
            <label class="control-label col-md-2">{{$column->title}}</label>
            <div class="col-md-8">
                <input type="hidden" name="column[{{$column->id}}][column_id]" value="{{$column->id}}">
                <input type="hidden" name="column[{{$column->id}}][content_id]" value="0">
                <input type="text" class="form-control" name="column[{{$column->id}}][value]" placeholder="" value="{{$column->default or ''}}">
            </div>
        </div>
        @endforeach

        <div class="form-group">
            <div class="col-md-8 col-md-offset-2">
                <button type="button" class="btn btn-sm btn-primary add-this-data">保存</button>
                <button type="button" class="btn btn-sm cansel-this-modal">取消</button>
            </div>
        </div>
    </form>

</div>
