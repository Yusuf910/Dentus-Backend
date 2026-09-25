<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserModels\Booking;
use Laravel\Passport\HasApiTokens;
use App\Models\UserModels\Rating;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'mysql';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'last_name',
    //     'email',
    //     'password',
    //     'mobile',
    //     'status',
    //     'parent_id',
    //     'ip_address',
    //     'device_id',
    //     'device_token',
    //     'device_type',
        
    // ];

    protected $attributes = [
        'parent_id' => 0,
        'qrcode'=>''
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function UserInformationDetails() {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    function UserClinicDetails() {
        return $this->hasOne(UserEstablishmentClinic::class, 'user_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id')->where('status', 1);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'doctor_id'); // doctor_id is the foreign key in the bookings table
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'doctor_id', 'id')->where('status',1);
    }

    public function activepackage()
    {
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();
        $finalFeatures = [];
        $specialFeatureIds = [12, 13, 14, 15, 16, 17, 18];
        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features' => function ($q) {
                $q->orderBy('position', 'ASC');
            }])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');
            foreach ($subscription->features as $feature) {
                $pivotId = $feature->pivot->id;

                $addon = $premiumAddons->get($pivotId);
                $isSpecial = in_array($feature->id, $specialFeatureIds);

                $quantity = 0;
                $price = 0;
                $unlimited = 0;
                if ($alreadySubscribed->subscription_id == 9) {
                    if ($addon) {
                        $quantity = $addon->quantity;
                        $price = $addon->price;
                        $unlimited = $addon->unlimited;

                        if (in_array($feature->id, [15, 16, 17])) {
                            $unlimited = 1;
                        }
                    } elseif ($isSpecial) {
                        $quantity = $feature->pivot->basic_quantity ?? 0;
                        $price = 0;
                        $unlimited = 0;
                    } else {
                        $quantity = $feature->pivot->quantity ?? 0;
                        $price = $feature->pivot->price ?? 0;
                        $unlimited = $feature->pivot->unlimited ?? 0;
                    }
                } else {
                    $quantity = $feature->pivot->quantity ?? 0;
                    $price = $feature->pivot->price ?? 0;
                    $unlimited = $feature->pivot->unlimited ?? 0;
                }
                

                $finalFeatures[] = [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'position' => $feature->position,
                    'is_premium' => $addon ? 1 : 0,
                    'purchased' => $addon ? 1 : 0,
                    'quantity' => $quantity,
                    'price' => $price,
                    'unlimited' => $unlimited,
                    'addon' => $addon ?? null
                ];
            }
        } 
        return $finalFeatures;
    }

    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class, 'doctor_id', 'id');
    // }
}
