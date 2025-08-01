<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use App\Models\Blog;

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
            'image.image' => 'The file must be a valid image.',
            'image.max' => 'Max size is 2048',
        ]);

        $filename = time() . '_' . $request->file('image')->getClientOriginalName();
        $tempPath = 'blog_images/temp/' . $filename;

        $request->file('image')->move(public_path('blog_images/temp'), $filename);
        session()->push('pending_images', $tempPath);

        return redirect()->back()->with('image_uploaded', true)->withInput();
    }
}
