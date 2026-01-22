<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RedeemItem;

class RedeemItemController extends Controller
{
    /**
     * Display a listing of reward items.
     */
    public function index()
    {
        $items = RedeemItem::query()
            ->active()
            ->select(['id', 'name', 'description', 'points_required', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'points_required' => $item->points_required,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()
            ->json([
                'status' => 'success',
                'data' => $items,
            ]);
    }
}
