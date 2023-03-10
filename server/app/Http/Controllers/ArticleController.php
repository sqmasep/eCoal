<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Article::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $role = $request->user()->role;
        if ($role == 'ADMIN') {
            if ($request->input('mediaType') == 'VIDEO') {
                $request->validate([
                    'title' => 'required',
                    'content' => 'required',
                    'thumbnailURL' => 'required',
                    'mediaType' => 'required',
                    'mediaURL' => 'file|required',
                    'leadStory' => 'required',
                    'tags' => 'required'
                ]);
            } else {
                $request->validate([
                    'title' => 'required',
                    'content' => 'required',
                    'mediaType' => 'required',
                    'mediaURL' => 'file|required',
                    'leadStory' => 'required',
                    'tags' => 'required'
                ]);
            }
            //Make the media
            $media = $request->file('mediaURL')->hashName();
            $request->file('mediaURL')->move('upload', $media);
            //Make thumbnailURL
            if ($request->input('mediaType') == 'IMAGE') {
                $thumbnailURL = $media;
            } else if ($request->input('mediaType') == 'VIDEO') {//In React do that : If mediaType == VIDEO then make thumbnailURL required
                $thumbnailURL = $request->input('thumbnailURL');
            }

            $newArticle = Article::create([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'thumbnailURL' => $thumbnailURL,
                'mediaType' => $request->input('mediaType'),
                'mediaURL' => $media,
                'leadStory' => $request->input('leadStory')
            ]);
            $newArticle->tags()->attach(
                $request->input('tags')->foreach(function ($tag) {
                    return Tag::where('name', $tag)->first()->id;
                })
            );
            return response($newArticle, 201);
        } else {
            return response()->json(['message' => 'You are not an admin'], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $upCount = Article::find($id);
        $upCount->viewCount = $upCount->viewCount + 1;
        $upCount->save();
        return Article::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnailURL' => 'required',
            'mediaType' => 'required',
            'mediaURL' => 'required',
            'leadStory' => 'required',
        ]);
        $articleUpdate = Article::find($id);
        $articleUpdate->title = $request->input('title');
        $articleUpdate->content = $request->input('content');
        $articleUpdate->thumbnailURL = $request->input('thumbnailURL');
        $articleUpdate->mediaType = $request->input('mediaType');
        $articleUpdate->mediaURL = $request->input('mediaURL');
        $articleUpdate->leadStory = $request->input('leadStory');
        $articleUpdate->save();
        return response($articleUpdate, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $articleDelete = Article::find($id);
        $articleDelete->delete();
        return response(null, 204);
    }

    public function searchFunction(string $searchContent)
    {
        return Article::whereRaw("title like '%'||?||'%'", [$searchContent])->get(); // Search Data from $searchContent and
    }

    public function searchFunctionByTag(string $tagId)//Doesn't work
    {
        $tag = Tag::find($tagId);
        return $tag->articles;
    }
}
