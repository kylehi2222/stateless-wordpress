=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.4
Tested up to: 6.5
Stable tag: 4.64.2
Requires PHP: 7.3
License URI: https://www.role-editor.com/end-user-license-agreement/

User Role Editor Pro WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor Pro WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. Changes made to the roles will stay unchanged.
You will have to configure lost part of the settings at the User Role Editor Pro Settings page again after that.
Right decision in this case is to delete free version folder (user-role-editor) after deactivation via FTP, not via WordPress.

== Changelog ==

= [4.64.2] 26.03.2024 =
* Core version: 4.64.2
* Update: Marked as compatible with WordPress 6.5
* Update: Content view restrictions add-on: historically if field "For users" was empty, URE applies "Selected Roles" to the existing post (in case it was not assigned yet), but a default value set by user at URE Settings is applied in both cases, for new added and existing posts.
* Fix: Posts/pages edit restrictions add-on: endless recursion calls issue (conflict with "The Events Calendar" plugin) was fixed.
* Fix: Admin menu access add-on: 
* - full URL (including domain) was used for some menu items. For this reason checkboxes of such menu items may lose selection in case of replication of admin menu restrictions to all subsites under WP multisite. Re-check your admin menu access settings just in case mentioned menu items became unchecked after this update.
* - strpos(): Passing null to parameter #1 ($haystack) of type string is deprecated in /wp-content/plugins/user-role-editor-pro/pro/includes/classes/admin-menu-view.php on line 253
* Fix: wp-admin pages permissions viewer: Undefined array key -1 in /wp-content/plugins/user-role-editor-pro/pro/includes/classes/page-permissions-view.php on line 137
* Fix: Deprecated: explode(): Passing null to parameter #2 ($string) of type string is deprecated in wp-content/plugins/user-role-editor-pro/pro/includes/classes/utils.php on line 181
* Fix: Notice: Array to string conversion in wp-content/plugins/user-role-editor-pro/pro/includes/classes/posts-edit-access-user.php on line 965
* Core version was updated to 4.64.2
* Update: URE_Advertisement: rand() is replaced with wp_rand().
* Update: URE_Ajax_Proccessor: json_encode() is replaced with wp_json_encode().
* Update: User_Role_Editor::load_translation(): load_plugin_textdomain() is called with the 2nd parameter value false, instead of deprecated ''.
* Update: URE_Lib::is_right_admin_path(): parse_url() is replaced with wp_parse_url().
* Update: URE_Lib::user_is_admin() does not call WP_User::has_cap() to enhance performance.
* Update: Plugin version was added to CSS loaded to the "Users", "Users->User Role Editor", "Settings->User Role Editor" pages.
* Update: All JavaScript files are loaded in footer now.
* Fix: "Users->Add New Users". Unneeded extra '<table></table>' HTML tags was removed (thanks to Alejandro A. for this bug report). 


Full list of changes is available in changelog.txt file.
