<?php

/**
 * Api for Preferences
 */


/**
 * Gets alls preferences for a given user
 * or if null, the current user
 *
 * @param mixed $user_id
 *
 * @return array
 */
function cs_preferences_user($user_id = null) {
  return cornerstone("Preferences")->get_user_preferences($user_id);
}

/**
 * Gets singular preference by key for a given user
 * or if null, the current user
 *
 * @param string $key
 * @param mixed $fallback value to use when no preference set
 * @param mixed $user_id
 *
 * @return array
 */
function cs_preference_user($key, $fallback = null, $user_id = null) {
  return cornerstone("Preferences")->get_preference($key, $fallback, $user_id);
}

/**
 * Gets all preferences in Cornerstone system
 *
 * @return array
 */
function cs_preferences() {
  return cornerstone("Preferences")->get_preferences();
}
