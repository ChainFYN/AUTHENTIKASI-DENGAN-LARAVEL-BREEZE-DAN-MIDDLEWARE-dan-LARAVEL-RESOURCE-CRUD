<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Mail\NotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function showForm()
    {
        return view('emails.send-form');
    }

    public function sendWelcome(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $user = (object) [
            'email' => $request->email,
            'name' => $request->name,
        ];

        Mail::to($request->email)->send(new WelcomeEmail($user));

        return back()->with('success', 'Email selamat datang berhasil dikirim ke: ' . $request->email);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        Mail::to($request->email)->send(new NotificationEmail(
            $request->title,
            $request->message
        ));

        return back()->with('success', 'Email notifikasi berhasil dikirim ke: ' . $request->email);
    }

    public function previewWelcome()
    {
        $user = (object) [
            'email' => 'user@example.com',
            'name' => 'John Doe',
        ];

        return view('emails.welcome', ['user' => $user]);
    }

    public function previewNotification()
    {
        return view('emails.notification', [
            'title' => 'Pemberitahuan Penting',
            'message' => 'Ini adalah contoh email notifikasi dari sistem Minimarket.',
            'data' => [],
        ]);
    }
}
