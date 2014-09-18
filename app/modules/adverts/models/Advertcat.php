<?php namespace App\Modules\Adverts\Models;

use SoftDeletingTrait, BaseModel;

class Advertcat extends BaseModel {

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = ['title'];
    
    public static $rules = [
        'title'   => 'required',
    ];
}