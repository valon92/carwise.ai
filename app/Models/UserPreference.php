<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preference_key',
        'preference_value',
        'preference_type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get the user that owns the preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the preference value casted to the correct type.
     */
    public function getValueAttribute()
    {
        return match($this->preference_type) {
            'boolean' => filter_var($this->preference_value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->preference_value,
            'float' => (float) $this->preference_value,
            'json' => json_decode($this->preference_value, true),
            default => $this->preference_value
        };
    }

    /**
     * Set the preference value with proper type casting.
     */
    public function setValueAttribute($value)
    {
        $this->preference_value = match($this->preference_type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'float' => (string) (float) $value,
            'json' => json_encode($value),
            default => (string) $value
        };
    }

    /**
     * Scope for filtering by preference key.
     */
    public function scopeKey($query, string $key)
    {
        return $query->where('preference_key', $key);
    }

    /**
     * Scope for filtering by preference type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('preference_type', $type);
    }

    /**
     * Scope for public preferences.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private preferences.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Get or create a preference for a user.
     */
    public static function getOrCreate($userId, $key, $defaultValue = null, $type = 'string', $description = null)
    {
        $preference = static::where('user_id', $userId)
            ->where('preference_key', $key)
            ->first();

        if (!$preference) {
            $preference = static::create([
                'user_id' => $userId,
                'preference_key' => $key,
                'preference_value' => $defaultValue,
                'preference_type' => $type,
                'description' => $description
            ]);
        }

        return $preference;
    }

    /**
     * Set a preference value for a user.
     */
    public static function set($userId, $key, $value, $type = 'string', $description = null)
    {
        $preference = static::getOrCreate($userId, $key, $value, $type, $description);
        $preference->preference_type = $type;
        $preference->preference_value = $value;
        $preference->description = $description;
        $preference->save();

        return $preference;
    }

    /**
     * Get a preference value for a user.
     */
    public static function get($userId, $key, $defaultValue = null)
    {
        $preference = static::where('user_id', $userId)
            ->where('preference_key', $key)
            ->first();

        return $preference ? $preference->value : $defaultValue;
    }
}
