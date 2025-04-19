<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\UserNotification;

class PostController extends Controller
{
    public function post_form()
    {
        return view('post-form');
    }

    public function post_create(Request $request)
    {  
        $request->validate([
            'title'=>'required',
            'description'=>'required',
        ]);
        Post::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // $user = User::where('id',2)->first();
        $users = User::all();
        $message = 'You have a new post';
        foreach($users as $user){
            UserNotification::create([
                'user_id' => $user->id,
                'message' => $message,
            ]);
        event(new NotificationSent($message,$user->id));
            
        }

        // event(new NotificationSent($message,$user->id));
       return back();
    }
}
