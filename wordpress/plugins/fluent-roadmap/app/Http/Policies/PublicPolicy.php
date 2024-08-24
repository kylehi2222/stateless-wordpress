<?php

namespace FluentRoadmap\App\Http\Policies;

use FluentBoards\Framework\Http\Request\Request;

class PublicPolicy extends \FluentBoards\App\Http\Policies\BasePolicy
{
    /**
     * @param \FluentBoards\Framework\Http\Request\Request $request
     * @return true
     */
    public function verifyRequest(Request $request)
    {
        return true;
    }

}
