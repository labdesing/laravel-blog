<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BlogCategory
 *
 * @package App\Models
 *
 * @property-read BlogCategory $parentCategory
 * @property-read string       $parentTitle
 */
class BlogCategory extends Model
{
    use softDeletes;

    /**
     * ID корень
     */

    const ROOT = 1;

    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'description',
    ];

    /**
     *  Получить родительскую категорию.
     *
     * @return BlogCategory
     */
    public function parentCategory()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id', 'id');
    }

    /**
     * Пример аксесуара (Accessor)
     *
     * @url https://laravel.com/docs/5.8/eloquent-mutators
     */
    public function getParentTitleAttribute()
    {
        $title = $this->parentCategory->title
            ?? ($this->isRoot()
                ? 'Корень'
                :'???');
        return $title;
    }

    /**
     * Является ли текущий обьект корневым
     *
     * @return bool
     */
    public function isRoot()
    {
        return $this->id === BlogCategory::ROOT;
    }

//    /**
//     * Пример аксесуара.
//     *
//     * @param string $valueFormDB
//     *
//     * @return bool|mixed|null|string|string[]
//     */
//    public function getTitleAttribute($valueFormObject)
//    {
//        return mb_strtoupper($valueFormObject);
//    }
//
//    /**
//     * Пример мутатора.
//     *
//     * @param  string  $incomingValue
//     */
//    public function setTitleAttribute($incomingValue)
//    {
//        $this->attributes['title'] = mb_strtolower($incomingValue);
//    }
}
