<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    private const PER_PAGE = 20;

    /**
     * Paginated bookmark list.
     *
     * Query string:
     *   ?search=  free text, matches the title or the url
     *   ?tag=     tag slug
     *   ?page=    1-based page number
     */
    public function index(Request $request): JsonResponse
    {
        $bookmarks = Bookmark::query()
            ->with('tags')
            ->when($request->boolean('archived'), 
                fn ($q) => $q->whereNotNull('archived_at'),
                fn ($q) => $q->whereNull('archived_at')
            )
            ->when($request->input('search'), fn ($q, $term) => $q->where(
                fn ($q) => $q->where('title', 'like', "%{$term}%")
                    ->orWhere('url', 'like', "%{$term}%")
            ))
            ->when($request->input('tag'), fn ($q, $tag) => $q->whereHas(
                'tags',
                fn ($q) => $q->where('tags.slug', $tag)
            ))
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);
            // ->simplePaginate(self::PER_PAGE);

        return response()->json([
        'data' => BookmarkResource::collection($bookmarks->items()),
        'meta' => [
            'current_page' => $bookmarks->currentPage(),
            'per_page' => $bookmarks->perPage(),
            'total' => $bookmarks->total(),
            'last_page' => $bookmarks->lastPage(),
        ],
        ]);

        // return response()->json([
        //     'data' => BookmarkResource::collection($bookmarks->items()),
        //     'meta' => [
        //         'current_page' => $bookmarks->currentPage(),
        //         'per_page' => $bookmarks->perPage(),
        //         'has_more_pages' => $bookmarks->hasMorePages(),
        //     ],
        // ]);
    }
    public function archive(Bookmark $bookmark){
        $bookmark->archive();
    }
    public function unarchive(Bookmark $bookmark){
        $bookmark->unarchive();
    }
}
