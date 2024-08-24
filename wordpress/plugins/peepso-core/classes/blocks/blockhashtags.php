<?php

class PeepSoBlockHashtags extends PeepSoBlockAbstract
{
	protected function get_slug() {
		return 'hashtags';
	}

	protected function get_attributes() {
		$attributes = [
			'title' => [ 'type' => 'string', 'default' => __('Community Hashtags', 'peepso-core') ],
			'limit' => [ 'type' => 'integer', 'default' => 12 ],
			'displaystyle' => [ 'type' => 'integer', 'default' => 0 ],
			'sortby' => [ 'type' => 'integer', 'default' => 0 ],
			'sortorder' => [ 'type' => 'integer', 'default' => 0 ],
			'minsize' => [ 'type' => 'integer', 'default' => 0 ]
		];

		return apply_filters('peepso_block_attributes', $attributes, $this->get_slug());
	}

	protected function get_render_args($attributes, $preview) {
		if ($preview) {
			$data = $this->get_data($attributes);
			$block_id = $attributes['__psBlockId'];
			if ($block_id) {
				PeepSo3_Mayfly::del("peepso_hashtags_{$block_id}");
			}
		} else {
			$data = $this->get_cached_data($attributes);
		}

		return ['data' => $data];
	}

	private function get_data($attributes) {
		global $wpdb;

		$table_name = "";

		$where = '';
		if ($attributes['minsize'] > 0) {
			$where = " ht_count >= {$attributes['minsize']}";
		}

		$where = apply_filters('peepso_hashtags_query', $where);

		if (!empty($where)) {
			$where = 'WHERE ' . $where;
		}

		$order = ' ORDER BY';
		if ($attributes['sortby'] == 1) {
			$order .= ' ht_count ' . ($attributes['sortorder'] == 1 ? 'ASC' : 'DESC') . ',';
		}
		$order .= ' ht_name ' . ($attributes['sortorder'] == 1 ? 'ASC' : 'DESC');

        $suppress_errors = $wpdb->suppress_errors();

		$query = "SELECT * FROM {$wpdb->prefix}peepso_hashtags h $where $order LIMIT {$attributes['limit']}";
		$result = $wpdb->get_results($query);

		if (empty($result)) {
            // try without collation
            $query = str_replace("COLLATE {$wpdb->collate}", '', $query);
            $result = $wpdb->get_results($query);
        }

        $wpdb->suppress_errors($suppress_errors);

		return $result;
	}

	private function get_cached_data($attributes) {
		$block_id = $attributes['__psBlockId'];
		$result = PeepSo3_Mayfly::get("peepso_hashtags_{$block_id}");
		if (!$result) {
			$result = $this->get_data($attributes);
			PeepSo3_Mayfly::set("peepso_hashtags_{$block_id}", $result, HOUR_IN_SECONDS);
		}

		return $result;
	}
}
