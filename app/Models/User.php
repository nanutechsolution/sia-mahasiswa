<?php

namespace App\Models;

use App\Domains\Akademik\Models\Dosen;
use App\Domains\Mahasiswa\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * Menggunakan UUID sebagai Primary Key.
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role', // Pastikan role ada di sini agar bisa disimpan
        'is_active',
        'person_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Profil Dosen
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    /**
     * Relasi ke Profil Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    /**
     * Shortcut Accessor: $user->profileable
     * Ini akan secara otomatis mengembalikan objek Dosen atau Mahasiswa 
     * berdasarkan role user tanpa perlu kolom morph di tabel users.
     */
    public function getProfileableAttribute()
    {
        if ($this->role === 'dosen') {
            return $this->dosen;
        }

        if ($this->role === 'mahasiswa') {
            return $this->mahasiswa;
        }

        return null;
    }

    // /person
    public function person()
    {
        return $this->belongsTo(\App\Domains\Core\Models\Person::class, 'person_id');
    }
}