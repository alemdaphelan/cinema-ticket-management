<?php

namespace App\Http\Controllers;

use App\Models\Snack;
use Illuminate\Http\Request;

class SnackController extends Controller
{
    public function index()
    {
        $snacks = Snack::where('is_active', true)->get();
        return response()->json([
            'message' => 'Lấy danh sách bắp nước thành công',
            'data' => $snacks
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
            'is_active' => 'boolean'
        ]);

        $snack = Snack::create([
            'name' => $request->name,
            'price' => $request->price,
            'image_url' => $request->image_url,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Tạo bắp nước thành công',
            'data' => $snack
        ], 201);
    }

    public function show(Snack $snack)
    {
        //
    }

    public function update(Request $request, Snack $snack)
    {
        //
    }

    public function destroy(Snack $snack)
    {
        //
    }
}
