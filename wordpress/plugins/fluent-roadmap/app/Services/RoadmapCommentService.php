<?php

namespace FluentRoadmap\App\Services;

use stdClass;
use Illuminate\Http\Request;
use FluentRoadmap\App\Models\TaskComment;
use FluentRoadmap\App\Models\TaskActivity;
use FluentRoadmap\App\Models\Idea;

class RoadmapCommentService
{
    public function commentRoadmap($data)
    {
        $current_user = wp_get_current_user();
        $task = Idea::find($data['task_id']);

        $settingData = [];
        if(!$current_user->ID){
            $settingData = array(
                'author' => [
                    'name' => $current_user->ID ? $current_user->display_name : $data['user_name'],
                    'email' => $current_user->ID ? $current_user->user_email : $data['user_email']
                ],
            ); 
        }

        $comment = TaskComment::create([
            'user_id'   => $current_user->ID ? $current_user->ID : null,
            'board_id'  => $task['board_id'],
            'task_id'   => $data['task_id'],
            'message'   => $data['comment'],
            'type'      => 'task',
            'status'    => 'published',
            'extra'     => !$current_user->ID ? serialize($settingData) : null,
        ]);
        
        return $comment;
    }
    public function fetchAllComment($datas, $task_id, $user)
    {
        $selectedSortSearch = $datas->selectSortSearch;
        $perPage   = $datas->per_page;
        
        $comments = TaskComment::where('task_id', $task_id)
                                ->where('type', 'task')
                                ->where('status', 'published')
                                ->when($selectedSortSearch, function ($query) use ($selectedSortSearch) {
                                    if ($selectedSortSearch == 'asc') {
                                        return $query->orderBy('id', 'asc');
                                    } elseif ($selectedSortSearch == 'desc') {
                                        return $query->orderBy('id', 'desc');
                                    }
                                })
                                ->with(['user' => function ($query) {
                                    $query->select('ID','display_name','user_email');
                                },'commentReactions' => function ($query) use ($user) {
                                    $query->where('user_id', $user->ID)
                                          ->where('object_type','comment')
                                          ->where('user_id', '!=', 0);
                                }])
                                ->orderBy('id','desc')
                                ->paginate($perPage);

        foreach($comments as $comment){
            if(!$comment->user && $comment->user_id == 0){
                $comment->extra = unserialize($comment->extra);
                $comment->administrator = false;
                $displayName = $comment->extra['author']['name'];
                // Generate an avatar with the first character of the first name
                $first_initial = mb_substr($displayName, 0, 1, 'UTF-8');
                $size = 128;
                $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($displayName))) . "?s={$size}&d=" . urlencode("https://ui-avatars.com/api/{$first_initial}/{$size}");
                
                unset($comment->user);
                $userObject = new stdClass;
                $userObject->display_name = $displayName;
                // $userObject->user_email = $comment->extra['author']['email'];
                $userObject->photo = $avatar_url;
                
                $comment->user = $userObject;
            }else{
                $user_meta = get_userdata($comment->user_id);
                $user_roles = $user_meta->roles;
                $comment->administrator = $user_roles[0] == 'administrator' ?? true;
            }
        }

        return $comments;
    }

    public function createReply($commentData, $id)
    {
        $current_user = wp_get_current_user();
       

        
        $idea = Idea::findOrFail($id);

        $settingData = [];
        if(!$current_user->ID){
            $settingData = array(
                'author' => [
                    'name' => !empty($current_user->ID) ? $current_user->display_name : $commentData['user_name'],
                    'email' => !empty($current_user->ID)  ? $current_user->user_email : $commentData['user_email']
                ],
            ); 
        }

        $replyComment = new TaskComment();

        $replyComment->user_id = $current_user->ID ? $current_user->ID : null;
        $replyComment->board_id = $idea->board_id;
        $replyComment->task_id = $commentData['task_id'];
        $replyComment->message = $commentData['message'];
        $replyComment->parent_id = $commentData['parent_id'];
        $replyComment->type = 'comment';
        $replyComment->status = 'published';
        $replyComment->extra = serialize($settingData);
        $replyComment->save();

        return $replyComment;
    }

    public function fetchReplies($commentId)
    {
        $replies = TaskComment::where('parent_id', $commentId)
            ->whereType('comment')
            ->where('status', 'published')
            ->with(['user' => function ($query) {
                $query->select('ID','display_name','user_email');
            }])->get();
        foreach($replies as $reply)
        {
            if(!$reply->user_id){
                $reply->extra = unserialize($reply->extra);
                $reply->administrator = false;
                $displayName = $reply->extra['author']['name'];
                // Generate an avatar with the first character of the first name
                $first_initial = mb_substr($displayName, 0, 1, 'UTF-8');
                $size = 128;
                $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($displayName))) . "?s={$size}&d=" . urlencode("https://ui-avatars.com/api/{$first_initial}/{$size}");
                
                unset($reply->user);
                $userObject = new stdClass;
                $userObject->display_name = $displayName;
                // $userObject->user_email = $comment->extra['author']['email'];
                $userObject->photo = $avatar_url;
                
                $reply->user = $userObject;

                $user_meta = get_userdata($reply->user_id);
                $user_roles = $user_meta->roles;
                $reply->administrator = $user_roles[0] == 'administrator' ?? true;
            }else{
                $reply->administrator = false;
            }
        }
        return $replies;
    }

    public function updateComment($message, $commentId)
    {
        $comment = TaskComment::findOrFail($commentId);
        if ($comment->user_id != get_current_user_id()) {
            return false;
        }
        $comment->message = $message;
        $comment->save();

        return $comment;
    }

    public function commentTrash($commentId)
    {
        $comment = TaskComment::findOrFail($commentId);
        $comment->status = 'unpublished';
        $comment->save();

        return $comment;
    }
}
