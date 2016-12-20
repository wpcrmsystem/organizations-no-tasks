<?php
/*
Plugin Name: WP-CRM System Organizations with No Tasks Report
Plugin URI: https://www.wp-crm.com
Description: Display a report of organizations with no tasks
Version: 1.0.0
Author: Scott DeLuzio
Author URI: https://scottdeluzio.com
Text Domain: wcs-orgs-no-tasks
*/
/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}
add_action( 'wpcrm_system_report_tab', 'wpcrm_system_orgs_no_tasks_report_tab', 8 );
function wpcrm_system_orgs_no_tasks_report_tab() {
  // Retrieve the active tab
  global $wpcrm_reports_active_tab; ?>
  <a class="nav-tab <?php echo $wpcrm_reports_active_tab == 'orgs-no-tasks' ? 'nav-tab-active' : ''; ?>" href="?page=wpcrm-reports&tab=orgs-no-tasks"><?php _e('Orgs. Without Tasks', 'wcs-orgs-no-tasks'); ?></a>
<?php }

// Add custom report's content
add_action( 'wpcrm_system_report_content', 'wpcrm_system_report_orgs_no_tasks_content' );
function wpcrm_system_report_orgs_no_tasks_content() {
  // Retrieve the active tab
	global $wpcrm_reports_active_tab;
	if ($wpcrm_reports_active_tab == 'orgs-no-tasks') { ?>
	  <div class="wrap">
		<div>
		  <h2><?php _e( 'Organizations with no tasks', 'wcs-orgs-no-tasks' ); ?></h2>
		  <?php echo display_orgs_no_tasks(); ?>
		</div>
	   </div>
	<?php }
}

function display_orgs_no_tasks(){
	$organization_report = ''; ?>
	<table>
	<?php
	//get all organizations
	$args = array( 'posts_per_page'=>-1,'post_type' => 'wpcrm-organization');
	$loop = new WP_Query( $args );
	$orgs = array();
	while ( $loop->have_posts() ) : $loop->the_post();
	   $orgs[] = get_the_ID();
	endwhile;
	//var_dump($orgs);
	//get tasks with organizations attached
	$args = array( 'posts_per_page'=>-1,'post_type'=>'wpcrm-task' );
	$loop = new WP_Query( $args );
	$tasks = array();
	while ( $loop->have_posts() ) : $loop->the_post();
		$tasks[] = get_post_meta( get_the_ID(), '_wpcrm_task-attach-to-organization', true );
	endwhile;
	//var_dump($tasks);
	$displayID = array();
	foreach ($orgs as $org){
		if ( !in_array( $org, $tasks ) ){
			$displayID[] = $org;
		}
	}
	//var_dump($displayID);
	$organizations = '';
	foreach( $displayID as $id ){
		$organizations .= '<tr><td><a href="' . get_edit_post_link($id) . '">' . get_the_title($id) . '</a></td></tr>';
	}
	echo $organizations; ?>
	</table>
	<?php
}