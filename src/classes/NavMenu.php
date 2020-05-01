<?php
namespace UpAssist\WordPress;

class NavMenu extends Singleton
{
    /**
     * @param string $name Nav Menu name
     */
    public function register($name) {
        /** @var Theme $theme */
        $theme = Theme::getInstance();
        register_nav_menu(strtolower($name), __($name . ' menu', $theme->getName()));
    }
}
