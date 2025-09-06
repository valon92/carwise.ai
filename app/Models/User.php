<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'avatar',
        'bio',
        'location',
        'timezone',
        'language',
        'status',
        'last_login_at',
        'last_login_ip',
        'login_history',
        'email_verified_at',
        'phone_verified_at',
        'notification_preferences',
        'privacy_settings',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'login_history' => 'array',
            'notification_preferences' => 'array',
            'privacy_settings' => 'array',
        ];
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function diagnosisSessions()
    {
        return $this->hasMany(DiagnosisSession::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function mechanic()
    {
        return $this->hasOne(Mechanic::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function isMechanic()
    {
        return $this->role === 'mechanic';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->name;
    }

    public function getInitialsAttribute()
    {
        $first = $this->first_name ? $this->first_name[0] : ($this->name ? $this->name[0] : 'U');
        $last = $this->last_name ? $this->last_name[0] : '';
        return strtoupper($first . $last);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->full_name) . "&color=7F9CF5&background=EBF4FF";
    }
}
