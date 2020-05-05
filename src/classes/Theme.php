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

    /** @var array|null $support */
    protected $support;

    /** @var array $dependancies */
    protected $dependancies;

    /**
     * Theme constructor.
     * @param string $themeName
     * @param string $locale
     * @param array|null $support
     * @param array $styles
     */
    public function __construct($themeName = 'upassist', $locale = 'nl_NL', array $support = [])
    {
        parent::__construct();

        setlocale(LC_TIME, $locale);
        $this->locale = $locale;
        $this->support = $support;
        $this->themeName = $themeName;

        add_action('after_setup_theme', [
            $this,
            'setup'
        ]);
    }

    public function renderThemeSupport()
    {
        if (empty($this->getSupport())) {
            return;
        }
        foreach ($this->getSupport() as $key => $value) {
            add_theme_support($key, $value);
        }
    }

    /**
     * Add support for translations
     */
    public function loadTextDomain()
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

    /** @return array */
    public function getSupport()
    {
        return $this->support;
    }

    public function registerDependency($functionName, $pluginName)
    {
        if (!function_exists($functionName)) {
            $this->dependancies[] = '<div class="error"><p>' . __('Warning: The theme needs Plugin `' . $pluginName . '` to function', $this->getName()) . '</p></div>';
        }

        add_action('admin_notices', [
            $this,
            'renderDependancies'
        ]);
    }

    public function registerStylesheets(array $styles = null)
    {
        if ($styles) {
            $this->styles = $styles;
            add_action('wp_enqueue_scripts', [
                $this,
                'enqueueStyles'
            ]);
        }
    }

    public function registerScripts(array $scripts = null)
    {
        if ($scripts) {
            $this->scripts = $scripts;
            add_action('wp_enqueue_scripts', [
                $this,
                'enqueueScripts'
            ]);
        }
    }

    public function registerCustomPostType($name, $singular_name, $description, $args, $queryArgs)
    {
        new CustomPostType(
            $name,
            $singular_name,
            $description,
            $args,
            $queryArgs
        );
    }

    public function renderDependancies()
    {
        if ($this->dependancies) {
            foreach ($this->dependancies as $dependancy) {
                echo $dependancy;
            }
        }
    }

    public function utilities() {
        return Utilities::getInstance();
    }
    public function setup()
    {
        $this->loadTextDomain();
        $this->renderThemeSupport();
    }
}
