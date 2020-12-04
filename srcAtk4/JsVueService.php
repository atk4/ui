<?php

declare(strict_types=1);
/**
 * Shortcut handler for calling method of
 * the atk javascript vue service.
 */

namespace Atk4\Ui;

class JsVueService
{
    /**
     * The atk vue service to talk too.
     *
     * @var JsChain
     */
    public $service;

    /**
     * JsVueService constructor.
     */
    public function __construct()
    {
        $this->service = new JsChain('atk.vueService');
    }

    /**
     * Create a new Vue instance using a component managed by ATK.
     *
     * This output js: atk.vueService.createAtkVue("id","component",{});
     *
     * @return mixed
     */
    public function createAtkVue($id, $component, array $data = [])
    {
        return $this->service->createAtkVue($id, $component, $data);
    }

    /**
     * Create a new Vue instance using an external component.
     * External component should be load via js file and define properly.
     *
     * This output js: atk.vueService.createVue("id","component",{});
     *
     * @return mixed
     */
    public function createVue($id, $componentName, $component, array $data = [])
    {
        return $this->service->createVue($id, $componentName, $component, $data);
    }

    /**
     * Make Vue aware of externally loaded components.
     * The component name must be accessible in javascript using the window namespace.
     * ex: window['SemanticUIVue'].
     *
     * @param string $component the component name to use with Vue
     *
     * @return mixed
     */
    public function useComponent($component)
    {
        return $this->service->useComponent($component);
    }
}
