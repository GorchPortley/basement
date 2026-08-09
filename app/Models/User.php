<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['name', 'email', 'password', 'forum_password', 'forum_token'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes','forum_password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, InteractsWithMedia;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function forumPassword()
    {
        return $this->forum_password;
    }

    protected static function booted()
    {
//
    }

    public function drivers()
    {
        return $this->morphMany(Driver::class, 'owner');
    }

    public function designs()
    {
        return $this->morphMany(Design::class, 'owner');
    }

    // designs where this user is a collaborator
    public function collaborations()
    {
        return $this->morphToMany(Design::class, 'collaborator', 'collaborator_design')
            ->using(CollaboratorDesign::class)
            ->withPivot('payload');
    }
}
