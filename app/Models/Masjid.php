<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Masjid extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'lat', 'long', 'img', 'type_id', 'facilities', 'phone', 'operating_start', 'operating_end', 'address'];
    protected $casts = [
        'facilities' => 'array',
    ];

    public function reviews()
    {
        $this->hasMany(MasjidReview::class);
    }

    public function type()
    {
        $this->belongsTo(MasjidType::class);
    }

    //start #mobreq
    protected $appends =  ['category_name', 'allphotos', 'review_avg','review_count'];

    public function getAllphotosAttribute()
    {
        $masjidReviews = MasjidReview::where("masjid_id", '=', $this->id)->get();
        $arrayPhotoUrl = array();
        array_push($arrayPhotoUrl, $this->img);
        foreach ($masjidReviews as $item) {
            $masjidPhotos = MasjidReviewImage::where("review_id", '=', $item->id);
            foreach ($masjidPhotos as $itemPhoto) {
                array_push($arrayPhotoUrl, $itemPhoto->path);
            }
        }
        return $arrayPhotoUrl;
    }

    public function getCategoryNameAttribute()
    {
        $categoryName = "";
        $category = MasjidType::all();
        $masjidType = $this->type_id;
        foreach ($category as $cat) {

            if ($masjidType == $cat->id) {
                $categoryName = $cat->name;
            }
        }

        return $categoryName;
    }

    public function getReviewCountAttribute(){
        $masjidReviews = MasjidReview::where("masjid_id", '=', $this->id)->get();

        return strval($masjidReviews->count());
    }

    public function getReviewAvgAttribute()
    {
        $masjidReviews = MasjidReview::where("masjid_id", '=', $this->id)->get();
        $object = new stdClass();

        $ratings1 = 0;
        $ratings2 = 0;
        $ratings3 = 0;
        $ratings4 = 0;
        $ratings5 = 0;

        foreach ($masjidReviews as $data) {
            if ($data->rating_id == 1) {
                $ratings1 += 1;
            }
            if ($data->rating_id == 2) {
                $ratings2 += 1;
            }
            if ($data->rating_id == 3) {
                $ratings3 += 1;
            }
            if ($data->rating_id == 4) {
                $ratings4 += 1;
            }
            if ($data->rating_id == 5) {
                $ratings5 += 1;
            }
        }

        $avg = ($ratings1 + $ratings2 + $ratings3 + $ratings4 + $ratings5) / 5.0;
        $object->avg = $avg;
        $object->rating1 = $ratings1;
        $object->rating2 = $ratings2;
        $object->rating3 = $ratings3;
        $object->rating4 = $ratings4;
        $object->rating5 = $ratings5;
        return strval($avg);
    }

    //end #mobreq

}
