<ol class="sortable-list">
    @foreach ($tree as $department)
        <li data-id="{{ $department->id }}">
            <div class="tree-node d-flex justify-content-between align-items-center">
                <div>
                    <span class="tree-handle mr-2"><i class="fas fa-arrows-alt"></i></span>
                    <strong>{{ $department->name }}</strong>
                    <small class="text-muted ml-2">({{ $department->code }})</small>
                </div>
                <span class="badge badge-{{ $department->is_active ? 'success' : 'danger' }} badge-pill">
                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @if ($department->children->count() > 0)
                @include('staff.department.partials.hierarchy-tree', ['tree' => $department->children])
            @endif
        </li>
    @endforeach
</ol>

<style>
    .sortable-list {
        list-style: none;
        padding-left: 0;
    }

    .sortable-list ol {
        list-style: none;
        padding-left: 25px;
        margin-top: 5px;
    }

    .sortable-list li {
        margin: 5px 0;
        padding: 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: move;
    }

    .tree-node {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .tree-handle {
        cursor: move;
        color: #6c757d;
    }
</style>
