@extends('layouts.admin')

@section('title', $modelName)

@permission($permissionCreateName)
@section('new_button')
<span class="new-button"><a href="{{ url($newUrl) }}" class="btn btn-success btn-sm"><span class="fa fa-plus"></span>
        &nbsp;{{ trans('general.add_new') }}</a></span>
@endsection
@endpermission

@section('content')
<!-- Default box -->
<div class="box box-success">
    <div class="box-header with-border">
        {!! Form::open(['url' => $saveUrl, 'role' => 'form', 'method' => 'GET']) !!}
        <div id="items" class="pull-left box-filter">
            <span class="title-filter hidden-xs">{{ trans('general.search') }}:</span>
            {!! Form::text('search', request('search'), ['class' => 'form-control input-filter input-sm', 'placeholder' => trans('general.search_placeholder')]) !!}
            {!! Form::button('<span class="fa fa-filter"></span> &nbsp;' . trans('general.filter'), ['type' => 'submit', 'class' => 'btn btn-sm btn-default btn-filter']) !!}
        </div>
        <div class="pull-right">
            <span class="title-filter hidden-xs">{{ trans('general.show') }}:</span>
            {!! Form::select('limit', $limits, request('limit', setting('general.list_limit', '25')), ['class' => 'form-control input-filter input-sm', 'onchange' => 'this.form.submit()']) !!}
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="table table-responsive">
            <table class="table table-striped table-hover" id="tbl-categories">
                <thead>
                    <tr>
                        <th class="col-md-5">@sortablelink('name', trans('general.name'))</th>
                        <th class="col-md-1 hidden-xs">@sortablelink('enabled', trans_choice('general.statuses', 1))</th>
                        <th class="col-md-1 text-center">{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($models as $item)
                    <tr>
                        <td>
                            <a href="{{ route($editUrl, $item->id) }}">{{ $item->name }}</a>
                        </td>
                        <td class="hidden-xs">
                            @if ($item->enabled)
                                <span class="label label-success">{{ trans('general.enabled') }}</span>
                            @else
                                <span class="label label-danger">{{ trans('general.disabled') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-toggle-position="left" aria-expanded="false">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
	                                    <a href="{{ route($editUrl, $item->id) }}">{{
                                    trans
                                    ('general.edit') }}</a>
                                    </li>
                                    <li>
	                                    <a href="{{ route($showUrl, $item->id) }}">{{
                                    trans
                                    ('general.show') }}</a>
                                    </li>
                                    @if ($item->enabled)
                                    <li>
	                                    <a href="{{ route($disableUrl, $item->id) }}">{{ trans
                                    ('general.disable') }}</a>
                                    </li>
                                    @else
                                    <li>
	                                    <a href="{{ route($enableUrl, $item->id) }}">{{ trans('general.enable')
                                    }}</a>
                                    </li>
                                    @endif
                                    @permission($permissionDeleteName)
                                    <li class="divider"></li>
                                    <li>{!! Form::deleteLink($item, route($destroyUrl, $item->id)) !!}</li>
                                    @endpermission
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
        @include('partials.admin.pagination', ['items' => $models, 'type' => 'records'])
    </div>
    <!-- /.box-footer -->
</div>
<!-- /.box -->
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function(){
    
    });
</script>
@endpush
