<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{

    public function index(Request $request)
    {
        $query = Blog::with('user')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        $blogs = $query->paginate(10)->withQueryString();

        return view('blog.index', compact('blogs'));
    }

    public function byAuthor($id)
    {
        $author = User::findOrFail($id);

        $blogs = Blog::with('user')
            ->where('user_id', $id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('blog.by-author', compact('author', 'blogs'));
    }

    public function show(int $id)
    {
        $blog = Blog::with('images')->findOrFail($id);

        $comments = $blog->comments()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('blog.view', compact('blog', 'comments'));
    }

    public function create()
    {
        return view('blog.create');
    }

    public function store(Request $request)
    {
        $blog = Blog::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
        ]);

        $content = $request->input('content');

        foreach (session('pending_images', []) as $tempPath) {
            $filename = basename($tempPath);
            $newPath = 'blog-images/' . $filename;

            $sourcePath = public_path($tempPath);

            if (file_exists($sourcePath)) {
                $fileContent = file_get_contents($sourcePath);
                Storage::disk('public')->put($newPath, $fileContent);

                unlink($sourcePath);

                Image::create([
                    'path' => $newPath,
                    'alt' => 'autoloaded',
                    'blog_id' => $blog->id,
                ]);

                $tempUrl = asset($tempPath);                    // http://localhost:8000/blog_images/temp/filename.jpg
                $finalUrl = asset('storage/' . $newPath);       // http://localhost:8000/storage/blog-images/filename.jpg

                $content = str_replace($tempUrl, $finalUrl, $content);
            }
        }

        $blog->update(['content' => $content]);

        session()->forget('pending_images');

        return redirect()->route('blog.view', ['id' => $blog->id])
            ->with('success', 'Published!');
    }

    public function confirmDelete($id)
    {
        $blog = Blog::findOrFail($id);

        if (auth()->id() !== $blog->user_id && !auth()->user()->isAdmin()) {
            return redirect()->route('blogs')->with('error', 'You are not allowed to delete this post.');
        }

        return view('blog.confirm-delete', compact('blog'));
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if (auth()->id() !== $blog->user_id && !auth()->user()->isAdmin()) {
            return redirect()->route('blog.index')->with('error', 'Only author and admin can delete.');
        }

        $blog->delete();

        return redirect()->route('blog.index')->with('success', 'Blog post deleted.');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);

        if (auth()->id() !== $blog->user_id && !auth()->user()->isAdmin()) {
            return redirect()->route('blog.index')->with('error', 'Only author and admin can edit.');
        }

        session(['editing_blog_id' => $blog->id]);

        return view('blog.edit', compact('blog'));
    }

    public function update(Request $request)
    {
        $blogId = session('editing_blog_id');

        if (!$blogId) {
            return redirect()->route('blogs')->with('error', 'No blog in session. Edit session expired.');
        }

        $blog = Blog::findOrFail($blogId);

        if (auth()->id() !== $blog->user_id && !auth()->user()->isAdmin()) {
            return redirect()->route('blogs')->with('error', 'You are not authorized to update this post.');
        }

        $blog->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        $content = $request->input('content');

        foreach (session('pending_images', []) as $tempPath) {
            $filename = basename($tempPath);
            $newPath = 'blog-images/' . $filename;
            $sourcePath = public_path($tempPath);

            if (file_exists($sourcePath)) {
                Storage::disk('public')->put($newPath, file_get_contents($sourcePath));
                unlink($sourcePath);

                Image::create([
                    'path' => $newPath,
                    'alt' => 'updated',
                    'blog_id' => $blog->id,
                ]);

                $content = str_replace(asset($tempPath), asset('storage/' . $newPath), $content);
            }
        }

        $blog->update(['content' => $content]);

        session()->forget(['pending_images', 'editing_blog_id']);

        return redirect()->route('blog.view', $blog->id)->with('success', 'Updated!');
    }

    public function dashboard(Request $request)
    {
        $title = $request->input('title');
        $author = $request->input('author');

        $blogs = Blog::with('user')
            ->when($title, fn($q) => $q->where('title', 'like', "%{$title}%"))
            ->when($author, fn($q) => $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$author}%")))
            ->latest()
            ->paginate(10);

        return view('dashboard.posts', compact('blogs', 'title', 'author'));
    }
 
}
