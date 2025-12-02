<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.token');
    }

    /**
     * Get current group info
     */
    public function info()
    {
        $user = Auth::user();
        
        if (!$user->group_id) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'User does not belong to any group'
            ]);
        }

        $group = Group::with('users')->find($user->group_id);

        return response()->json([
            'success' => true,
            'data' => $group,
            'message' => 'Group info retrieved successfully'
        ]);
    }

    /**
     * Create a new group
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        if ($user->group_id) {
            return response()->json([
                'success' => false,
                'message' => 'User already belongs to a group'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Generate unique code
        do {
            $code = 'GRP-' . strtoupper(Str::random(6));
        } while (Group::where('code', $code)->exists());

        $group = Group::create([
            'name' => $request->name,
            'code' => $code,
            'created_by' => $user->id
        ]);

        // Update user's group
        $user->group_id = $group->id;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $group,
            'message' => 'Group created successfully'
        ], 201);
    }

    /**
     * Join a group using code
     */
    public function join(Request $request)
    {
        $user = Auth::user();

        if ($user->group_id) {
            return response()->json([
                'success' => false,
                'message' => 'User already belongs to a group'
            ], 400);
        }

        $request->validate([
            'code' => 'required|string|exists:groups,code',
        ]);

        $group = Group::where('code', $request->code)->first();

        // Update user's group
        $user->group_id = $group->id;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $group,
            'message' => 'Successfully joined group'
        ]);
    }

    /**
     * Leave current group
     */
    public function leave()
    {
        $user = Auth::user();

        if (!$user->group_id) {
            return response()->json([
                'success' => false,
                'message' => 'User does not belong to any group'
            ], 400);
        }

        // Optional: Check if user is the last member or owner
        // For now, simple leave logic
        
        $user->group_id = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the group'
        ]);
    }
}
