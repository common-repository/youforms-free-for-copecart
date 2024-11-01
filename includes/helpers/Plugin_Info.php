<?php
/*
Copyright 2020 itservicejung.de - All Rights Reserved
*/
class YouFormsITJ_Plugin_Info
{
    protected static $initd = false;

    /**
     * init pluggable functions.
     *
     * @return  void
     */
    public static function init()
    {
        // Do nothing if pluggable functions already initd.
        if (self::$initd) {
            return;
        }
        add_action('init', [__CLASS__, 'youformsITJ_lables'], 0);
        // State that initialization completed.
        self::$initd = true;
    }


    public static function youformsITJ_lables()
    {
        $labels = array(
            'name' => __("youForms Free for CopeCart", 'Cope_Formlang'),
            'singular_name' => __('youforms', 'Cope_Formlang'),
            'menu_name' => __('youForms free','Cope_Formlang'),
            'name_admin_bar' => __('youForms free','Cope_Formlang'),
            'archives' => __('youForms Archives','Cope_Formlang'),
            'attributes' => __('youForms Attributes','Cope_Formlang'),
            'parent_item_colon' => __('Parent sales:','Cope_Formlang'),
            'all_items' => __('Your templates','Cope_Formlang'),
            'add_new_item' => __('Add new sales form','Cope_Formlang'),
            'add_new' => __('Add new','Cope_Formlang'),
            'new_item' => __('New form','Cope_Formlang'),
            'edit_item' => __('Edit form','Cope_Formlang'),
            'update_item' => __('Update form','Cope_Formlang'),
            'view_item' => __('View form','Cope_Formlang'),
            'view_items' => __('View form','Cope_Formlang'),
            'search_items' => __('Search form','Cope_Formlang'),
            'not_found' => __('Not found','Cope_Formlang'),
            'not_found_in_trash' => __('Not found in Trash','Cope_Formlang'),
            'featured_image' => __('Featured Image','Cope_Formlang'),
            'set_featured_image' => __('Set featured image','Cope_Formlang'),
            'remove_featured_image' => __('Remove featured image','Cope_Formlang'),
            'use_featured_image' => __('Use as featured image','Cope_Formlang'),
            'insert_into_item' => __('Insert item','Cope_Formlang'),
            'uploaded_to_this_item' => __('Uploaded to this item','Cope_Formlang'),
            'items_list' => __('Sales form list','Cope_Formlang'),
            'items_list_navigation' => __('Form list navigation','Cope_Formlang'),
            'filter_items_list' => __('Filter items list','Cope_Formlang'),
        );
        $args = array(
            'label' => __('youForms free for CopeCart','Cope_Formlang'),
            'description' => __('youForms free for CopeCart','Cope_Formlang'),
            'labels' => $labels,
            'supports' => array('title', 'editor'),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-feedback',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite' => false,
            'capability_type' => 'page',
        );
        register_post_type('youforms', $args);
    }

}
