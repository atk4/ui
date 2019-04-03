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
     * @param $id
     * @param $component
     * @param array $data
     *
     * @return mixed
     */
    public function createAtkVue($id, $component, $data = [])
    {
        return $this->service->createAtkVue($id, $component, $data);
    }

    /**
     * Create a new Vue instance using an external component.
     * External component should be load via js file and define properly.
     *
     * This output js: atk.vueService.createVue("id","component",{});
     *
     * @param $id
     * @param $component
     * @param array $data
     *
     * @return mixed
     */
    public function createVue($id, $componentName, $component, $data = [])
    {
        return $this->service->createVue($id, $componentName, $component, $data);
    }

    /**
     * Emit an event that other component can listen too.
     * Allow Vue instances to talk to each other.
     *
     * This output js: atk.vueService.emitEvent("eventName", {});
     *
     * @param $eventName
     * @param array $data
     *
     * @return mixed
     */
    public function emitEvent($eventName, $data = [])
    {
        return $this->service->emitEvent($eventName, $data);
    }
}
