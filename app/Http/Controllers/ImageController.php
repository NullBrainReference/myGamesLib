<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use App\Models\Blog;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image'    => 'required|image|max:2048',
            'blog_id'  => 'required|exists:blogs,id',
        ]);

        $path = $request->file('image')->store('blog_images', 'public');

        $image = Image::create([
            'path'     => $path,
            'alt'      => 'Your alt text here',
            'blog_id'  => $request->blog_id,
        ]);

        return back()->with('imagePath', asset('storage/' . $path));
    }

    public function uploadTempImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ], [
            'image.required' => 'Please upload an image.',
            'image.image'    => 'The file must be a valid image.',
            'image.max'      => 'Max size is 2048',
        ]);

        $file = $request->file('image');

        // Extract name and extension separately
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension    = $file->getClientOriginalExtension();

        // Sanitize spaces and symbols into hyphens (e.g., "like this" -> "like-this")
        $safeName = Str::slug($originalName) . '.' . $extension;
        $filename = time() . '_' . $safeName;

        // Save using the 'public' disk so Storage::disk('public')->delete() can track it
        $tempPath = $file->storeAs('blog_images/temp', $filename, 'public');

        session()->push('pending_images', $tempPath);

        return redirect()->back()->with('image_uploaded', true)->withInput();
    }

    public function clearTempImages()
    {
        session()->forget('pending_images');
        return back();
    }

    public function deleteTempImage($index)
    {
        $pending = session('pending_images', []);
        if (isset($pending[$index])) {
            Storage::disk('public')->delete($pending[$index]);
            unset($pending[$index]);
            session(['pending_images' => array_values($pending)]);
        }
        return back();
    }
}
