<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\GameBase;
use App\Models\QrUseList;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'nickname',
        'avatar_choice',
        'birthday',
        'remember_token',
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
            'password' => 'hashed',
        ];
    }

    public function gameBase()
    {
        return $this->hasOne(GameBase::class, 'gamebase_userid');
    }

    public function qrUseList()
    {
        return $this->hasMany(QrUseList::class, 'qruse_user_id');
    }

    public function preRegistrations()
    {
        return $this->hasMany(PreRegistration::class, 'prereg_phone', 'phone');
    }

    /**
     * SMS認証が完了しているかどうかを確認
     */
    public function isSmsVerified()
    {
        // pre_registrationsテーブルで同じ電話番号で認証済みのレコードがあるかチェック
        return \App\Models\PreRegistration::where('prereg_phone', $this->phone)
            ->where('prereg_is_verified', true)
            ->whereNotNull('prereg_verified_at')
            ->exists();
    }

    /**
     * 認証済みかどうかを確認（メール認証またはSMS認証）
     */
    public function isVerified()
    {
        return $this->email_verified_at || $this->isSmsVerified();
    }

    /**
     * 特定のアーティストのライブイベント参加回数を取得
     */
    public function getArtistParticipationCount($artistName)
    {
        return $this->qrUseList()
            ->whereHas('qrCode', function ($query) use ($artistName) {
                $query->where('qr_artist_name', $artistName)
                      ->where('qr_is_liveevent', true);
            })
            ->count();
    }

    /**
     * 全アーティストのライブイベント参加回数を取得
     */
    public function getAllArtistParticipationCounts()
    {
        $participationCounts = [];
        
        $artistEvents = $this->qrUseList()
            ->whereHas('qrCode', function ($query) {
                $query->where('qr_is_liveevent', true);
            })
            ->with('qrCode')
            ->get()
            ->groupBy('qrCode.qr_artist_name');

        foreach ($artistEvents as $artistName => $events) {
            $participationCounts[$artistName] = $events->count();
        }

        return $participationCounts;
    }
}
