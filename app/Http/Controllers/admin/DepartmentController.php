<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
         $user = Auth::user();
        $departments = Department::with(['parent', 'head'])
            ->orderBy('display_order')
            ->get();
            
        $tree = Department::getTree();
        
        return view($user->user_type.'.department.index', compact('departments', 'tree'));
    }

    public function create()
    {
         $user = Auth::user();
        $departments = Department::active()->get();
        $users = User::where('status','active')->get();
        
        return view($user->user_type.'.department.create', compact('departments', 'users'));
    }

    

    public function store(Request $request)
    {
         $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'display_order' => 'nullable|integer',
            'services' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $department = Department::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'head_id' => $request->head_id,
                'email' => $request->email,
                'phone' => $request->phone,
                'display_order' => $request->display_order ?? 0,
                'services' => $request->services,
            ]);
        });

        return redirect()->route($user->user_type.'.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $user = Auth::user();
        $department->load(['parent', 'head', 'children', 'users']);
        return view($user->user_type.'.department.show', compact('department'));
    }

    public function edit(Department $department)
    {
         $user = Auth::user(); 
        $departments = Department::where('id', '!=', $department->id)->active()->get();
        $users = User::where('status', 'active')->get();
        
        return view($user->user_type.'.department.edit', compact('department', 'departments', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'display_order' => 'nullable|integer',
            'services' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $department) {
            $department->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'head_id' => $request->head_id,
                'email' => $request->email,
                'phone' => $request->phone,
                'display_order' => $request->display_order ?? 0,
                'services' => $request->services,
                'is_active' => $request->is_active ?? true,
            ]);
        });

        return redirect()->route($user->user_type.'.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
            $user = Auth::user();
        // Check if department has children or users
        if ($department->children()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete department with sub-departments.');
        }


        $department->delete();

        return redirect()->route($user->user_type.'.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    // Service mapping methods
    public function updateServices(Request $request, Department $department)
    {
        $request->validate([
            'services' => 'required|array',
        ]);

        $department->update(['services' => $request->services]);

        return response()->json(['success' => true, 'message' => 'Services updated successfully.']);
    }

    // Hierarchy management
    public function updateHierarchy(Request $request)
    {
        $request->validate([
            'hierarchy' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $this->saveHierarchy($request->hierarchy);
        });

        return response()->json(['success' => true, 'message' => 'Hierarchy updated successfully.']);
    }

    private function saveHierarchy($items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            $department = Department::find($item['id']);
            if ($department) {
                $department->update([
                    'parent_id' => $parentId,
                    'display_order' => $index
                ]);

                if (isset($item['children'])) {
                    $this->saveHierarchy($item['children'], $item['id']);
                }
            }
        }
    }

    // Add this method to DepartmentController
    public function hierarchyTree()
    {
            $user = Auth::user();
        $tree = Department::getTree();
        return view($user->user_type.'.department.partials.hierarchy-tree', compact('tree'));
    }
}