<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:50',
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'min_order_value' => 'required|numeric|min:0',
            'max_uses_per_user' => 'required|integer|min:1',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $voucher = Voucher::create([
            'code' => strtoupper($request->code),
            'discount_value' => $request->discount_value,
            'discount_type' => $request->discount_type,
            'min_order_value' => $request->min_order_value,
            'max_uses_per_user' => $request->max_uses_per_user,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Tạo voucher thành công',
            'data' => $voucher
        ], 201);
    }

    public function show(Voucher $voucher)
    {
        //
    }

    public function update(Request $request, Voucher $voucher)
    {
        //
    }

    public function destroy(Voucher $voucher)
    {
        //
    }
}
