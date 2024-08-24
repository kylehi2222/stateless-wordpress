<?php
namespace FluentRoadmap\App\Services;


class Helper {
    public static function snake_case($string) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function slugify($text, $id = '', $length = 20) {
        $text = substr($text, 0, $length); // limiting to max 20 chars
        $text = \FluentBoards\Framework\Support\Str::slug($text);
        if ($id) {
            $text = $id . '-' . $text;
        }
        return $text;
    }

	public static function loadView($template, $data)
	{
		extract($data, EXTR_OVERWRITE);

		$template = sanitize_file_name($template);

		$template = str_replace('.', DIRECTORY_SEPARATOR, $template);

		ob_start();
		include FLUENT_ROADMAP_PLUGIN_PATH . 'app/Views/' . $template . '.php';
		return ob_get_clean();
	}

    public static function sanitizeRoadmap($data)
    {
        $fieldMaps = [
            'title'              => 'sanitize_text_field',
            'roadmap_board_id'   => 'intval',
            'description'        => 'sanitize_textarea_field',
            'user_email'         => 'sanitize_email',
        ];

        return self::sanitizeData($data, $fieldMaps);
    }

    private static function sanitizeData($data, $fieldMaps){
        foreach ($data as $key => $value) {
            if ($value && isset($fieldMaps[$key]) && !is_array($value)) {
                $data[$key] = call_user_func($fieldMaps[$key], $value);
            }
        }

        return $data;
    }

    public static function sanitizeComment($data)
    {
        $fieldMaps = [
            'comment'   => 'sanitize_textarea_field',
            'user_id'    => 'intval',
            'task_id'       => 'intval',
            'user_name' => 'sanitize_text_field',
            'user_email' => 'sanitize_text_field',
        ];
        return self::sanitizeData($data, $fieldMaps);
    }

    public static function getClientIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';


        return sanitize_text_field($ipaddress);
    }

    public static function getRoadmapPageUrl($roadmapBoard)
    {
        $pageUrl = '#';

        if ($roadmapBoard->type != 'roadmap') {
            return $pageUrl;
        }

        $pageId = $roadmapBoard->meta['roadmap_page_id'] ?? null;

        if(!$pageId) {
            return $pageUrl;
        }

        return get_permalink($pageId);

    }
}
