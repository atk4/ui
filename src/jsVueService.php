<?php
/**
 * Shortcut handler for calling method of
 * the atk javascript vue service.
 */

namespace atk4\ui;

class jsVueService
{
    /**
     * The atk vue service to talk too.
     *
     * @var jsChain
     */
    public $service = null;

    /**
     * jsVueService constructor.
     */
    public function __construct()
    {
        $this->service = new jsChain('atk.vueService');
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
     * Emit an event that other component can listen too.
     * Allow Vue instances to talk to each other.
     *
     * This output js: atk.vueService.emitEvent("eventName", {});
     *
     * @return mixed
     */
    public function emitEvent($eventName, array $data = [])
    {
        return $this->service->emitEvent($eventName, $data);
    }

    /**
     * Make Vue aware of externally loaded components.
     * The component name must be accessible in javascript using the window namespace.
     * ex: window['SemanticUIVue'].
     *
     * @param string $component The component name to use with Vue.
     *
     * @return mixed
     */
    public function useComponent($component)
    {
        return $this->service->useComponent($component);
    }
}
