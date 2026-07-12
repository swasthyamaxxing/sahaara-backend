<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'name', 'email', 'password', 'role', 'age', 'gender'
    ];

    /**
     * Determine if the user has a caretaker role.
     *
     * @return bool True if the user is a caretaker, false otherwise.
     */
    public function isCaretaker() {
        return $this->role == 'caretaker';
    }

    /**
     * Determine if the user has a patient role.
     * 
     * @return bool True if the user is a patient, false otherwise.
     */
    public function isPatient() {
        return $this->role == 'patient';
    }

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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'name',
        'password',
        'remember_token'
    ];

    /**
     * The accessors to append to the model's array/JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'fullName',
    ];

    /**
     * Get the user's full name (CamelCase Accessor).
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name,
        );
    }
    
    protected $table = "users";
}
