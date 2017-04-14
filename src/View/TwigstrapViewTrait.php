<?php

namespace Twigstrap\View;

/**
 * TwigstrapViewTrait: Trait that loads the custom Twigstrap helpers and sets View
 * layout to the Twigstrap's one.
 */
trait TwigstrapViewTrait
{
    /**
     * Initialization hook method.
     *
     * @param array $options Associative array with valid keys:
     *   - `layout`:
     *      - If not set or true will use the plugin's layout
     *      - If a layout name passed it will be used
     *      - If false do nothing (will keep your layout)
     *
     * @return void
     */
    public function initializeUI(array $options = [])
    {
        if ((!isset($options['layout']) || $options['layout'] === true) &&
            $this->layout === 'default'
        ) {
            $this->layout = 'default';
        } elseif (isset($options['layout']) && is_string($options['layout'])) {
            $this->layout = $options['layout'];
        }

        $this->loadHelper('Html', ['className' => 'Twigstrap.Html']);
        $this->loadHelper('Form', ['className' => 'Twigstrap.Form']);
        $this->loadHelper('Flash', ['className' => 'Twigstrap.Flash']);
        $this->loadHelper('Paginator', ['className' => 'Twigstrap.Paginator']);
    }
}
