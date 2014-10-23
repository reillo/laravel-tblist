
{{ $list->getTableData() }}

<div class="row">
    <div class="col-sm-4 center-sm">
        {{ $list->getPaginationInfo() }}
        {{ $list->getPerPageLimit() }}
    </div>
    <div class="col-sm-8 center-sm">
        {{ $list->getPagination() }}
    </div>
</div>