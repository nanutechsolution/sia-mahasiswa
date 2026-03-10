<?php

namespace App\Models;

use App\Domains\Akademik\Models\Dosen;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles, HasApiTokens, SoftDeletes;

    /**
     * Menggunakan UUID sebagai Primary Key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Properti fillable diperbarui untuk mendukung Audit Login.
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'person_id',
        'last_login_at', // Audit Keamanan
        'last_login_ip', // Audit Keamanan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data diperbarui.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime', // Di-cast ke Carbon instance
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Profil Dosen (via Person)
     * Mengikuti pola SSOT: User -> Person -> Dosen
     */
    public function dosen()
    {
        return $this->hasOneThrough(
            Dosen::class, 
            \App\Domains\Core\Models\Person::class, 
            'id', // FK di ref_person
            'person_id', // FK di trx_dosen
            'person_id', // Local key di users
            'id' // Local key di ref_person
        );
    }

    /**
     * Relasi ke Profil Mahasiswa (via Person)
     * Mengikuti pola SSOT: User -> Person -> Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasOneThrough(
            Mahasiswa::class,
            \App\Domains\Core\Models\Person::class,
            'id',
            'person_id',
            'person_id',
            'id'
        );
    }

    /**
     * Relasi Inti ke Data Personil (Identity Center)
     */
    public function person()
    {
        return $this->belongsTo(\App\Domains\Core\Models\Person::class, 'person_id');
    }

    /**
     * Shortcut Accessor: $user->profileable
     * Secara cerdas mengembalikan objek Mahasiswa atau Dosen.
     */
    public function getProfileableAttribute()
    {
        return match($this->role) {
            'dosen' => $this->dosen,
            'mahasiswa' => $this->mahasiswa,
            default => null,
        };
    }
}