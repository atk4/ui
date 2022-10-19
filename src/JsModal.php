<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class JsModal extends JsExpression
{
    /**
     * @param string|null        $title when empty, header will be removed in modal
     * @param string|VirtualPage $url
     */
    public function __construct($title, $url, array $args = [], string $dataType = 'json')
    {
        if ($url instanceof VirtualPage) {
            $url = $url->getJsUrl('cut');
        }

        parent::__construct('$(this).atkCreateModal([arg])', ['arg' => ['url' => $url, 'title' => $title, 'dataType' => $dataType, 'urlOptions' => $args]]);

        if (!$title) {
            $this->removeHeader();
        }
    }

    /**
     * Set additionnal option for this JsModal.
     *
     * Valuable option are headerCss and label:
     *  'headerCss' -> customize css class name for the header.
     *      ex: changing color text for header
     *      $jsModal->setOption('headerCss', 'ui blue header');
     *
     *  'loadingLabel' -> set the text loader value.
     *      ex: changing default 'Loading...' for no text
     *      $jsModal->setOption('loadingLabel', '');
     *
     *   'modalCss' -> customize css class name for the entire modal.
     *      ex: making modal fullscreen
     *      $jsModal->setOption('modalCss', 'fullscreen');
     *
     *   'contentCss' -> customize css class name for Modal content.
     *       ex: making content scrollable
     *       $jsModal->setOption('contentCss', 'scrolling');
     *       Note: Default to 'image' for backward compatibility.
     *
     * You can set option individually or supply an array.
     *
     * @param string|array $options
     * @param ($options is array ? never : mixed) $value
     *
     * @return $this
     */
    public function setOption($options, $value = null)
    {
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->args['arg'][$k] = $v;
            }
        } else {
            $this->args['arg'][$options] = $value;
        }

        return $this;
    }

    /**
     * Clear header class and title.
     *
     * @return $this
     */
    public function removeHeader()
    {
        $this->args['arg']['headerCss'] = '';
        $this->args['arg']['title'] = '';

        return $this;
    }
}
