<?php

namespace UpAssist\WordPress;

class Theme extends Singleton
{
    /**
     * @var string Theme name (text domain)
     */
    protected $themeName;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var array $styles
     */
    protected $styles;

    /**
     * @var array $scripts
     */
    protected $scripts;

    /**
     * Theme constructor.
     * @param string $themeName
     * @param string $locale
     * @param array|null $support
     * @param array $styles
     */
    public function __construct($themeName = 'upassist', $locale = 'nl_NL', array $support = null, array $styles = null, array $scripts = null)
    {
        setlocale(LC_ALL, $locale);
        $this->locale = $locale;

        $this->loadTextDomain();
        $this->addThemeSupport($support);

        if ($styles) {
            $this->styles = $styles;
            add_action('wp_enqueue_scripts', [
                $this,
                'enqueueStyles'
            ]);
        }

        if ($scripts) {
            $this->scripts = $scripts;
            add_action('wp_enqueue_scripts', [
                $this,
                'enqueueScripts'
            ]);
        }

        parent::__construct();
    }

    /**
     * @param array|null $support
     */
    protected function addThemeSupport(array $support = null)
    {
        if ($support === null) {
            $support = [
                'title-tag',
                'html5' => [
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                ],
                'post-thumbnails',
                'post-formats' => [
                    'aside',
                    'gallery',
                    'quote',
                    'image',
                    'video'
                ]
            ];
        }

        foreach ($support as $key => $value) {
            switch ($key) {
                case 'html5':
                case 'post-formats':
                    add_theme_support($key, $value);
                    break;
                default:
                    add_theme_support($key);
                    break;
            }
        }
    }

    /**
     * Add support for translations
     */
    protected function loadTextDomain()
    {
        load_theme_textdomain($this->themeName, get_template_directory() . '/languages');
    }

    /**
     * @param array|null $styles
     */
    public function enqueueStyles($styles = null)
    {
        if (empty($styles) || $styles === null) {
            $styles = $this->styles;
        }

        foreach ($styles as $style) {
            $file = strpos($style['file'], 'http') !== false ? $style['file'] : get_template_directory_uri() . '/' . $style['file'];
            $depends_on = isset($style['depends_on']) ? $style['depends_on'] : null;
            $version = isset($style['version']) ? $style['version'] : null;
            $media = isset($style['media']) ? $style['media'] : null;
            wp_enqueue_style($style['handle'], $file, $depends_on, $version, $media);
        }
    }

    /**
     * @param array|null $scripts
     */
    public function enqueueScripts($scripts = null)
    {
        if (empty($scripts) || $scripts === null) {
            $scripts = $this->scripts;
        }

        foreach ($scripts as $script) {
            $handle = $script['handle'];
            $file = strpos($script['file'], 'http') !== false ? $script['file'] : get_template_directory_uri() . '/' . $script['file'];
            $depends_on = isset($script['depends_on']) ? $script['depends_on'] : null;
            $version = isset($script['version']) ? $script['version'] : null;
            $in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;
            wp_enqueue_script($handle, $file, $depends_on, $version, $in_footer);
        }
    }

    /**
     * @return NavMenu
     */
    public function navMenu()
    {
        return NavMenu::getInstance();
    }

    /** @return string */
    public function getName()
    {
        return $this->themeName;
    }
}

