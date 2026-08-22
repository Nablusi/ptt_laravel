<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyChannelRequest;
use App\Http\Requests\StoreSubChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ChannelController extends Controller
{
    public function index(Request $request)
    {
        
        $user = $request->user();
//ccmpany here account حراء
        $query = Channel::with(['company', 'parent'])->orderBy('level')->orderBy('created_at');

        if (! $user->isSuperAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $channels = $query->get();

        return response()->json([
            'success' => true,
            'count' => $channels->count(),
            'data' => ChannelResource::collection($channels),
        ]);
    }

    public function storeCompanyChannel(StoreCompanyChannelRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $companyId = $data['company_id']; 
            

        if ($user->IsCompanyAdmin() && $companyId !== $user->company_id) {
            abort(403, 'You can only create a company channel for your own company.');
        }
        
        $existingRoot = Channel::query()
            ->where('company_id', $companyId)
            ->whereNull('parent_channel_id')
            ->where('level', 0)
            ->first();

        if ($existingRoot) {
            return response()->json([
                'success' => false,
                'message' => 'Company root channel already exists',
                'data' => new ChannelResource($existingRoot),
            ], 409);
        }
        $channel = new Channel();
        $channel->fill([
            'id' => (string) Str::uuid(),
            'company_id' => $companyId,
            'parent_channel_id' => null,
            'name' => $data['name'],
            'level' => 0,
            'is_active' => true,
        ]);
        $channel->save();

        $member = new ChannelMember();
        $member->fill([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'channel_role' => 'company_admin',
            'is_muted' => false,
        ]);
        $member->save();

        if (! $user->isSuperAdmin() && $user->system_role !== 'company_admin') {
            $user->update(['system_role' => 'company_admin']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company root channel created successfully',
            'data' => new ChannelResource($channel->load(['company', 'parent'])),
        ], 201);
    }

    public function storeSubChannel(StoreSubChannelRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $parent = $data['parent_channel_id'];

        if ($user->cannot('view', $parent)) {
            abort(403, 'You can only create channels within your own company.');
        }

        $channel = new Channel();
        $channel->fill([
            'id' => (string) Str::uuid(),
            'company_id' => $parent->company_id,
            'parent_channel_id' => $parent->id,
            'name' => $data['name'],
            'level' => $parent->level + 1,
            'is_active' => true,
        ]);
        $channel->save();

        $member = new ChannelMember();

        $member->fill([
            [
                'channel_id' => $channel->id,
                'user_id' => $user->id,
            ],
            [
                'id' => (string) Str::uuid(),
                'channel_role' => 'company_admin',
                'is_muted' => false,
            ]
        ]);
        $member->save();

        return response()->json([
            'success' => true,
            'message' => 'Sub-channel created successfully',
            'data' => new ChannelResource($channel->load(['company', 'parent'])),
        ], 201);
    }

    public function update(UpdateChannelRequest $request, Channel $channel)
    {
        $channel->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Channel updated successfully',
            'data' => new ChannelResource($channel->fresh()->load(['company', 'parent'])),
        ]);
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Channel deleted successfully',
        ]);
    }
}