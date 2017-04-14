<?php

namespace Twigstrap\View;

use WyriHaximus\TwigView\View\TwigView;

/**
 * UIView: the customised BootstrapUI View class.
 *
 * It customises the View::$layout to the BootstrapUI's layout and loads
 * BootstrapUI's helpers.
 *
 * @property \Twigstrap\View\Helper\FlashHelper $Flash
 * @property \Twigstrap\View\Helper\FormHelper $Form
 * @property \Twigstrap\View\Helper\HtmlHelper $Html
 * @property \Twigstrap\View\Helper\PaginatorHelper $Paginator
 */
class TwigstrapView extends TwigView
{
    use TwigstrapViewTrait;

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->initializeUI();
    }
}
