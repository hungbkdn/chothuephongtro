<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\BeforeUpdate;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use BeforeUpdate;

    const ITEMS_PER_PAGE = 10;
    const MALE = 1;
    const FEMALE = 0;
    const ADMIN = 1;
    const DEFAULT_PASSWORD = 'abc123';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'image', 'birthday', 'address', 'reset_password_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'reset_password_token'
    ];

    public function posts()
    {
        return $this->hasMany('App\Post', 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'user_id', 'id');
    }

    /**
     * This is a recommended way to declare event handlers
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Register a deleting model event with the dispatcher.
         *
         * @param \Closure|string $callback
         *
         * @return void
         */
        static::deleting(function ($user) {
            $user->posts()->delete();
            $user->comments()->delete();
        });
    }

    public function countDeletedPosts()
    {
        $postsCount = DB::table('posts')->where('user_id', $this->id)->whereNotNull('deleted_at')->count();

        return $postsCount;
    }

    public static function generateResetPasswordCode()
    {
        return str_random(40);
    }
}
