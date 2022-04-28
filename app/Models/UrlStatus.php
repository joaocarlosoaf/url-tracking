<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UrlStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'url'
    ];
    
    protected $dates = ['status_verified_at', 'updated_at','created_at'];

    public static function getUrlsByUserId($user_id){

        $pessoas_details = DB::table('url_statuses AS url')
                                ->join('users AS user', 'url.user_id', '=', 'user.id')
                                ->select('url.id', 'url.url', 'url.status_code', 
                                            'url.body', 'url.status_verified_at')
                                ->where('url.user_id', $user_id)
                                        ->get()
                                        ->toArray();
        return $pessoas_details;

    }

}
