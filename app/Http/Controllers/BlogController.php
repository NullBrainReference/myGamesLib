<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function show(int $id)
    {
        $blog = Blog::with('images')->findOrFail($id);

        return view('blog.view', compact('blog'));
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

    
}
