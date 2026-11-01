<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogActivity;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Merchant::with('user')->withCount('products', 'sales');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('txtMerchantName', 'LIKE', "%{$search}%")
                  ->orWhere('txtOwnerName', 'LIKE', "%{$search}%")
                  ->orWhere('txtBoothNumber', 'LIKE', "%{$search}%");
            });
        }

        $merchants = $query->orderBy('intMerchant_ID')->paginate(10)->withQueryString();

        return view('admin.merchants.index', compact('merchants', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_name' => 'required|string|max:150',
            'owner_name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'booth_number' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:150',
            'username' => 'required|string|max:50|unique:mUser,txtUsername',
            'password' => 'required|string|min:4',
        ]);

        $code = 'UMKM-' . str_pad(Merchant::count() + 1, 3, '0', STR_PAD_LEFT);

        $merchant = Merchant::create([
            'txtMerchantCode' => $code,
            'txtMerchantName' => $validated['merchant_name'],
            'txtOwnerName' => $validated['owner_name'],
            'txtPhone' => $validated['phone'],
            'txtBoothNumber' => $validated['booth_number'] ?? 'Stand Baru',
            'txtCategory' => $validated['category'] ?? 'Kuliner',
            'txtDescription' => $validated['description'] ?? null,
            'txtBankName' => $validated['bank_name'] ?? null,
            'txtBankAccountNumber' => $validated['bank_account_number'] ?? null,
            'txtBankAccountHolder' => $validated['bank_account_holder'] ?? $validated['owner_name'],
            'bitActive' => 1,
        ]);

        User::create([
            'txtUsername' => trim($validated['username']),
            'txtFullName' => $validated['merchant_name'],
            'txtEmail' => Str::slug($validated['username']) . '@umkm.local',
            'txtPassword' => Hash::make($validated['password']),
            'txtRole' => 'umkm',
            'intMerchant_ID' => $merchant->intMerchant_ID,
            'txtPhone' => $validated['phone'],
            'bitActive' => 1,
        ]);

        LogActivity::record('CREATE_MERCHANT', "Pendaftaran booth UMKM {$merchant->txtMerchantName} ({$code}).");

        return redirect()->route('admin.merchants.index')->with('success', "Booth UMKM {$merchant->txtMerchantName} berhasil didaftarkan!");
    }

    public function update(Request $request, int $id)
    {
        $merchant = Merchant::findOrFail($id);

        $validated = $request->validate([
            'merchant_name' => 'required|string|max:150',
            'owner_name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'booth_number' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:150',
            'password' => 'nullable|string|min:4',
        ]);

        $merchant->update([
            'txtMerchantName' => $validated['merchant_name'],
            'txtOwnerName' => $validated['owner_name'],
            'txtPhone' => $validated['phone'],
            'txtBoothNumber' => $validated['booth_number'],
            'txtCategory' => $validated['category'],
            'txtDescription' => $validated['description'],
            'txtBankName' => $validated['bank_name'],
            'txtBankAccountNumber' => $validated['bank_account_number'],
            'txtBankAccountHolder' => $validated['bank_account_holder'],
        ]);

        if (!empty($validated['password']) && $merchant->user) {
            $merchant->user->update([
                'txtPassword' => Hash::make($validated['password']),
            ]);
        }

        LogActivity::record('UPDATE_MERCHANT', "Update data booth UMKM {$merchant->txtMerchantName}.");

        return redirect()->route('admin.merchants.index')->with('success', "Data UMKM {$merchant->txtMerchantName} berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $merchant = Merchant::findOrFail($id);
        $merchant->update(['bitActive' => 0]);
        if ($merchant->user) {
            $merchant->user->update(['bitActive' => 0]);
        }

        LogActivity::record('DEACTIVATE_MERCHANT', "Non-aktifkan booth UMKM {$merchant->txtMerchantName}.");

        return redirect()->route('admin.merchants.index')->with('success', "Booth UMKM {$merchant->txtMerchantName} dinonaktifkan.");
    }
}

