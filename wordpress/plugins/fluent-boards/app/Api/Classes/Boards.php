<?php

namespace FluentBoards\App\Api\Classes;

defined('ABSPATH') || exit;

use FluentBoards\App\Models\Board;
use FluentBoards\App\Services\BoardService;
use FluentBoards\App\Services\Helper;
use FluentBoards\App\Services\PermissionManager;
use FluentBoards\App\Services\StageService;
use FluentBoards\App\Services\LabelService;
use FluentBoards\Framework\Support\Arr;


/**
 * Contacts Class - PHP APi Wrapper
 *
 * Contacts API Wrapper Class that can be used as <code>FluentBoardsApi('boards')</code> to get the class instance
 *
 * @package FluentBoards\App\Api\Classes
 * @namespace FluentBoards\App\Api\Classes
 *
 * @version 1.0.0
 */
class Boards
{
    private $instance = null;

    private $allowedInstanceMethods = [
        'all',
        'get',
        'find',
        'first',
        'paginate'
    ];

    public function __construct(Board $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Get Boards
     *
     * Use:
     * <code>FluentBoardsApi('boards')->getBoards();</code>
     *
     * @param array $with
     * @return array|Board Model
     */
    public function getBoards($with = [], $sortBy = 'title', $sortOrder = 'asc')
    {
        $userId = get_current_user_id();
        $query = Board::byAccessUser($userId);

        if($sortBy) {
            $query->orderBy($sortBy, $sortOrder);
        }

        if ($with) {
            $query->with($with);
        }

        $boards = $query->get();
        return $boards;
    }

    /**
     * Get stages by board
     *
     * Use:
     * <code>FluentBoardsApi('boards')->getStagesByBoard($board_id);</code>
     *
     * @param int|string $board_id
     * @return array Model
     */
    public function getStagesByBoard($board_id)
    {
        if (empty($board_id)) {
            return [];
        }

        return Board::with('stages')->where('id', $board_id)->get();
    }

    public function create($data)
    {
        if (empty($data['title'])) {
            return false;
        }

        $boardData = $this->boardSanitizeAndValidate($data);

        $boardService = new BoardService();
        $labelService = new LabelService();
        $stageService = new StageService();

        $board = $boardService->createBoard($boardData);

        if (!$board) {
            return false;
        }

        $labelService->createDefaultLabel($board->id);
        $stageService->createDefaultStages($board);

        // if board is created from crm contact
        if (isset($boardData['crm_contact_id'])) {
            $boardService->updateAssociateMember($boardData['crm_contact_id'], $board->id);
        }
        do_action('fluent_boards/board_created', $board);

        return $board;
    }

    private function boardSanitizeAndValidate($data)
    {
        return Helper::sanitizeBoard($data);
    }

}
