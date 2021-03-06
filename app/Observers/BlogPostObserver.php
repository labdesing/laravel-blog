<?php

namespace App\Observers;

use App\Models\BlogPost;
use Carbon\Carbon;
use Str;

class BlogPostObserver
{
    /**
     * Обработка ПЕРЕД созданием записи.
     *
     * @param  BlogPost  $blogPost
     */
    public function creating(BlogPost $blogPost)
    {
        $this->setPublishedAt($blogPost);

        $this->setSlug($blogPost);

        $this->setHtml($blogPost);

        $this->setUser($blogPost);

    }

    /**
     * Обработка ПЕРЕД обновлением записи.
     *
     * @param BlogPost  $blogPost
     */
    public function updating(BlogPost $blogPost)
    {
//        $test[] = $blogPost->isDirty();
//        $test[] = $blogPost->isDirty('is_published');
//        $test[] = $blogPost->isDirty('user_id');
//        $test[] = $blogPost->getAttribute('is_published');
//        $test[] = $blogPost->is_published;
//        $test[] = $blogPost->getOriginal('is_published');
//        dd($test);

        $this->setPublishedAt($blogPost);

        $this->setSlug($blogPost);
    }

    /**
     * Если дата публикации не установлена и происходит установка флага - Опубликовано,
     * то устанавливаем дату публикации на текущую.
     *
     * @param  BlogPost  $blogPost
     */
    public function setPublishedAt(BlogPost $blogPost)
    {
        $needPublished = empty($blogPost->published_at) && $blogPost->is_published;
        if ($needPublished) {
            $blogPost->published_at = Carbon::now();
        }
    }

    /**
     * Если поле слаг пустое, то заполняем его конвертацией заголовка.
     *
     * @param  BlogPost  $blogPost
     */
    public function setSlug(BlogPost $blogPost)
    {
        if (empty($blogPost->slug)) {
            $blogPost->slug = str::slug($blogPost->title);
        }
    }

    /**
     * Установка значения полю content_html относительно поля content_raw.
     *
     * @param BlogPost  $blogPost
     */
    public function setHtml(BlogPost $blogPost)
    {
        if ($blogPost->isDirty('content_raw')) {
            // TODO тут должна быть генерация markdown -> html
            $blogPost->content_html = $blogPost->content_raw;
        }
    }

    /**
     * Если не указан User_id устанавливаем пользователя по-умолчанию.
     *
     * @param BlogPost  $blogPost
     */
    public function setUser(BlogPost $blogPost)
    {
        $blogPost->user_id = auth()->id() ?? BlogPost::UNKNOWN_USER;
    }

    /**
     * @param  \App\Models\BlogPost  $blogPost
     */
    public function deleting(BlogPost $blogPost)
    {
        // dd(__METHOD__, $blogPost);
        // return false;

    }

    /**
     * Handle the blog post "deleted" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function deleted(BlogPost $blogPost)
    {
       // dd(__METHOD__, $blogPost);
    }

    /**
     * Handle the blog post "restored" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function restored(BlogPost $blogPost)
    {
        //
    }

    /**
     * Handle the blog post "force deleted" event.
     *
     * @param  \App\Models\BlogPost  $blogPost
     * @return void
     */
    public function forceDeleted(BlogPost $blogPost)
    {
        //
    }
}
