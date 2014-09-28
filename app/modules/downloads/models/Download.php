<?php namespace App\Modules\Downloads\Models;

use InterImage, File, SoftDeletingTrait, BaseModel;

class Download extends BaseModel {

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];

    protected $slugable = true;

    protected $fillable = ['title', 'description', 'downloadcat_id'];

    public static $fileHandling = ['file'];

    protected $rules = [
        'title'             => 'required',
        'downloadcat_id'    => 'required|integer'
    ];

    public static $relationsData = [
        'downloadcat'   => [self::BELONGS_TO, 'App\Modules\Downloads\Models\Downloadcat'],
        'creator'       => [self::BELONGS_TO, 'User', 'title' => 'username'],
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function($download)
        {
            $fileName = $download->uploadPath(true).$download->file;
            if (File::isFile($fileName)) {
                $download->file_size = File::size($fileName); // Save file size
                
                $imgsize = getimagesize($fileName); // Try to gather infos about the image 
                if ($imgsize[2]) {
                    $download->is_image = true;

                    /*
                     * Create Thumbnail
                     */
                    $size = 50;
                    InterImage::make($fileName)->resize($size, $size, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($download->uploadPath(true).$size.'/'.$download->file); 
                }
            }
        });
    }

    /**
     * Count the comments that are related to this download.
     * 
     * @return int
     */
    public function countComments()
    {
        return Comment::count('downloads', $this->id);
    }

}