<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 08-April-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace BetterMessages\OpenAI\Testing\Responses\Fixtures\Threads\Runs;

final class ThreadRunListResponseFixture
{
    public const ATTRIBUTES = [
        'object' => 'list',
        'data' => [
            [
                'id' => 'run_4RCYyYzX9m41WQicoJtUQAb8',
                'object' => 'thread.run',
                'created_at' => 1_699_621_735,
                'assistant_id' => 'asst_EopvUEMh90bxkNRYEYM81Orc',
                'thread_id' => 'thread_EKt7MjGOC6bwKWmenQv5VD6r',
                'status' => 'queued',
                'started_at' => null,
                'expires_at' => 1_699_622_335,
                'cancelled_at' => null,
                'failed_at' => null,
                'completed_at' => null,
                'last_error' => null,
                'model' => 'gpt-4',
                'instructions' => 'You are a personal math tutor. When asked a question, write and run Python code to answer the question.',
                'tools' => [
                    [
                        'type' => 'code_interpreter',
                    ],
                ],
                'file_ids' => [
                    'file-6EsV79Y261TEmi0PY5iHbZdS',
                ],
                'metadata' => [],
            ],
        ],
        'first_id' => 'run_4RCYyYzX9m41WQicoJtUQAb8',
        'last_id' => 'run_4RCYyYzX9m41WQicoJtUQAb8',
        'has_more' => false,
    ];
}
