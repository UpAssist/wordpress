<?php
namespace UpAssist\WordPress;

/**
 * Class AbstractShortcode
 * @package UpAssist\WordPress
 */
abstract class AbstractShortcode extends Singleton
{
    /**
     * AbstractShortcode constructor.
     */
    public function __construct()
    {
        add_shortcode($this->getName(), [$this, 'shortcode']);
        parent::__construct();
    }

    /**
     * @param array $atts The parameters
     * @return string The rendered shortcode
     */
    public function shortcode($atts) {
        return '';
    }

    /**
     * @return string
     */
    protected function getName()
    {
        $shortcodeClassName = explode('\\', get_class($this));
        return strtolower(str_replace('Shortcode', '', end($shortcodeClassName)));
    }
}
