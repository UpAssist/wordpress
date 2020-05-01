<?php


namespace UpAssist\Wordpress;


class Theme
{
    /**
     * @var string Theme name (text domain)
     */
    protected $themeName;

    /**
     * Theme constructor.
     * @param string $themeName
     * @param string $locale
     * @param array|null $support
     * @param array $styles
     */
    public function __construct($themeName, $locale = 'nl_NL', array $support = null, array $styles)
    {
        setlocale(LC_ALL, $locale);
        $this->loadTextDomain();
        $this->addThemeSupport($support);
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

    public function enqueueStyles(array $styles)
    {
        foreach ($styles as $style) {

        }
    }
}
