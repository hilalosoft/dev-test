<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Bookmark extends Model
{
    protected $fillable = [
        'title',
        'url',
        'domain',
        'description',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function archive(){
        return DB::transaction(function () {
            $now = now();
            $archived = static::whereKey($this->getKey())->whereNull('archived_at')->update(['archived_at' => $now]);

            if ($archived === 0) {
                return false;
            }
            Tag::whereIn('id', $this->tags()->pluck('tags.id'))->decrement('bookmarks_count');

            $this->archived_at = $now;

            return true;
        });
    }

    public function unarchive(){
        $now = null;
            $restored = static::whereKey($this->getKey())->whereNotNull('archived_at')->update(['archived_at' => $now]);

            if ($restored === 0) {
                return false;
            }
            Tag::whereIn('id', $this->tags()->pluck('tags.id'))->increment('bookmarks_count');

            $this->archived_at = $now;

            return true;
        
    }
}
