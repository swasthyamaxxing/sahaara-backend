<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

// Notifications
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

// Models
use App\Models\Medication;
use App\Models\Vital;
use App\Models\CaretakerPatient;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'age', 
        'gender'
    ];

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
     * Get the user's full name (Old Accessor Style).
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Determine if the user has a caretaker role.
     *
     * @return bool
     */
    public function isCaretaker() {
        return $this->role === 'caretaker';
    }

    /**
     * Determine if the user has a patient role.
     * * @return bool
     */
    public function isPatient() {
        return $this->role === 'patient';
    }


    public function canAccessPatientData(User $patient): bool
    {
        // User accessing their own data
        if ($this->id === $patient->id) {
            return true;
        }

        // User is an assigned caretaker
        return $this->isCaretaker() && CaretakerPatient::where([
            'patient_id'   => $patient->id,
            'caretaker_id' => $this->id,
        ])->exists();
    }

    /**
     * a user can be a caretaker or a patient
     * this function finds the caretaker of the patient
     * you have to be a patient
     */
    public function caretaker() {
        return $this->hasOne(CaretakerPatient::class, 'patient_id', 'id');
    }

    // $user->caretaker->name { name: 'Jane Doe', }
    // foreach( $user->caretakers() as $caretaker )
    // hasOne User::where()->first();
    // hasMany array of information User::where()->get();

    /**
     * this function finds the patients of the caretaker
     * you have to be role: caretaker
     * right now, we are working with users table
     */
    public function patients() {
        return $this->hasMany(CaretakerPatient::class, 'caretaker_id', 'id');
    }

    /**
     * Vitals
     */
    public function vitals () {
        return $this->hasMany(Vital::class, 'patient_id', 'id');
    }

    /**
     * Medications
     */
    public function medications () {
        return $this->hasMany(Medication::class, 'patient_id', 'id');
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
}