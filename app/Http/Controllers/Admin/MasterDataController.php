<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\LogActivity;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'department');
        $search = $request->input('search');

        // Departments
        $deptQuery = Department::query();
        if ($tab === 'department' && !empty($search)) {
            $deptQuery->where(function ($q) use ($search) {
                $q->where('txtDepartmentCode', 'LIKE', "%{$search}%")
                  ->orWhere('txtDepartmentName', 'LIKE', "%{$search}%");
            });
        }
        $departments = $deptQuery->orderBy('txtDepartmentName')->paginate(10, ['*'], 'dept_page')->withQueryString();

        // Categories (global master where intMerchant_ID is null)
        $catQuery = Category::whereNull('intMerchant_ID')->withCount('products');
        if ($tab === 'category' && !empty($search)) {
            $catQuery->where(function ($q) use ($search) {
                $q->where('txtCategoryName', 'LIKE', "%{$search}%")
                  ->orWhere('txtCategorySlug', 'LIKE', "%{$search}%");
            });
        }
        $categories = $catQuery->orderBy('txtCategoryName')->paginate(10, ['*'], 'cat_page')->withQueryString();

        // Units
        $unitQuery = Unit::query();
        if ($tab === 'unit' && !empty($search)) {
            $unitQuery->where(function ($q) use ($search) {
                $q->where('txtUnitCode', 'LIKE', "%{$search}%")
                  ->orWhere('txtUnitName', 'LIKE', "%{$search}%");
            });
        }
        $units = $unitQuery->orderBy('txtUnitName')->paginate(10, ['*'], 'unit_page')->withQueryString();

        $stats = [
            'total_dept' => Department::where('bitActive', 1)->count(),
            'total_cat' => Category::whereNull('intMerchant_ID')->where('bitActive', 1)->count(),
            'total_unit' => Unit::where('bitActive', 1)->count(),
        ];

        return view('admin.master.index', compact('departments', 'categories', 'units', 'tab', 'search', 'stats'));
    }

    // ================= DEPARTMENT CRUD =================
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'department_code' => 'required|string|max:50|unique:mDepartment,txtDepartmentCode',
            'department_name' => 'required|string|max:150',
            'active' => 'nullable|boolean',
        ]);

        $dept = Department::create([
            'txtDepartmentCode' => strtoupper(trim($validated['department_code'])),
            'txtDepartmentName' => trim($validated['department_name']),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('CREATE_MASTER_DEPT', "Membuat master departemen {$dept->txtDepartmentName} ({$dept->txtDepartmentCode}).");

        return redirect()->route('admin.master.index', ['tab' => 'department'])
            ->with('success', "Departemen '{$dept->txtDepartmentName}' berhasil ditambahkan!");
    }

    public function updateDepartment(Request $request, int $id)
    {
        $dept = Department::findOrFail($id);

        $validated = $request->validate([
            'department_code' => 'required|string|max:50|unique:mDepartment,txtDepartmentCode,' . $id . ',intDepartment_ID',
            'department_name' => 'required|string|max:150',
            'active' => 'nullable|boolean',
        ]);

        $dept->update([
            'txtDepartmentCode' => strtoupper(trim($validated['department_code'])),
            'txtDepartmentName' => trim($validated['department_name']),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('UPDATE_MASTER_DEPT', "Memperbarui master departemen {$dept->txtDepartmentName}.");

        return redirect()->route('admin.master.index', ['tab' => 'department'])
            ->with('success', "Departemen '{$dept->txtDepartmentName}' berhasil diperbarui!");
    }

    public function destroyDepartment(int $id)
    {
        $dept = Department::findOrFail($id);
        $name = $dept->txtDepartmentName;

        // Toggle or delete
        $dept->delete();

        LogActivity::record('DELETE_MASTER_DEPT', "Menghapus master departemen {$name}.");

        return redirect()->route('admin.master.index', ['tab' => 'department'])
            ->with('success', "Departemen '{$name}' berhasil dihapus.");
    }

    // ================= CATEGORY CRUD =================
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:100',
            'active' => 'nullable|boolean',
        ]);

        $name = trim($validated['category_name']);
        $cat = Category::create([
            'intMerchant_ID' => null, // Master category
            'txtCategoryName' => $name,
            'txtCategorySlug' => Str::slug($name),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('CREATE_MASTER_CAT', "Membuat master kategori {$cat->txtCategoryName}.");

        return redirect()->route('admin.master.index', ['tab' => 'category'])
            ->with('success', "Kategori '{$cat->txtCategoryName}' berhasil ditambahkan!");
    }

    public function updateCategory(Request $request, int $id)
    {
        $cat = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:100',
            'active' => 'nullable|boolean',
        ]);

        $name = trim($validated['category_name']);
        $cat->update([
            'txtCategoryName' => $name,
            'txtCategorySlug' => Str::slug($name),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('UPDATE_MASTER_CAT', "Memperbarui master kategori {$cat->txtCategoryName}.");

        return redirect()->route('admin.master.index', ['tab' => 'category'])
            ->with('success', "Kategori '{$cat->txtCategoryName}' berhasil diperbarui!");
    }

    public function destroyCategory(int $id)
    {
        $cat = Category::findOrFail($id);
        $name = $cat->txtCategoryName;

        // Check if there are products using this category
        $productCount = Product::where('intCategory_ID', $id)->count();
        if ($productCount > 0) {
            $cat->update(['bitActive' => 0]);
            return redirect()->route('admin.master.index', ['tab' => 'category'])
                ->with('warning', "Kategori '{$name}' memiliki {$productCount} produk terkait sehingga dinonaktifkan (bukan dihapus permanen).");
        }

        $cat->delete();
        LogActivity::record('DELETE_MASTER_CAT', "Menghapus master kategori {$name}.");

        return redirect()->route('admin.master.index', ['tab' => 'category'])
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }

    // ================= UNIT CRUD =================
    public function storeUnit(Request $request)
    {
        $validated = $request->validate([
            'unit_code' => 'required|string|max:30|unique:mUnit,txtUnitCode',
            'unit_name' => 'required|string|max:50',
            'active' => 'nullable|boolean',
        ]);

        $unit = Unit::create([
            'txtUnitCode' => strtoupper(trim($validated['unit_code'])),
            'txtUnitName' => trim($validated['unit_name']),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('CREATE_MASTER_UNIT', "Membuat master satuan {$unit->txtUnitName} ({$unit->txtUnitCode}).");

        return redirect()->route('admin.master.index', ['tab' => 'unit'])
            ->with('success', "Satuan '{$unit->txtUnitName}' berhasil ditambahkan!");
    }

    public function updateUnit(Request $request, int $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'unit_code' => 'required|string|max:30|unique:mUnit,txtUnitCode,' . $id . ',intUnit_ID',
            'unit_name' => 'required|string|max:50',
            'active' => 'nullable|boolean',
        ]);

        $unit->update([
            'txtUnitCode' => strtoupper(trim($validated['unit_code'])),
            'txtUnitName' => trim($validated['unit_name']),
            'bitActive' => $request->boolean('active', true) ? 1 : 0,
        ]);

        LogActivity::record('UPDATE_MASTER_UNIT', "Memperbarui master satuan {$unit->txtUnitName}.");

        return redirect()->route('admin.master.index', ['tab' => 'unit'])
            ->with('success', "Satuan '{$unit->txtUnitName}' berhasil diperbarui!");
    }

    public function destroyUnit(int $id)
    {
        $unit = Unit::findOrFail($id);
        $name = $unit->txtUnitName;

        $unit->delete();
        LogActivity::record('DELETE_MASTER_UNIT', "Menghapus master satuan {$name}.");

        return redirect()->route('admin.master.index', ['tab' => 'unit'])
            ->with('success', "Satuan '{$name}' berhasil dihapus.");
    }
}

