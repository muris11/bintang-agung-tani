<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function unread()
    {
        $messages = ContactMessage::unread()->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);

        if (!$message->is_read) {
            $message->markAsRead();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();

        return redirect()->back()->with('success', 'Pesan ditandai telah dibaca');
    }

    public function markAllAsRead()
    {
        ContactMessage::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Semua pesan ditandai telah dibaca');
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Pesan dihapus');
    }

    public function getUnreadCount()
    {
        $count = ContactMessage::unread()->count();

        return response()->json(['count' => $count]);
    }
}
