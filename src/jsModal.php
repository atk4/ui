<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsModal extends jsExpression
{
    /**
     * jsModal constructor.
     *
     * @param $title       //When empty, header will be remove in modal.
     * @param $url
     * @param array $args
     * @param string $mode
     */
    public function __construct($title, $url, $args = [], $mode = 'json')
    {
        if ($url instanceof VirtualPage) {
            $url = $url->getJSURL('cut');
        }

        parent::__construct('$(this).atkCreateModal([arg])', ['arg' => ['uri' => $url, 'title' => $title, 'mode' => $mode, 'uri_options' => $args]]);

        if (empty($title)) {
            $this->removeHeader();
        }
    }

    /**
     * Set additionnal option for this jsModal.
     *
     * Valuable option are headerCss and label:
     *  'headerCss' -> customize css class name for the header.
     *      ex: changing color text for header
     *      $jsModal->setOption('headerCss', 'ui blue header');
     *
     *  'label' -> set the text loader value.
     *      ex: changing default 'Loading...' for no text
     *      $jsModal->setOption('label', '');
     *
     * You can set option individually or supply an array.
     *
     * @param $options
     * @param null $value
     *
     * @return $this
     */
    public function setOption($options, $value = null)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $this->args['arg'][$key] = $value;
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
