<?php

declare(strict_types=1);

final class AuthController
{
    public function __construct(private readonly Auth $auth)
    {
    }

    public function register(): never
    {
        $body = Request::jsonBody();
        $validated = AuthRequestValidator::register($body);
        if (!$validated['ok']) {
            JsonResponse::error($validated['error'], JsonResponse::statusForCode($validated['code']));
        }

        $result = $this->auth->register(
            $validated['data']['email'],
            $validated['data']['password']
        );

        if (!$result['ok']) {
            JsonResponse::error($result['error'], JsonResponse::statusForCode($result['code'] ?? null));
        }

        JsonResponse::json(ApiFormatter::user($result['user']), 201);
    }

    public function login(): never
    {
        $body = Request::jsonBody();
        $validated = AuthRequestValidator::login($body);
        if (!$validated['ok']) {
            JsonResponse::error($validated['error'], JsonResponse::statusForCode($validated['code']));
        }

        $result = $this->auth->authenticate(
            $validated['data']['email'],
            $validated['data']['password']
        );

        if (!$result['ok']) {
            JsonResponse::error('Invalid credentials', 401);
        }

        JsonResponse::json([
            'access_token' => JwtAuth::createToken((int) $result['user_id']),
            'token_type' => 'bearer',
        ]);
    }

    public function me(): never
    {
        $user = $this->auth->findUserById(AuthContext::requireUserId());
        if (!$user) {
            JsonResponse::error('Could not validate credentials', 401);
        }

        JsonResponse::json(ApiFormatter::user($user));
    }
}
