<?php

namespace FluentRoadmap\App\Http\Policies;

use FluentBoards\App\Http\Policies\BasePolicy;
use FluentBoards\Framework\Http\Request\Request;

class RoadmapAdminPolicy extends BasePolicy
{
    /**
     * Check user permission for any method
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @return Boolean
     */
    public function verifyRequest(Request $request)
    {
        return current_user_can('manage_options');
    }
}
