<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * [Admin] Danh sách tất cả voucher.
     */
    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $vouchers]);
    }

    /**
     * [Admin] Tạo voucher mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percent,fixed',
            'min_order_value' => 'required|numeric|min:0',
            'max_uses_per_user' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $voucher = Voucher::create($validated);

        return response()->json(['data' => $voucher, 'message' => 'Tạo voucher thành công!'], 201);
    }

    /**
     * [Admin] Cập nhật voucher.
     */
    public function update(Request $request, int $id)
    {
        $voucher = Voucher::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:vouchers,code,' . $id,
            'discount_value' => 'sometimes|numeric|min:0',
            'discount_type' => 'sometimes|in:percent,fixed',
            'min_order_value' => 'sometimes|numeric|min:0',
            'max_uses_per_user' => 'sometimes|integer|min:1',
            'valid_from' => 'sometimes|date',
            'valid_to' => 'sometimes|date',
            'is_active' => 'boolean',
        ]);

        $voucher->update($validated);

        return response()->json(['data' => $voucher, 'message' => 'Cập nhật voucher thành công!']);
    }

    /**
     * [Admin] Xóa voucher.
     */
    public function destroy(int $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return response()->json(null, 204);
    }
}
