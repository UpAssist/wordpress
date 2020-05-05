<?php

namespace UpAssist\WordPress;

class CustomPostType
{
    /** @var Theme */
    protected $theme;

    /** @var array $queryArgs */
    protected $queryArgs = [];

    /** @var string $post_type */
    protected $post_type;

    /** @var string $name */
    protected $name;

    public function __construct(
        $name,
        $singular_name,
        $description,
        $args,
        $queryArgs = [],
        $customizerOption = true
    )
    {
        $this->theme = Theme::getInstance();
        $this->post_type = strtolower($singular_name);
        $this->name = $name;
        // Set UI labels for Custom Post Type
        $labels = array(
            'name' => _x($name, 'Post Type General Name', $this->theme->getName()),
            'singular_name' => _x($singular_name, 'Post Type Singular Name', $this->theme->getName()),
            'menu_name' => __($name, $this->theme->getName()),
            'parent_item_colon' => __('Parent ' . $singular_name, $this->theme->getName()),
            'all_items' => __('All ' . $name, $this->theme->getName()),
            'view_item' => __('View ' . $singular_name, $this->theme->getName()),
            'add_new_item' => __('Add New ' . $singular_name, $this->theme->getName()),
            'add_new' => __('Add New', $this->theme->getName()),
            'edit_item' => __('Edit ' . $singular_name, $this->theme->getName()),
            'update_item' => __('Update ' . $singular_name, $this->theme->getName()),
            'search_items' => __('Search ' . $singular_name, $this->theme->getName()),
            'not_found' => __('Not found', $this->theme->getName()),
            'not_found_in_trash' => __('Not found in trash', $this->theme->getName()),
        );

        // Set other options for Custom Post Type
        $defaultArgs = array(
            'label' => __($name, $this->theme->getName()),
            'description' => __($description, $this->theme->getName()),
            'labels' => $labels,
            // Features this CPT supports in Post Editor
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'author',
                'thumbnail',
                'comments',
                'revisions',
                'custom-fields',
            ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies' => [],
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-edit',
            'rewrite' => [
                'slug' => strtolower($name),
                'with_front' => false
            ],
        );

        $args = array_merge($defaultArgs, $args);

        register_post_type($singular_name, $args);

        // Allow for custom querying
        if (!empty($queryArgs) || !is_admin()) {
            $this->queryArgs = $queryArgs;

            add_action('pre_get_posts', [
                $this,
                'customQuery'
            ], 0);
        }

        // Setup customizer option
        if ($customizerOption) {
            add_action('customize_register', [$this, 'registerCustomizerOption']);
        }
    }

    /**
     * 'pre_get_posts' hook
     * @param \WP_Query $query
     */
    public function customQuery($query)
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        if (is_post_type_archive($this->post_type) && !empty($this->queryArgs)) {
            foreach ($this->queryArgs as $key => $value) {
                $query->set($key, $value);
            }
            return;
        }
    }

    public function registerCustomizerOption(\WP_Customize_Manager $wp_customize)
    {
        // Do stuff with $wp_customize, the WP_Customize_Manager object.
        $wp_customize->add_section($this->post_type, [
            'title' => __($this->name, $this->theme->getName()),
            'capability' => 'edit_theme_options',
        ]);

        $wp_customize->add_setting(
            sprintf('page_for_%s', strtolower($this->name)),
            array(
                'type' => 'option',
                'capability' => 'manage_options',
            )
        );

        $wp_customize->add_control(
            sprintf('page_for_%s', strtolower($this->name)),
            array(
                'label' => __($this->name . ' archive page', $this->theme->getName()),
                'section' => $this->post_type,
                'type' => 'dropdown-pages',
                'allow_addition' => true,
            )
        );
    }
}
