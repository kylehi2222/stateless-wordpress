<?php

namespace FluentRoadmap\App\Services;

use stdClass;
use FluentRoadmap\App\Models\TaskComment;
use FluentRoadmap\App\Models\TaskActivity;
use FluentBoards\App\Models\Board;
use FluentRoadmap\App\Models\Idea;

class RoadmapService
{
    public function fetchAllStages($data, $roadmap_id)
    {
        $roadmapBoardStages = Board::find($roadmap_id)->load('stages');
        $roadmapBoardStages = json_decode($roadmapBoardStages);
        $publicStages = [];

        foreach ($roadmapBoardStages->stages as $stage) {
            if (property_exists($stage->settings, 'is_public')) {
                if ($stage->settings->is_public) {
                    $publicStages[] = $stage;
                }
            }
        }

        $roadmapBoardStages->stages = $publicStages;
        return $roadmapBoardStages;
    }

    public function createIdea($data)
    {
        // TODO: check if user is logged in first and then get the current user
        // if not logged in, then insert the idea with the email address

        $current_user = wp_get_current_user();

        $idea = new Idea();
        $idea->title = $data['title'];
        $idea->board_id = $data['roadmap_board_id'];
        $idea->stage = 'pending'; // we will make it dynamic later , currently in board pending is the default entry stage
        $idea->description = $data['description'];

        $settingData = array(
            'integration_type' => 'feature',
            'logo' => ''
        );

        $idea['settings'] = serialize($settingData);
        $idea->save();

        $activity = new TaskActivity();

        $activity->user_id    = $current_user->ID ? $current_user->ID : null;
        $activity->author_name    = $current_user->ID ? $current_user->display_name : $data['user_name'];
        $activity->author_email    = $current_user->ID ? $current_user->user_email : $data['user_email'];
        $activity->author_ip    = $_SERVER['REMOTE_ADDR'];
        $activity->object_type    = 'task_create';
        $activity->object_id    = $idea->id;
        $activity->save();

        return $idea;
    }

