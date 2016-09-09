<?php


/**
* Adds user management for editor role.
*
* @return void
*/
function add_grav_forms(){
    $role = get_role('editor');
    $role->add_cap('gform_full_access');
}
add_action('admin_init','add_grav_forms');

/**
* Enable public editing for Gravity forms
*
* @return void
*/
add_filter('gform_update_post/public_edit', '__return_true');

/**
 * JPB User Caps Functional class courtesy of John P Bloch (JPB)
 *
 * Extends user management to the editor role, whilst
 * preventing editors to create or delete administrators.
 * Via: http://wordpress.stackexchange.com/a/4500
 *
 * @since 0.1.0
 **/

add_action('exchange_plugin_deactivate','exchange_remove_user_management_for_editors');


/**
* Adds user management for editor role.
*
* @return void
*/
function exchange_add_user_management_for_editors() {
	$role = get_role( 'editor' );
	$caps = exchange_get_user_caps();
	foreach ( $caps as $cap ) {
		$role->add_cap( $cap );
	}
}

/**
* Returns array with user-editing capacities.
*
* @return array
*/
function exchange_get_user_caps() {
	return array(
		'list_users',
		'edit_users',
		'create_users',
		'add_users',
		'remove_users',
		'promote_users',
	);
}

/**
* Removes user management for editor role.
*
* @return void
*/
function exchange_remove_user_management_for_editors() {
	$role = get_role( 'editor' );
	$caps = exchange_get_user_caps();
	foreach ( $caps as $cap ) {
		$role->remove_cap( $cap );
	}
}

class JPB_User_Caps {

	// Add our filters
	public function __construct(){
		add_filter( 'editable_roles', array(&$this, 'editable_roles'));
		add_filter( 'map_meta_cap', array(&$this, 'map_meta_cap'),10,4);
	}

  // Remove 'Administrator' from the list of roles if the current user is not an admin
  function editable_roles( $roles ){
    if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
      unset( $roles['administrator']);
    }
    return $roles;
  }

  // If someone is trying to edit or delete and admin and that user isn't an admin, don't allow it
  function map_meta_cap( $caps, $cap, $user_id, $args ){

    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }

}

$jpb_user_caps = new JPB_User_Caps();
