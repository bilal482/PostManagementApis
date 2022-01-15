<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->account_type == 'admin') {
            $posts = Post::latest()->with('user')->get();
        }else{
            $posts = Post::where('user_id',$user->id)->with('user')->get();
        }

        //return success response
        return response()->json([
            'success' => true,
            'posts' => $posts,
            'user' => $user,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate data
        $data = $request->only('title', 'description');
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        
        $approve = false;
        if ($request->user()->account_type == "admin") {
            $approve = true;
        }
        // Create new post
        $post = Post::create([
            'title' => $request->title,
        	'description' => $request->description,
            'user_id' => $request->user()->id,
            'is_approve' => $approve,
        ]);

        //return response
        if ($post) {
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully.',
            ], 200);
        }
        //return error response
        return response()->json([
            'success' => false,
            'message' => 'Something wrong! Please try again.',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $user = Auth::user();
        // if you need user can't view after approve then uncommit this condition then it'll work 
        // But as far as my understanding, the post is being viewed in all the cases.
        //return response
        // if (($user->account_type == 'user' && $post->is_approve == 0) || $user->account_type == 'admin') {
            return response()->json([
                'success' => true,
                'post' => $post,
            ], 200);
        // }
        //return error response
        // return response()->json([
        //     'success' => false,
        //     'message' => 'Something wrong! Please try again.',
        // ], 400);
    }

    /**
     * Post approve
     *
     */
    public function approve(Post $post)
    {
        $user = Auth::user();
        if ($user->account_type == 'admin') {
            // approve post
            $approve = !$post->is_approve;
            $post->update([
                'is_approve' => $approve,
            ]);

            //return success response
            return response()->json([
                'success' => true,
                'message' => 'Post approved successfully.',
            ], 200);
        }

        //return error response
        return response()->json([
            'success' => false,
            'message' => 'Something wrong! Please try again.',
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();
        // edit for user => when post is not approved and admin can edit the post.
        if (($user->account_type == 'user' && $post->is_approve == 0) || $user->account_type == 'admin') {

            //Validate data
            $data = $request->only('title', 'description');
            $validator = Validator::make($data, [
                'title' => 'required|string',
                'description' => 'required'
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
            
            // update post
            $post->update($request->only([
                'title',
                'description',
            ]));

            //return response
            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully.',
            ], 200);
        }

        //return error response
        return response()->json([
            'success' => false,
            'message' => 'Something wrong! Please try again.',
        ], 400);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $user = Auth::user();
        if ($user->account_type == 'admin') {
            $post->delete();

            //return success response
            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully.',
            ], 200);
        }

        //return error response
        return response()->json([
            'success' => false,
            'message' => 'Something wrong! Please try again.',
        ], 400);
    }
}
