<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignUserToChannelRequest;
use App\Http\Requests\PromoteUserRequest;
use App\Http\Requests\RemoveUserFromChannelRequest;
use App\Http\Resources\ChannelMemberResource;
use App\Models\ChannelMember;
use Illuminate\Support\Str;

class ChannelMemberController extends Controller
{

    public function assign(AssignUserToChannelRequest $request)
    {
        $channelMember = new ChannelMember();

        $channelMember->fill([
            'id' => (string) Str::uuid(),
            'channel_id' => $request->channel_id,
            'user_id' => $request->user_id,
            'channel_role' => $request->channel_role ?? 'member',
            'is_muted' => false,
        ]);

        $channelMember->save();

        return response()->json([
            'success' => true,
            'message' => 'User assigned to channel successfully',
            'data' => ChannelMemberResource::collection($channelMember),
        ], 201);
    }

    public function remove(RemoveUserFromChannelRequest $request)
    {
        $channelMember = ChannelMember::where('channel_id', $request->channel_id)
        ->where('user_id', $request->user_id)
        ->firstOrFail();

    $channelMember->delete();

    return response()->json([
        'success' => true,
        'message' => 'User removed from channel successfully',
        'data' => ChannelMemberResource::collection($channelMember),
    ]);
    }

    public function promote(PromoteUserRequest $request)
    {
        
        $channelMember = ChannelMember::where('channel_id', $request->channel_id)
        ->where('user_id', $request->user_id)
        ->firstOrFail();

    $channelMember->update(['channel_role' => $request->channel_role]);

    return response()->json([
        'success' => true,
        'message' => 'User promoted to channel admin successfully',
        'data' => ChannelMemberResource::collection($channelMember),
    ]);

    }
}