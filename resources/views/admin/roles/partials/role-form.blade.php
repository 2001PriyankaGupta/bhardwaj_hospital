<!-- resources/views/admin/roles/partials/role-form.blade.php -->
<form id="roleForm" method="POST">
    @csrf
    <input type="hidden" name="_method" id="roleFormMethod" value="">
    <!-- Method spoofing will be handled by JS -->

    <div class="mb-3">
        <label for="name" class="form-label">Role Name *</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name ?? '') }}"
            required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $role->description ?? '') }}</textarea>
    </div>

    <h5 class="mt-4 mb-3">Permissions</h5>

    @foreach ($permissions as $module => $modulePermissions)
        <div class="card mb-2">
            <div class="card-header py-2">
                <h6 class="m-0">{{ $module }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($modulePermissions as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]"
                                    value="{{ $permission->id }}" id="perm_{{ $permission->id }}">
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Save Role</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
