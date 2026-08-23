<?php

namespace App\Http\Controllers;

use App\Http\Resources\OneTimeTokenResource;
use App\Models\OneTimeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateOneTimeToken extends Controller
{
    public function createOneTimeToken(Request $request): OneTimeTokenResource
    {
        $data = $request->validate([
            'user_id' => ['required', 'uuid'],
        ]);

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
}
