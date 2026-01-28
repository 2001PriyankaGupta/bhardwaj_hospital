{{-- resources/views/admin/roles/partials/role-list.blade.php --}}
<div class="table-responsive">
    <table class="table table-centered table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td><span class="badge bg-light text-dark">{{ $role->slug }}</span></td>
                    <td>{{ $role->description }}</td>
                    <td>
                        @foreach ($role->permissions->take(3) as $permission)
                            <span class="badge bg-info">{{ $permission->name }}</span>
                        @endforeach
                        @if ($role->permissions->count() > 3)
                            <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} more</span>
                        @endif
                    </td>
                    <td>
                        @if (!in_array($role->slug, ['admin']))
                            <button type="button" class="btn btn-sm btn-primary"
                                onclick="editRole('{{ $role->id }}', '{{ $role->name }}', '{{ $role->description }}')">
                                Edit
                            </button>
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this role?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        @else
                            <span class="badge bg-warning">System Role</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
