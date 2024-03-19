<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = $this->searchPost($request);

            return response()->json([
                'status_code' => 200,
                'message' => 'Liste des posts.',
                'items' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function store(PostRequest $request)
    {
        try {
            $valdData = $request->validated();
            $valdData['user_id'] = auth()->user()->id;
            Post::create($valdData);
            // Post::create($request->validated());
    
            return response()->json([
                'status_code' => 200,
                'message' => 'Le post a été ajouté.',
                'data' => Post::get()->last() // all()->last()
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function update(PostRequest $request, Post $post)
    {
        try {
            //$post = Post::findOrFail($post);
            $ValdData = $request->validated();

            if ($post->user_id === auth()->user()->id) {
                $post->update($ValdData);                
            } else {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Vous n\'est pas l\'auteur de ce post.',
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Le post a été modifié.',
                'data' => Post::get()->last()
            ]);

        } catch (Exception $e) {
            return response()->json($e);
        }

    }

    public function delete(Post $post)
    {
        try {
            if ($post->user_id === auth()->user()->id) {
                $post->delete();              
            } else {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Vous n\'est pas l\'auteur de ce post. Suppresion non autorisée.',
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Le post a été supprimé.',
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function searchPost(Request $request)
    {
        $query = Post::query();
        $perPage = 2;
        $page = $request->input('page', 1);
        $search = $request->input('search');

        if ($search) {
            $query->whereRaw("title LIKE '%" . $search . "%'");
        }

        $total = $query->count();
        $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return [
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'result' => $result,
        ];
    }
}
