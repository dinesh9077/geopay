<?php 
namespace App\Services;

use App\Models\AccessToken;
use Illuminate\Support\Str;

class OpaqueToken
{ 
    public static function issue(int $userId, ?string $name = null, $ttlMinutes): string
    {  
        $secret = rtrim(strtr(base64_encode(random_bytes(128)), '+/', '-_'), '=');
        $hash = hash('sha256', $secret);

        $token = AccessToken::create([
            'user_id'    => $userId,
            'name'       => $name,
            'token_hash' => $hash,
			'expires_at' => is_bool($ttlMinutes) ? null : now()->addMinutes($ttlMinutes), 
            'ip'         => request()->ip(),
            'ua'         => Str::limit((string) request()->userAgent(), 255),
        ]);

        return "{$token->id}.{$secret}";
    } 
	
	public static function validate(?string $bearer)
	{
		if (!$bearer || !str_contains($bearer, '.')) {
			request()->attributes->set('auth_error', 'ERR_TOKEN_MISSING');
			return null;
		}

		[$id, $secret] = explode('.', $bearer, 2); 
		if (!ctype_digit($id) || $secret === '') {
			request()->attributes->set('auth_error', 'ERR_TOKEN_FORMAT');
			return null;
		}

		$token = AccessToken::with('user')->find($id);
		if (!$token) {
			request()->attributes->set('auth_error', 'ERR_TOKEN_NOT_FOUND');
			return null;
		}
		 
		if (!$token->isActive()) {
			request()->attributes->set('auth_error', 'ERR_TOKEN_EXPIRED');
			return null;
		}

		if (!hash_equals($token->token_hash, hash('sha256', $secret))) {
			request()->attributes->set('auth_error', 'ERR_TOKEN_INVALID');
			return null;
		}

		$token->forceFill(['last_used_at' => now()])->saveQuietly();
		return $token;
	}
 
    public static function revoke(AccessToken $token): void
    {
        $token->update(['revoked_at' => now()]);
    }
}
