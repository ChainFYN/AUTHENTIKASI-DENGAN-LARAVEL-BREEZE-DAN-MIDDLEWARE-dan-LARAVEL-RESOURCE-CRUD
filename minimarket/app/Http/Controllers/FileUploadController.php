<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function showForm()
    {
        return view('file-upload.form');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,png,pdf,doc,docx|max:2048',
        ]);

        $path = $request->file('document')->store('documents', 'public');

        return back()->with('success', 'File berhasil diunggah: ' . $path);
    }

    public function uploadCustomName(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('documents', $filename, 'public');

        return back()->with('success', 'File tersimpan di: ' . $path);
    }

    public function listFiles()
    {
        $files = Storage::disk('public')->files('documents');
        return view('file-upload.list', ['files' => $files]);
    }

    public function deleteFile($filename)
    {
        Storage::disk('public')->delete('documents/' . $filename);
        return back()->with('success', 'File dihapus: ' . $filename);
    }
}
