<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    public function position() {
        return $this->belongsTo(UserPosition::class, 'position_id');
    }

    public function comfortCategories() {
        return isset($this->position) ? $this->position->comfortCategories : collect([]);
    }

    public function carModels() {
        return $this->comfortCategories()->flatMap(function ($comfortCategory) {
            return $comfortCategory->carModels;
        });
    }

    public function bookedCars() {
        return $this->hasMany(CarBooking::class);
    }

    public function cars() {
        return $this->carModels()->flatMap(function ($carModel) {
            return $carModel->cars;
        });
    }

    public function findAvailableCars($startTime, $endTime, $modelId = null, $comfortCategoryId = null)
    {
        $comfortCategoryIds = $this->position ? $this->position->comfortCategories->pluck('id') : [];

        $query = Car::whereDoesntHave('bookings', function ($query) use ($startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_datetime', [$startTime, $endTime])
                  ->orWhereBetween('end_datetime', [$startTime, $endTime])
                  ->orWhere(function ($q1) use ($startTime, $endTime) {
                      $q1->where('start_datetime', '<', $startTime)
                        ->where('end_datetime', '>', $endTime);
                  });
            });
        });

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        if ($comfortCategoryId) {
            $query->whereHas('model', function ($query) use ($comfortCategoryId) {
                $query->where('comfort_category_id', $comfortCategoryId);
            });
        }

        if ($comfortCategoryIds->isNotEmpty()) {
            $query->whereHas('model', function ($query) use ($comfortCategoryIds) {
                $query->whereIn('comfort_category_id', $comfortCategoryIds);
            });
        }

        return $query->get();
    }
}
