<?php
// wp_dashboard_setup is the action hook
add_action('wp_dashboard_setup', 'cl_dashboard');
function cl_dashboard() {
    wp_add_dashboard_widget('cl_dashboard_widget', 'Contact lite','cl_custom_dashboard_message');
}
function cl_custom_dashboard_message(){

    $posts = get_posts(array(
        'posts_per_page'    => 10,
        'post_type'         => 'contact-lite',
        'post_status'       => 'publish',

        ));
    ?>
	<ul>
	<?php
    global $post;
    foreach ( $posts as $post ) : setup_postdata( $post ); ?>
        <li>
            <a href="<?php echo admin_url( 'post.php?post='.$post->ID.'&action=edit' ); ?>"><?php the_title(); ?> (<?php echo get_the_date();?>)</a>
        </li>
    <?php endforeach;
    wp_reset_postdata();?>
    </ul>
	<a href="?page=cl_download_report&report=cl_contact" class="button">Export CSV</a>
	<?php
}

class cl_CSVExport
{
	/**
	* Constructor
	*/
	public function __construct()
    {
        if(isset($_GET['page']) && $_GET['page'] == 'cl_download_report' && isset($_GET['page']) && $_GET['report'] == 'cl_contact')
        {
        	$this->cl_export();
        }
		else if(isset($_GET['page']) && $_GET['page'] == 'cl_download_report'){
        	exit;
        }
    }
	public function cl_export(){
		global $wpdb;

        $csv_fields=array();
        $csv_fields[] = 'Title';
		$csv_fields[] = 'Content';

        $output_filename = "contact_lite_".date("Y-m-d H:i:s").'.csv';
        $output_handle = @fopen( 'php://output', 'w' );

        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-type: text/csv' );
        header( 'Content-Disposition: attachment; filename=' . $output_filename );
        header( 'Expires: 0' );
        header( 'Pragma: public' );

        // Insert header row
        fputcsv( $output_handle, $csv_fields );
		global $post;
		$args = array( 'posts_per_page' => -1, 'post_type' => 'contact-lite' );
		$myposts = get_posts( $args );
		foreach ( $myposts as $post ) : setup_postdata( $post );
			$tab_data = array( get_the_title(), str_replace('
',' - ',$post->post_content) );
			fputcsv( $output_handle, $tab_data );
		endforeach;
		wp_reset_postdata();
        fclose( $output_handle );
		exit();
	}
}

// Instantiate a singleton of this plugin
new cl_CSVExport();
