<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use Illuminate\Http\Request;

class SnackController extends Controller
{
    /**
     * [Public] Danh sách bắp nước active.
     */
    public function publicIndex()
    {
        $snacks = Snack::where('is_active', true)->get();

        return response()->json(['data' => $snacks]);
    }

    /**
     * [Admin] Danh sách tất cả bắp nước.
     */
    public function index()
    {
        $snacks = Snack::orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $snacks]);
    }

    /**
     * [Admin] Thêm combo bắp nước.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $snack = Snack::create($validated);

        return response()->json(['data' => $snack, 'message' => 'Thêm bắp nước thành công!'], 201);
    }

    /**
     * [Admin] Cập nhật bắp nước.
     */
    public function update(Request $request, int $id)
    {
        $snack = Snack::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'image_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $snack->update($validated);

        return response()->json(['data' => $snack, 'message' => 'Cập nhật thành công!']);
    }

    /**
     * [Admin] Xóa bắp nước.
     */
    public function destroy(int $id)
    {
        $snack = Snack::findOrFail($id);
        $snack->delete();

        return response()->json(null, 204);
    }
}
