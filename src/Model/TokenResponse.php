<?php

namespace Poweroffice\Model;

use Poweroffice\Contracts\ModelInterface;

final class TokenResponse implements ModelInterface
{
    public function __construct(
        public ?string $accessToken = null,
        public ?string $tokenType = null,
        public ?int $expiresIn = null,
        public ?int $expiresAt = null,
    ) {
        if ($this->expiresIn !== null && $this->expiresAt === null) {
            $this->expiresAt = time() + $this->expiresIn - 60; // subtract 60s for safety
        }
    }

    public function toJson(): string
    {
        return json_encode([
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'expires_at' => $this->expiresAt,
        ]);
    }

    public static function make(array $data): TokenResponse
    {
        return new self(
            accessToken: $data['access_token'],
            tokenType: $data['token_type'],
            expiresIn: $data['expires_in'],
        );
    }
}