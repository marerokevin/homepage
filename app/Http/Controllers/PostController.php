<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the posts OR a single post within the updates view.
     */
    public function index(Request $request, $slug = null)
    {
        // 1. Fetch archives using event_date instead of created_at for better accuracy
        $archives = Post::selectRaw('year(event_date) as year, monthname(event_date) as month, count(*) as post_count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->get();

        $singlePost = null;
        $posts = null;

        // 2. Determine if we are showing a single article or the list
        if ($slug) {
            $singlePost = Post::with('user')->where('slug', $slug)->firstOrFail();
        } else {
            // Logic for the List View - Ordered by event_date
            $query = Post::with('user')->orderBy('event_date', 'desc');

            // Apply Month/Year filters based on event_date
            if ($request->filled('year')) {
                $query->whereYear('event_date', $request->year);
            }
            if ($request->filled('month')) {
                $monthNumber = date('m', strtotime($request->month));
                $query->whereMonth('event_date', $monthNumber);
            }

            $posts = $query->paginate(9)->withQueryString();
        }

        return view('updates', compact('posts', 'archives', 'singlePost'));
    }

    /**
     * Store a newly created post in storage (Supports Multiple Images).
     */
    public function store(Request $request)
    {
        // Validation: Note the 'image.*' to validate every file in the array
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tags' => 'nullable|string|max:255' // <-- New
        ]);

        // 1. Handle Multiple Image Uploads
        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                // Stores each image and adds path to our array
                $imagePaths[] = $file->store('posts', 'public');
            }
        }

        $tags = $request->tags
        ? array_filter(array_map('trim', explode(',', $request->tags)))
        : [];

        // 2. Generate Unique Slug
        $slug = Str::slug($request->title) . '-' . rand(100, 999);

        // 3. Create the Post
        $request->user()->posts()->create([
            'title'      => $request->title,
            'body'       => $request->body,
            'event_date' => $request->event_date ?? now()->format('Y-m-d'),
            'slug'       => $slug,
            'image'      => $imagePaths, // This will be saved as JSON thanks to the $casts in Post model
            'tags' => $tags, // <-- Save tags here
        ]);

        return redirect()->back()->with('success', 'Post created successfully with ' . count($imagePaths) . ' images!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $post->delete();

        return redirect()->route('updates')->with('success', 'Post deleted!');
    }

    // update
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'image.*' => 'nullable|image|max:2048',
            'tags' => 'nullable|string|max:255' // <-- New
        ]);

        $post->update($request->only('title', 'body', 'event_date', 'location'));

        // Handle tags
        $post->tags = $request->tags
            ? array_filter(array_map('trim', explode(',', $request->tags)))
            : [];
        $post->save();


        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $path = $file->store('posts', 'public');
                $post->image = array_merge($post->image ?? [], [$path]);
            }
            $post->save();
        }

        return redirect()->back()->with('success', 'Post updated successfully!');
    }

}
