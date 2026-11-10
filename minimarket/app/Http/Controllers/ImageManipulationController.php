<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\GdDriver;

class ImageManipulationController extends Controller
{
    private $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new GdDriver());
    }

    public function showForm()
    {
        return view('image-manipulation.form');
    }

    public function resize(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'width' => 'required|integer|min:50|max:1000',
            'height' => 'required|integer|min:50|max:1000',
        ]);

        $file = $request->file('image');
        $image = $this->manager->read($file);
        $image->resize($request->width, $request->height);
        
        $filename = 'resized_' . time() . '.' . $file->getClientOriginalExtension();
        $path = storage_path('app/public/images/' . $filename);
        
        if (!is_dir(storage_path('app/public/images'))) {
            mkdir(storage_path('app/public/images'), 0755, true);
        }
        
        $image->save($path);

        return back()->with('success', 'Gambar berhasil di-resize: ' . $filename)
                    ->with('filename', $filename);
    }

    public function crop(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'width' => 'required|integer|min:50|max:500',
            'height' => 'required|integer|min:50|max:500',
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
        ]);

        $file = $request->file('image');
        $image = $this->manager->read($file);
        $image->crop($request->width, $request->height, $request->x, $request->y);
        
        $filename = 'cropped_' . time() . '.' . $file->getClientOriginalExtension();
        $path = storage_path('app/public/images/' . $filename);
        
        if (!is_dir(storage_path('app/public/images'))) {
            mkdir(storage_path('app/public/images'), 0755, true);
        }
        
        $image->save($path);

        return back()->with('success', 'Gambar berhasil di-crop: ' . $filename)
                    ->with('filename', $filename);
    }

    public function rotate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'angle' => 'required|integer|min:0|max:360',
        ]);

        $file = $request->file('image');
        $image = $this->manager->read($file);
        $image->rotate($request->angle);
        
        $filename = 'rotated_' . time() . '.' . $file->getClientOriginalExtension();
        $path = storage_path('app/public/images/' . $filename);
        
        if (!is_dir(storage_path('app/public/images'))) {
            mkdir(storage_path('app/public/images'), 0755, true);
        }
        
        $image->save($path);

        return back()->with('success', 'Gambar berhasil di-rotate: ' . $filename)
                    ->with('filename', $filename);
    }

    public function listImages()
    {
        $path = storage_path('app/public/images');
        $images = [];

        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $images[] = $file;
                }
            }
        }

        return view('image-manipulation.list', ['images' => $images]);
    }

    public function deleteImage($filename)
    {
        $path = storage_path('app/public/images/' . $filename);
        
        if (file_exists($path)) {
            unlink($path);
            return back()->with('success', 'Gambar dihapus: ' . $filename);
        }

        return back()->with('error', 'Gambar tidak ditemukan');
    }
}
