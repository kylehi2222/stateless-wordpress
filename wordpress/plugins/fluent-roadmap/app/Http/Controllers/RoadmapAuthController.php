<?php

namespace FluentRoadmap\App\Http\Controllers;

use FluentRoadmap\App\Models\Board;
use FluentBoards\Framework\Http\Request\Request;

use FluentRoadmap\App\Models\Idea;
use FluentRoadmap\App\Services\RoadmapService;
use FluentRoadmap\App\Services\Helper;

class RoadmapController extends Controller
{
    private RoadmapService $roadmapService;

    public function __construct(RoadmapService $roadmapService)
    {
        parent::__construct();
        $this->roadmapService = $roadmapService;
    }

    public function registerUser(Request $request) {

        if (isset($request->name) && isset($request->email) && $request->password) {
            $name = sanitize_user($request->name);
            $email = sanitize_email($request->email);
            $password = $request->password;
    
            $user_id = wp_create_user($name, $password, $email);
    
            if (is_wp_error($user_id)) {
                // Registration failed, handle the error
            } else {
                // Registration successful, log the user in
                $creds = array(
                    'user_login' => $name,
                    'user_password' => $password,
                    'remember' => true,
                );
    
                $user = wp_signon($creds, false);
    
                if (is_wp_error($user)) {
                    // Login failed, handle the error
                } else {
                    // Login successful, you can redirect the user to a specific page
                    wp_redirect(home_url('/dashboard/')); // Change this to your desired URL
                    exit;
                }
            }
        }
    }

    public function login(Request $request)
    {
        return $request;
    }
}
