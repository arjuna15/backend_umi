<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email',
        'phone',
        'address',
        'bio',
        'avatar_url', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'nim_nip',
        'prodi',
        'phone',
        'address',
        'bio',
        'avatar_url',
        'dosen_wali_id',
        'jfa',
        'status',
        'email_notifications',
        'public_visibility',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password' => 'hashed',
            'email_notifications' => 'boolean',
            'public_visibility' => 'boolean',
        ];
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'mahasiswa_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'mahasiswa_id');
    }

    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function dosenWali()
    {
        return $this->belongsTo(User::class, 'dosen_wali_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'dosen_id');
    }
}
