<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        // $posts = Post::latest();

        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'title' => 'required',
            'content' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);

        return new PostResource(true, 'Detail Data Post.', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);

        if($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            Storage::delete('public/posts/' . basename($post->image));

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        Storage::delete('public/posts/' . basename($post->image));

        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus!', 200);
    }
}
