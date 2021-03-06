<?php

class Bl_Sponsor_Cms_Manager_Admin {

    private $version;

    private $data_model;

    private $options;

    function __construct( $version, $options, $data_model )
    {

        $this->version = $version;

        $this->options = $options;

        $this->data_model = $data_model;

    }

    function register_bl_sponsor_post_type() {

        $labels = array(
            'name'               => __( 'Sponsors', 'bl-sponsor-cms' ),
            'singular_name'      => __( 'Slide', 'bl-sponsor-cms' ),
            'menu_name'          => __( 'Sponsors', 'admin menu', 'bl-sponsor-cms' ),
            'name_admin_bar'     => __( 'Slide', 'add new on admin bar', 'bl-sponsor-cms' ),
            'add_new'            => __( 'Add New Slide', 'bl-sponsor-cms' ),
            'add_new_item'       => __( 'Add New Slide', 'bl-sponsor-cms' ),
            'new_item'           => __( 'New Slide', 'bl-sponsor-cms' ),
            'edit_item'          => __( 'Edit Slide', 'bl-sponsor-cms' ),
            'view_item'          => __( 'View Slide', 'bl-sponsor-cms' ),
            'all_items'          => __( 'All Sponsors', 'bl-sponsor-cms' ),
            'search_items'       => __( 'Search Sponsors', 'bl-sponsor-cms' ),
            'parent_item_colon'  => __( 'Parent Sponsors:', 'bl-sponsor-cms' ),
            'not_found'          => __( 'No sponsors found.', 'bl-sponsor-cms' ),
            'not_found_in_trash' => __( 'No sponsors found in Trash.', 'bl-sponsor-cms' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'slide' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'map_meta_cap'       => true,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
        );

        register_post_type( 'bl-sponsor', $args );

        $sponsor_category_labels = array(
            'name' => __( 'Category', 'bl-sponsor-cms' ),
            'singular_name' => __( 'Categoria', 'bl-sponsor-cms' ),
            'search_items' =>  __( 'Search Category', 'bl-sponsor-cms' ),
            'all_items' => __( 'All Categories', 'bl-sponsor-cms' ),
            'parent_item' => __( 'Parent Category', 'bl-sponsor-cms' ),
            'parent_item_colon' => __( 'Parent Category', 'bl-sponsor-cms' ),
            'edit_item' => __( 'Edit Category', 'bl-sponsor-cms' ),
            'update_item' => __( 'Update Category', 'bl-sponsor-cms' ),
            'add_new_item' => __( 'Add New Category', 'bl-sponsor-cms' ),
            'new_item_name' => __( 'New Category', 'bl-sponsor-cms' ),
            'menu_name' => __( 'Category', 'bl-sponsor-cms' ),
        );

        $sponsor_category_args = array(
            'hierarchical' => true,
            'labels' => $sponsor_category_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'rubrics' ),
            'show_in_nav_menus' => true,
        );

        register_taxonomy('bl-sponsor-category', array('bl-sponsor'), $sponsor_category_args);

        if( ! get_option( 'bl-sponsor-default-category') ){

            $default_bl_sponsor_category_cats = array('homepage');

            foreach($default_bl_sponsor_category_cats as $cat){

                if(!term_exists($cat, 'bl-sponsor-category')) wp_insert_term($cat, 'bl-sponsor-category');

            }

            add_option( 'bl-sponsor-default-category', true );

        }

    }

    function add_meta_box_linking_sponsor() {
        global $post_type;        

        add_meta_box('linking_posts_list', 
            __("Linking Sponsor", 'linking-sponsor'), 
            array($this, 'render_meta_box_linking_sponsor'), 
            $post_type , 
            'side'
        );
        

    }

    function render_meta_box_linking_sponsor( $post ) {

        global $post;
        $value = get_post_meta( $post->ID, 'meta_box_linking_sponsor', true );
        echo '<input name="meta_box_linking_sponsor" type="text" class="large-text ui-autocomplete-input" value="'.$value.'">';
        echo '<p>Aggiungi il link da associare al\'oggetto sponsor</p>';

    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function save_meta_box_linking_sponsor( $post_id ) {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['meta_box_linking_sponsor'] ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
    
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }
        
        /* OK, its safe for us to save the data now. */

        // Sanitize the user input.
        $mydata = sanitize_text_field( $_POST['meta_box_linking_sponsor'] );

        // Update the meta field.
        update_post_meta( $post_id, 'meta_box_linking_sponsor', $mydata );
    }

    

}