<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    public $translatedAttributes = ['name','description'];
    protected $fillable=['category_id','image','stock','purchase_price','sale_price'];

    protected $appends=['image_path','profit_percent'];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function getImagePathAttribute(){
        return asset('uploads/products/'.$this->image);
    }
    public function getProfitPercentAttribute(){
        $profit=$this->sale_price-$this->purchase_price;
        return number_format($profit *100 / $this->purchase_price,2);
    }
}