    public function fetchAllIdeas($datas, $boardId, $userId)
    {
        $roadmapType = $datas->roadmap_type;
        $search = $datas->search;
        $selectedSortSearch = $datas->selectSortSearch;
        $perPage   = $datas->per_page;

        $tasks = Idea::query()->where('board_id', $boardId)
            ->where('stage', $roadmapType)
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($selectedSortSearch, function ($query) use ($selectedSortSearch) {
                if ($selectedSortSearch == 'new-first') {
                    return $query->orderBy('id', 'desc');
                } elseif ($selectedSortSearch == 'old-first') {
                    return $query->orderBy('id', 'asc');
                } elseif ($selectedSortSearch == 'trending') {
                    return $query->withCount('votes')
                        ->orderBy('votes_count', 'desc');
                }
            })
            ->with(['user'=> function ($query) {
                $query->select('ID','display_name','user_email');
            }, 'comments' => function ($query) {
                $query->where('type', 'task');
            }])
            ->with(['votes' => function ($query) {
                $query->where('object_type', 'task')->whereNotNull('reactions');
            }])
            ->paginate($perPage);

        foreach ($tasks as $task) {
            if ($task->votes) {
                $auth_user_vote_check = TaskActivity::where('user_id', $userId)
                    ->where('object_id', $task->id)
                    ->where('object_type', 'task')
                    ->first();

                $task->is_vote_provided = $auth_user_vote_check ? true : false;
                $task->provided_vote = $auth_user_vote_check ? $auth_user_vote_check['reactions'] : '';
            } else {
                $task->is_vote_provided = false;
            }
            $task->description = strip_tags($task->description);
            $task->formatted_created_at = date("M jS Y", strtotime($task->created_at));
            $task->settings = unserialize($task->settings);

            if (!$task->user) {

                $task_created_by = TaskActivity::where('object_id', $task->id)
                    ->where('object_type', 'task_create')
                    ->first();

                $displayName = $task_created_by->author_name;
                // Generate an avatar with the first character of the first name
                $first_initial = mb_substr($displayName, 0, 1, 'UTF-8');
                $size = 128;
                $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($displayName))) . "?s={$size}&d=" . urlencode("https://ui-avatars.com/api/{$first_initial}/{$size}");

                unset($task->user);
                $userObject = new \stdClass;
                $userObject->display_name = $displayName;
                // $userObject->user_email = $task_created_by->email;
                $userObject->photo = $avatar_url;

                // Assign the user object to the "user" field in your data
                $task->user = $userObject;
            }
        }

        return $tasks;
    }

    public function fetchIdea($task_id, $user_id)
    {
        $user_id = $user_id ? $user_id : null;
        $result = Idea::where('id', $task_id)
            ->with(
                ['user' => function ($query) {
                    $query->select('ID','display_name','user_email');
                }, 'votes' => function ($query) {
                    $query->where('object_type', 'task');
                }]
            )
            ->first();
        if (!$result) {
            return null;
        }

        $result->description = strip_tags($result->description);
        $result->formatted_created_at = date("F Y", strtotime($result->created_at));

        $check_auth_user_available = null;

        if ($user_id) {
            $check_auth_user_available = TaskActivity::where('user_id', $user_id)
                ->where('object_id', $result->id)
                ->where('object_type', 'task')
                ->first();
        }

        $result->is_vote_provided = !is_null($check_auth_user_available);
        $result->settings = unserialize($result->settings);


        if (!$result->user) {
            $task_created_by = TaskActivity::where('object_id', $result->id)
                ->where('object_type', 'task_create')
                ->first();

            $displayName = $task_created_by->author_name;
            $first_initial = mb_substr($displayName, 0, 1, 'UTF-8');
            $size = 128;
            $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($displayName))) . "?s={$size}&d=" . urlencode("https://ui-avatars.com/api/{$first_initial}/{$size}");

            unset($result->user);
            $userObject = new \stdClass;
            $userObject->display_name = $displayName;
            // $userObject->user_email = $task_created_by->email;
            $userObject->photo = $avatar_url;
            // Assign the user object to the "user" field in your data
            $result->user = $userObject;
        }

        return $result;
    }

    public function voteRoadmap($data)
    {
        $current_user = wp_get_current_user();
        $task = Idea::find($data['task_id']);

        $check_available_vote = null;
        if ($current_user->ID) {
            $check_available_vote = TaskActivity::where('user_id', $current_user->ID)
                ->where('object_type', 'task')
                ->where('object_id', $data['task_id'])
                ->first();
        }

        if (!$check_available_vote) {
            $ideaVote = TaskActivity::create([
                'user_id'   => $current_user->ID ? $current_user->ID : null,
                'author_name'  => $data['user_name'] ? $data['user_name'] : $current_user->data->display_name,
                'author_email'   => $data['user_email'] ? $data['user_email'] : $current_user->data->user_email,
                'author_ip'   => $_SERVER['REMOTE_ADDR'],
                'object_type'  => 'task',
                'object_id'   => $data['task_id'],
                'reactions'   => 'upvote',
            ]);
        } else {
            $check_available_vote->delete();
        }

        $task = Idea::where('id', $data['task_id'])
            ->with(
                ['user' => function ($query) {
                    $query->select('ID','display_name','user_email');
                }, 'comments' => function ($query) use ($current_user) {
                    $query->where('type', 'task')
                        ->with(['commentReactions' => function ($subQuery) use ($current_user) {
                            $subQuery->where('user_id', $current_user->ID)
                                ->where('object_type', 'comment')
                                ->orderBy('id', 'desc');
                        }]);
                }, 'votes' => function ($query) {
                    $query->where('object_type', 'task');
                }]
            )
            ->first();

        if (!$task->user) {
            $task_created_by = TaskActivity::where('object_id', $task->id)
                ->where('object_type', 'task_create')
                ->first();

            $displayName = $task_created_by->author_name;
            $first_initial = mb_substr($displayName, 0, 1, 'UTF-8');
            $size = 128;
            $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($displayName))) . "?s={$size}&d=" . urlencode("https://ui-avatars.com/api/{$first_initial}/{$size}");

            unset($task->user);
            $userObject = new \stdClass;
            $userObject->display_name = $displayName;
            $userObject->user_email = $task_created_by->email;
            $userObject->photo = $avatar_url;
            // Assign the user object to the "user" field in your data
            $task->user = $userObject;
        }
        $task->formatted_created_at = date("M jS Y", strtotime($task->created_at));
        $task->is_vote_provided = !$check_available_vote ? true : false;
        // $task->provided_vote = $check_available_vote ? $check_available_vote['reactions'] : '';
        $task->settings = unserialize($task->settings);
        return $task;
    }

    public function storeReaction($data, $user)
    {
        $is_reaction_available = TaskActivity::where('user_id', $user->ID)
            ->where('object_type', 'comment')
            ->where('object_id', $data->object_id)
            ->where('user_id', '!=', 0)
            ->first();
        if ($is_reaction_available) {
            if ($is_reaction_available['reactions'] == $data->reactions) {
                $is_reaction_available->delete();
                return;
            } else {
                $is_reaction_available->author_ip = $_SERVER['REMOTE_ADDR'];
                $is_reaction_available->reactions = $data->reactions;
                $is_reaction_available->save();
                return $is_reaction_available;
            }
        } else {
            $reactionVote = TaskActivity::create([
                'user_id'   => $user->ID,
                'author_name'  => $user->data->display_name,
                'author_email'   => $user->data->user_email,
                'author_ip'   => $_SERVER['REMOTE_ADDR'],
                'object_type'  => $data->object_type,
                'object_id'   => $data->object_id,
                'reactions'   => $data->reactions,
            ]);
            return $reactionVote;
        }
    }

    public function countReactionCount($commentId)
    {
        $reactions = TaskActivity::where('object_type', 'comment')
            ->where('object_id', $commentId)
            ->whereIn('reactions', ['upvote', 'downvote'])
            ->get();

        $upvoteCount = $reactions->where('reactions', 'upvote')->count();
        $downvoteCount = $reactions->where('reactions', 'downvote')->count();

        $reactionCounts['upvote'] = $upvoteCount;
        $reactionCounts['downvote'] = $downvoteCount;

        return $reactionCounts;
    }
}
