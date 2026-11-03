<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\MeetingVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MeetingVideoController extends Controller
{
    /**
     * Store a video link for a meeting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'meeting_id' => ['required', 'integer', 'exists:meetings,id'],
            'title' => ['required', 'string', 'max:180'],
            'url' => ['required', 'url', 'max:2048'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $url = $validated['url'];
        $provider = 'other';

        if (preg_match('/(?:youtu\.be\/|youtube\.com)/i', $url)) {
            $provider = 'youtube';
        } elseif (preg_match('/tiktok\.com/i', $url)) {
            $provider = 'tiktok';
        } elseif (preg_match('/instagram\.com/i', $url)) {
            $provider = 'instagram';
        } elseif (preg_match('/drive\.google\.com/i', $url)) {
            $provider = 'drive';
        }

        $validated['provider'] = $provider;
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        MeetingVideo::create($validated);

        notify()->success()->title('Berhasil')->message('Materi video berhasil ditambahkan.')->send();

        return back()->with('success', 'Materi video berhasil ditambahkan.');
    }

    /**
     * Update video.
     */
    public function update(Request $request, MeetingVideo $video): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'url' => ['required', 'url', 'max:2048'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $url = $validated['url'];
        $provider = 'other';

        if (preg_match('/(?:youtu\.be\/|youtube\.com)/i', $url)) {
            $provider = 'youtube';
        } elseif (preg_match('/tiktok\.com/i', $url)) {
            $provider = 'tiktok';
        } elseif (preg_match('/instagram\.com/i', $url)) {
            $provider = 'instagram';
        } elseif (preg_match('/drive\.google\.com/i', $url)) {
            $provider = 'drive';
        }

        $validated['provider'] = $provider;
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['updated_by'] = $request->user()->id;

        $video->update($validated);

        notify()->success()->title('Berhasil')->message('Materi video berhasil diperbarui.')->send();

        return back()->with('success', 'Materi video berhasil diperbarui.');
    }

    /**
     * Delete video.
     */
    public function destroy(MeetingVideo $video): RedirectResponse
    {
        $video->delete();

        notify()->success()->title('Berhasil')->message('Materi video berhasil dihapus.')->send();

        return back()->with('success', 'Materi video berhasil dihapus.');
    }
}
