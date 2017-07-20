<?php

/**
 * Options Page - CLR
 */

class CommentLinkRemove {

    private $comment_link_remove_options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'comment_link_remove_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'comment_link_remove_page_init' ) );
    }

    public function comment_link_remove_add_plugin_page() {
        add_options_page(
            'Comment Link Remove (CLR) - Settings', // page_title
            'QC CLR Settings', // menu_title
            'manage_options', // capability
            'comment-link-remove', // menu_slug
            array( $this, 'comment_link_remove_create_admin_page' ) // function
        );
    }

    public function comment_link_remove_create_admin_page() {
        
        $this->comment_link_remove_options = get_option( 'comment_link_remove_option_name' ); 

        //Check for available adds or promo, kadir - 09-19-16
        $iframeCode = '<div class="qc-promo-plugins" style="text-align: center;">';

        $iframeCode .= '<img src="'.QCCLR_ASSETS_URL.'/img/qc-logo-full.png" alt="QuantumCloud Logo">';
		$iframeCode .= "<br><br><hr><br>";
		$iframeCode .= '<a href="http://www.quantumcloud.com" target="_blank">QuantumCloud</a>';
		$iframeCode .= '</div>';
		

        ?>

        <div class="wrap">

            <style>
                #post-body-content h2{
                    padding-left: 0;
                }
            </style>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" style="position: relative;">

                        <h2>Comment Link Remove (CLR) - Settings</h2>

                        <p>
                            Here you can manage custom settings for the Comment Link Remove plugin.
                        </p>

                        <?php //settings_errors(); ?>

                        <form method="post" action="options.php">
                            <?php
                                settings_fields( 'comment_link_remove_option_group' );
                                do_settings_sections( 'comment-link-remove-admin' );
                                submit_button();
                            ?>
                        </form>
						
						<hr>
						
						<?php 

						global $wpdb;
						
						if( current_user_can( 'manage_options' ) )
						{
							if( isset($_POST['delAllCmts']) && $_POST['delAllCmts'] == 'delAllCmts' )
							{
								$response = true;
								
								$wpdb->query($wpdb->prepare( "UPDATE `{$wpdb->prefix}posts` set comment_count=%d",0));

								$wpdb->get_results("DELETE FROM `{$wpdb->prefix}comments` "); 

								if($response)
								{
									echo "<strong>All Comments deleted successfully!</strong><br>"; 
								}
								
							}

							if( isset($_POST['delPendingCmts']) && $_POST['delPendingCmts'] == 'delPendingCmts'  )
							{
								$query = $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}comments` WHERE `comment_approved` = %d",0);
								$response = $wpdb->query($query);  

								if($response)
								{
									echo "<strong>All Pending Comments deleted successfully!</strong><br>"; 
								}
								
							}

							if( isset($_POST['delSpamCmts']) && $_POST['delSpamCmts'] == 'delSpamCmts'  )
							{
								$query = $wpdb->prepare( "DELETE FROM `{$wpdb->prefix}comments` WHERE `comment_approved` = %s",'spam');
								$response = $wpdb->query($query);  
				

								if($response)
								{
									echo "<strong>All Spam Comments deleted successfully!</strong><br>"; 
								}
								
							}
						}

						?>

						<script>
							jQuery(document).ready(function($){
								
								$(".commentDelete").on("click", function(e){
									var result = false;
									result = confirm("Are you really want to delete? Once proceed, Option is irreversible.");
									return result;
								});
							});
						</script>

						<div class="dele-cmts">
							
							<p><strong>Delete comments easily: </strong></p>
							<p></p>

							<form style="display: inline-block;" action="" method="POST">
								<button class="commentDelete button button-primary" type="submit" name="delAllCmts" value="delAllCmts">
									Delete All Comments
								</button>
							</form>

							<form style="display: inline-block;" action="" method="POST">
								<button class="commentDelete button button-primary" type="submit" name="delPendingCmts" value="delPendingCmts">
									Delete Pending Comments
								</button>
							</form>

							<form style="display: inline-block;" action="" method="POST">
								<button class="commentDelete button button-primary" type="submit" name="delSpamCmts" value="delSpamCmts">
									Delete Spam Comments
								</button>
							</form>

						</div>
                        
                    </div>
                    <!-- /post-body-content -->

                    <hr>

                    <!-- Right Sidebar -->
                    <div id="postbox-container-1" id="postbox-container">

                        <!-- Plugin Logo -->
                        <div style="border: 1px solid #ccc; padding: 10px 0; text-align: center;">
                            QC Comment Link Remove
                        </div>

                        <!-- Promo Block 1 -->
                        <div style="margin-top: 20px;">
                            <?php echo $iframeCode; ?>
                        </div>

                    </div>
                    <!-- /Right Sidebar -->

                </div>
                <!-- /post-body -->
            </div>
            <!-- /poststuff -->

        </div>

    <?php }

    public function comment_link_remove_page_init() {
        register_setting(
            'comment_link_remove_option_group', // option_group
            'comment_link_remove_option_name', // option_name
            array( $this, 'comment_link_remove_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'comment_link_remove_setting_section', // id
            '', // title
            array( $this, 'comment_link_remove_section_info' ), // callback
            'comment-link-remove-admin' // page
        );

        add_settings_field(
            'remove_author_uri_field_0', // id
            'Remove WEBSITE Field from Comment Form', // title
            array( $this, 'remove_author_uri_field_0_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'remove_any_link_from_author_field_1', // id
            'Remove hyper-link from comment AUTHOR', // title
            array( $this, 'remove_any_link_from_author_field_1_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'remove_links_from_comments_field_3', // id
            'Disable turning URLs into hyper-links in comments', // title
            array( $this, 'remove_links_from_comments_field_3_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'remove_links_from_comments_field_2', // id
            'Remove HTML Link Tags in comments', // title
            array( $this, 'remove_links_from_comments_field_2_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'disable_comments_totally', // id
            'Disable Comments Globally', // title
            array( $this, 'disable_comments_totally_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'hide_existing_cmts', // id
            'Hide Existing Comments', // title
            array( $this, 'hide_existing_cmts_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );

        add_settings_field(
            'open_link_innewtab', // id
            'Open Comment Links in New Tab', // title
            array( $this, 'open_link_innewtab_callback' ), // callback
            'comment-link-remove-admin', // page
            'comment_link_remove_setting_section' // section
        );
    }

    public function comment_link_remove_sanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['remove_author_uri_field_0'] ) ) {
            $sanitary_values['remove_author_uri_field_0'] = $input['remove_author_uri_field_0'];
        }

        if ( isset( $input['remove_any_link_from_author_field_1'] ) ) {
            $sanitary_values['remove_any_link_from_author_field_1'] = $input['remove_any_link_from_author_field_1'];
        }

        if ( isset( $input['remove_links_from_comments_field_3'] ) ) {
            $sanitary_values['remove_links_from_comments_field_3'] = $input['remove_links_from_comments_field_3'];
        }

        if ( isset( $input['remove_links_from_comments_field_2'] ) ) {
            $sanitary_values['remove_links_from_comments_field_2'] = $input['remove_links_from_comments_field_2'];
        }

        if ( isset( $input['disable_comments_totally'] ) ) {
            $sanitary_values['disable_comments_totally'] = $input['disable_comments_totally'];
        }

        if ( isset( $input['hide_existing_cmts'] ) ) {
            $sanitary_values['hide_existing_cmts'] = $input['hide_existing_cmts'];
        }

        if ( isset( $input['open_link_innewtab'] ) ) {
            $sanitary_values['open_link_innewtab'] = $input['open_link_innewtab'];
        }

        return $sanitary_values;
    }

    public function comment_link_remove_section_info() {
        
    }

    public function remove_author_uri_field_0_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[remove_author_uri_field_0]" id="remove_author_uri_field_0" value="1" %s>',
            ( isset( $this->comment_link_remove_options['remove_author_uri_field_0'] ) && $this->comment_link_remove_options['remove_author_uri_field_0'] === '1' ) ? 'checked' : ''
        );
    }

    public function remove_any_link_from_author_field_1_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[remove_any_link_from_author_field_1]" id="remove_any_link_from_author_field_1" value="1" %s>',
            ( isset( $this->comment_link_remove_options['remove_any_link_from_author_field_1'] ) && $this->comment_link_remove_options['remove_any_link_from_author_field_1'] === '1' ) ? 'checked' : ''
        );
    }

    public function remove_links_from_comments_field_3_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[remove_links_from_comments_field_3]" id="remove_links_from_comments_field_3" value="1" %s>',
            ( isset( $this->comment_link_remove_options['remove_links_from_comments_field_3'] ) && $this->comment_link_remove_options['remove_links_from_comments_field_3'] === '1' ) ? 'checked' : ''
        );
    }

    public function remove_links_from_comments_field_2_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[remove_links_from_comments_field_2]" id="remove_links_from_comments_field_2" value="1" %s>',
            ( isset( $this->comment_link_remove_options['remove_links_from_comments_field_2'] ) && $this->comment_link_remove_options['remove_links_from_comments_field_2'] === '1' ) ? 'checked' : ''
        );
    }

    public function disable_comments_totally_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[disable_comments_totally]" id="disable_comments_totally" value="1" %s>',
            ( isset( $this->comment_link_remove_options['disable_comments_totally'] ) && $this->comment_link_remove_options['disable_comments_totally'] === '1' ) ? 'checked' : ''
        );
    }

    public function hide_existing_cmts_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[hide_existing_cmts]" id="hide_existing_cmts" value="1" %s>',
            ( isset( $this->comment_link_remove_options['hide_existing_cmts'] ) && $this->comment_link_remove_options['hide_existing_cmts'] === '1' ) ? 'checked' : ''
        );
    }

    public function open_link_innewtab_callback() {
        printf(
            '<input type="checkbox" name="comment_link_remove_option_name[open_link_innewtab]" id="open_link_innewtab" value="1" %s>',
            ( isset( $this->comment_link_remove_options['open_link_innewtab'] ) && $this->comment_link_remove_options['open_link_innewtab'] === '1' ) ? 'checked' : ''
        );
    }

}


if ( is_admin() )
    $comment_link_remove = new CommentLinkRemove();
