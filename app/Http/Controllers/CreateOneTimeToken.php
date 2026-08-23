<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveOneTimeTokenRequest;
use App\Http\Requests\OneTimeRequest;
use App\Http\Resources\ApproveOneTimeTokenResource;
use App\Http\Resources\OneTimeTokenResource;
use App\Models\OneTimeToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CreateOneTimeToken extends Controller
{
    public function createOneTimeToken(OneTimeRequest $request): OneTimeTokenResource
    {
        $data = $request->validated();
        $plainTextToken = Str::random(64);
        $oneTimeToken = new OneTimeToken;
        $oneTimeToken->fill([
            'token' => OneTimeToken::hashToken($plainTextToken),
            'user_id' => $data['user_id'],
            'expires_at' => now()->addSeconds(60),
        ]);
        $oneTimeToken->save();
        $oneTimeToken->plainTextToken = $plainTextToken;

        return new OneTimeTokenResource($oneTimeToken);
    }

    public function approve(ApproveOneTimeTokenRequest $request): ApproveOneTimeTokenResource|JsonResponse
    {
        $oneTimeToken = OneTimeToken::query()
            ->where('token', OneTimeToken::hashToken($request->validated('token')))
            ->first();

        if (! $oneTimeToken) {
            return response()->json([
                'message' => 'Invalid token',
            ], 401);
        }

        if ($oneTimeToken->used_at !== null) {
            return response()->json([
                'message' => 'Token already used',
            ], 401);
        }

        if ($oneTimeToken->expires_at->isPast()) {
            return response()->json([
                'message' => 'Token expired',
            ], 401);
        }

        $oneTimeToken->update([
            'user_id' => $request->user()->id,
            'used_at' => now(),
        ]);

        return new ApproveOneTimeTokenResource($oneTimeToken);
    }
}
