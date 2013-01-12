<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ bundleNamespace }}\Event;

use {{ bundleNamespace }}\Model\{{ entity }}Interface;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class {{ entity }}Event extends Event
{
    private ${{ entityCC }};
    private $request;

    public function __construct({{ entity }}Interface ${{ entityCC }}, Request $request)
    {
        $this->{{ entityCC }} = ${{ entityCC }};
        $this->request = $request;
    }

    /**
     * @return {{ entity }}Interface
     */
    public function get{{ entityCC }}()
    {
        return $this->{{ entityCC }};
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
