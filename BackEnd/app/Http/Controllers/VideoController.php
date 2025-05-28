<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::all();
        return response()->json($videos, 200);
    }

    public function upload(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'video' => 'required|mimes:mp4,mov,avi,wmv', // Limit to 20MB
            'title' => 'nullable|string|max:255',
        ]);

        try {
            // Store the uploaded video in the 'videos' directory
            $path = $request->file('video')->store('videos', 'public');

            // Generate a public URL for the uploaded video
            $url = Storage::url($path);

            // Save video details in the database
            $video = Video::create([
                'title' => $request->title,
                'path' => $path,
                'url' => asset($url),
            ]);

            // Return success response with the video URL
            return response()->json([
                'success' => true,
                'url' => $video->url,
                'video' => $video,
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors during the upload process
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload video.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
