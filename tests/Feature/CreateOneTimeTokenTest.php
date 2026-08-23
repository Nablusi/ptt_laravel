<?php

use App\Http\Controllers\CreateOneTimeToken;
use App\Http\Requests\ApproveOneTimeTokenRequest;
use App\Http\Requests\OneTimeRequest;
use App\Http\Resources\ApproveOneTimeTokenResource;
use App\Models\OneTimeToken;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('users')) {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
        });
    }
});

test('it stores a hashed token and returns the plain token once', function () {
    $userId = createUserId();
    $request = OneTimeRequest::create('/one-time-tokens', 'POST', [
        'user_id' => $userId,
    ]);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->validateResolved();

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

test('it approves a valid unused token', function () {
    $userId = createUserId();
    $plainTextToken = createPlainTextOneTimeToken();

    $response = app(CreateOneTimeToken::class)->approve(
        approveOneTimeTokenRequest(['token' => $plainTextToken], $userId),
    );

    expect($response)->toBeInstanceOf(ApproveOneTimeTokenResource::class);

    $payload = $response->resolve();

    expect($payload['message'])->toBe('Authentication approved')
        ->and($payload['user_id'])->toBe($userId)
        ->and($payload['used_at'])->not->toBeNull();

    $record = OneTimeToken::query()->first();

    expect($record->user_id)->toBe($userId)
        ->and($record->used_at)->not->toBeNull();
});

test('it rejects an invalid token', function () {
    $response = app(CreateOneTimeToken::class)->approve(
        approveOneTimeTokenRequest(['token' => Str::random(64)]),
    );

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(401)
        ->and($response->getData(true)['message'])->toBe('Invalid token');
});

test('it rejects a token that was already used', function () {
    $plainTextToken = createPlainTextOneTimeToken(['used_at' => now()]);

    $response = app(CreateOneTimeToken::class)->approve(
        approveOneTimeTokenRequest(['token' => $plainTextToken]),
    );

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(401)
        ->and($response->getData(true)['message'])->toBe('Token already used');
});

test('it rejects an expired token', function () {
    $plainTextToken = createPlainTextOneTimeToken(['expires_at' => now()->subSecond()]);

    $response = app(CreateOneTimeToken::class)->approve(
        approveOneTimeTokenRequest(['token' => $plainTextToken]),
    );

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(401)
        ->and($response->getData(true)['message'])->toBe('Token expired');
});

test('it requires a token to approve', function () {
    approveOneTimeTokenRequest([]);
})->throws(ValidationException::class);

function createUserId(): string
{
    $userId = (string) Str::uuid();

    DB::table('users')->insert([
        'id' => $userId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $userId;
}

function createPlainTextOneTimeToken(array $overrides = []): string
{
    $plainTextToken = Str::random(64);

    $oneTimeToken = new OneTimeToken;
    $oneTimeToken->fill(array_merge([
        'token' => OneTimeToken::hashToken($plainTextToken),
        'expires_at' => now()->addSeconds(60),
    ], $overrides));
    $oneTimeToken->save();

    return $plainTextToken;
}

function approveOneTimeTokenRequest(array $data, ?string $userId = null): ApproveOneTimeTokenRequest
{
    $request = ApproveOneTimeTokenRequest::create('/one-time-tokens/approve', 'POST', $data);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setUserResolver(fn () => (object) ['id' => $userId ?? createUserId()]);
    $request->validateResolved();

    return $request;
}
