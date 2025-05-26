<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Giftcode;
use App\Models\GiftcodeUsage;
use Illuminate\Support\Str;
use DB;

class GiftcodeController extends Controller
{
    /**
     * Display a listing of giftcodes
     */
    public function index(Request $request)
    {
        try {
            $query = Giftcode::query();

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('content', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // Type filter
            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                switch ($request->status) {
                    case 'active':
                        $query->where(function($q) {
                            $q->where('period', 0)
                              ->orWhere('created_at', '>', now()->subDays(DB::raw('period')));
                        });
                        break;
                    case 'expired':
                        $query->where('period', '>', 0)
                              ->where('created_at', '<=', now()->subDays(DB::raw('period')));
                        break;
                }
            }

            $giftcodes = $query->withCount('usageLogs')
                             ->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'giftcodes' => $giftcodes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created giftcode
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|integer|in:0,1',
                'content' => 'required|string|max:255',
                'items' => 'required|string',
                'limit' => 'required|integer|min:0',
                'period' => 'required|integer|min:0',
                'zoneid' => 'required|integer',
                'accounts' => 'nullable|string',
                'multiple' => 'required|boolean',
                'number' => 'required_if:multiple,true|integer|min:1|max:1000',
                'code' => 'required_if:multiple,false|string|max:50'
            ]);

            $codes = [];
            
            if ($request->multiple) {
                // Generate multiple codes
                $number = $request->number;
                for ($i = 0; $i < $number; $i++) {
                    do {
                        $code = strtoupper(Str::random(8));
                    } while (Giftcode::where('code', 'like', "%{$code}%")->exists());
                    
                    $codes[] = $code;
                }
            } else {
                // Single code
                $code = strtoupper($request->code);
                if (Giftcode::where('code', 'like', "%{$code}%")->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Giftcode đã tồn tại'
                    ], 400);
                }
                $codes[] = $code;
            }

            // Validate items format
            $items = explode(PHP_EOL, trim($request->items));
            foreach ($items as $item) {
                $parts = explode(',', trim($item));
                if (count($parts) !== 7) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Định dạng item không đúng. Cần 7 tham số: goodsid,count,binding,forge_level,appendproplev,lucky,excellenceinfo'
                    ], 400);
                }
            }

            $giftcode = Giftcode::create([
                'type' => $request->type,
                'multiple' => $request->multiple,
                'code' => $codes,
                'items' => $items,
                'content' => $request->content,
                'limit' => $request->limit,
                'accounts' => trim($request->accounts),
                'period' => $request->period,
                'zoneid' => $request->zoneid,
            ]);

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'create_giftcode',
                'target_id' => $giftcode->id,
                'details' => json_encode([
                    'codes' => $codes,
                    'content' => $request->content,
                    'items_count' => count($items)
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo giftcode thành công',
                'giftcode' => $giftcode,
                'codes' => $codes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified giftcode
     */
    public function show($id)
    {
        try {
            $giftcode = Giftcode::with('usageLogs.user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'giftcode' => $giftcode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified giftcode
     */
    public function update(Request $request, $id)
    {
        try {
            $giftcode = Giftcode::findOrFail($id);

            $request->validate([
                'type' => 'required|integer|in:0,1',
                'content' => 'required|string|max:255',
                'items' => 'required|string',
                'limit' => 'required|integer|min:0',
                'period' => 'required|integer|min:0',
                'accounts' => 'nullable|string'
            ]);

            // Validate items format
            $items = explode(PHP_EOL, trim($request->items));
            foreach ($items as $item) {
                $parts = explode(',', trim($item));
                if (count($parts) !== 7) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Định dạng item không đúng. Cần 7 tham số: goodsid,count,binding,forge_level,appendproplev,lucky,excellenceinfo'
                    ], 400);
                }
            }

            $giftcode->update([
                'type' => $request->type,
                'items' => $items,
                'content' => $request->content,
                'limit' => $request->limit,
                'accounts' => trim($request->accounts),
                'period' => $request->period,
            ]);

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'update_giftcode',
                'target_id' => $giftcode->id,
                'details' => json_encode([
                    'content' => $request->content,
                    'items_count' => count($items)
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giftcode thành công',
                'giftcode' => $giftcode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified giftcode
     */
    public function destroy($id)
    {
        try {
            $giftcode = Giftcode::findOrFail($id);
            
            // Log admin action before deletion
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'delete_giftcode',
                'target_id' => $giftcode->id,
                'details' => json_encode([
                    'content' => $giftcode->content,
                    'codes' => $giftcode->code
                ]),
                'created_at' => now()
            ]);

            $giftcode->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa giftcode thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get giftcode usage statistics
     */
    public function usage($id)
    {
        try {
            $giftcode = Giftcode::findOrFail($id);
            
            $usageLogs = GiftcodeUsage::where('giftcode_id', $id)
                                   ->with('user')
                                   ->orderBy('used_at', 'desc')
                                   ->paginate(20);

            $stats = [
                'total_usage' => $giftcode->total_usage,
                'remaining_usage' => $giftcode->remaining_usage,
                'usage_percentage' => $giftcode->limit > 0 ? ($giftcode->total_usage / $giftcode->limit) * 100 : 0
            ];

            return response()->json([
                'success' => true,
                'giftcode' => $giftcode,
                'usage_logs' => $usageLogs,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export giftcode list
     */
    public function export($id)
    {
        try {
            $giftcode = Giftcode::findOrFail($id);
            
            $codes = is_array($giftcode->code) ? $giftcode->code : [$giftcode->code];
            $content = implode(PHP_EOL, $codes);
            
            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="giftcode_' . $giftcode->id . '.txt"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
