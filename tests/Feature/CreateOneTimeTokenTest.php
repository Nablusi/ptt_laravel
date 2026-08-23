<?php

use App\Http\Controllers\CreateOneTimeToken;
use App\Models\OneTimeToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

test('it stores a hashed token and returns the plain token once', function () {
    $userId = (string) Str::uuid();
    $request = Request::create('/one-time-tokens', 'POST', [
        'user_id' => $userId,
    ]);

    $resource = app(CreateOneTimeToken::class)->createOneTimeToken($request);

    $payload = $resource->resolve();

    expect($payload['token'])->toBeString()->toHaveLength(64)
        ->and($payload['expires_in'])->toBeGreaterThan(0)
        ->and($payload['expires_in'])->toBeLessThanOrEqual(60);

    $record = OneTimeToken::query()->first();

    expect($record)->not->toBeNull()
        ->and($record->user_id)->toBe($userId)
        ->and($record->token)->toBe(OneTimeToken::hashToken($payload['token']))
        ->and($record->token)->not->toBe($payload['token'])
        ->and($record->used_at)->toBeNull();
});
