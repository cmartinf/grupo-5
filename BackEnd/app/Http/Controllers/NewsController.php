<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\News;
use App\Models\Tag;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        // Get query parameters
        $limit = $request->query('limit', 5); // Default limit: 5
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort by: 'created_at'
        $order = $request->query('order', 'desc'); // Default order: 'desc'
        $tag = $request->query('tag', null); // Optional tag filter

        // Fetch news with filters applied
        $newsQuery = News::with(['author', 'tags'])
            ->where('is_published', true); // Only fetch published news

        // Apply tag filter if tag is provided
        if ($tag) {
            $newsQuery->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            });
        }

        // Apply sorting and limit
        $news = $newsQuery->orderBy($sortBy, $order)->limit($limit)->get();

        return response()->json($news);
    }


    public function All()
    {
        $news = News::with(['author', 'tags'])
            ->get();
        return response()->json($news);
    }

    public function show($id)
    {
        $news = News::with(['author', 'tags'])->findOrFail($id);

        // Increment the views count
        $news->increment('views');

        return response()->json($news);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',  // Validate file type
            'category' => 'nullable|string',
            'authorId' => 'required|exists:authors,id',  // Validate author ID exists
            'tags' => 'nullable|string',  // Validate tags as a string (comma-separated)
        ]);

        // Handle image upload
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('images', 'public')  // Store in 'public/images'
            : null;

        // Create the news item
        $news = News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'author_id' => $validated['authorId'],
            'image' => $imagePath,
        ]);

        // Process tags
        if (!empty($validated['tags'])) {
            // Split the comma-separated tags into an array
            $tagsArray = array_map('trim', explode(',', $validated['tags']));

            $tagIds = collect($tagsArray)->map(function ($tagName) {
                // Find or create each tag and return its ID
                return Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['created_at' => now(), 'updated_at' => now()]
                )->id;
            })->toArray();

            // Sync tags with the news
            $news->tags()->sync($tagIds);
        }

        return response()->json($news->load('tags', 'author'));
    }



    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category' => 'nullable|string',
            'authorId' => 'required|exists:authors,id',
            'tags' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $news->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $validated['image'] ?? $news->image,
            'category' => $validated['category'],
            'author_id' => $validated['authorId'],
        ]);

        if (!empty($validated['tags'])) {
            $tags = json_decode($validated['tags'], true);

            $tagIds = collect($tags)->map(function ($tag) {
                return Tag::firstOrCreate(['name' => $tag['name']])->id;
            })->toArray();

            $news->tags()->sync($tagIds);
        } else {
            $news->tags()->detach();
        }

        return response()->json($news->load('tags', 'author'));
    }




    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return response()->json(['message' => 'News deleted successfully']);
    }

    public function addHeart(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $news->increment('heart_counts', 1);
        return response()->json(['message' => 'Heart count updated successfully', 'heart_counts' => $news->heart_counts]);
    }

    public function publish($id)
    {
        // Find the news item by ID
        $news = News::findOrFail($id);

        // Toggle the `is_published` status
        $news->is_published = !$news->is_published;

        // Optionally set `published_at` date when publishing
        if ($news->is_published) {
            $news->published_at = now();
        } else {
            $news->published_at = null;
        }

        // Save the updated status
        $news->save();

        // Return the updated news item as a response
        return response()->json([
            'message' => $news->is_published ? 'News published successfully' : 'News unpublished successfully',
            'news' => $news,
        ]);
    }
    public function newsByCategory()
    {
        // Group news by category, only fetching published news
        $news = News::with(['author', 'tags'])
            ->where('is_published', true)
            ->get()
            ->groupBy('category');

        // If no news is found, return an empty object
        if ($news->isEmpty()) {
            return response()->json(['message' => 'No news found'], 404);
        }

        return response()->json($news);
    }
}
