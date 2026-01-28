<ul class="department-tree">
    @foreach ($departments as $department)
        <li class="mb-2">
            <div class="tree-node">
                <span class="tree-handle"><i class="fas fa-arrows-alt"></i></span>
                <strong>{{ $department->name }}</strong>
                <small class="text-muted ml-2">({{ $department->code }})</small>
                <span class="badge badge-{{ $department->is_active ? 'success' : 'danger' }} badge-pill ml-2">
                    {{ $department->is_active ? 'A' : 'I' }}
                </span>
            </div>
            @if ($department->children->count() > 0)
                @include('admin.department.partials.tree', ['departments' => $department->children])
            @endif
        </li>
    @endforeach
</ul>
