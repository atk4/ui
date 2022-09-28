<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * Shortcut handler for calling method of
 * the atk javascript vue service.
 */
class JsVueService
{
    use WarnDynamicPropertyTrait;

    /** @var JsChain The atk vue service to talk too. */
    public $service;

    public function __construct()
    {
        $this->service = new JsChain('atk.vueService');
    }

    /**
     * Create a new Vue instance using a component managed by ATK.
     *
     * This output js: atk.vueService.createAtkVue("id", "component", {});
     */
    public function createAtkVue(string $id, string $component, array $data = []): JsChain
    {
        return $this->service->createAtkVue($id, $component, $data);
    }

    /**
     * Create a new Vue instance using an external component.
     * External component should be load via js file and define properly.
     *
     * This output js: atk.vueService.createVue("id", "component", {});
     */
    public function createVue(string $id, string $componentName, string $component, array $data = []): JsChain
    {
        return $this->service->createVue($id, $componentName, $component, $data);
    }

    /**
     * Make Vue aware of externally loaded components.
     * The component name must be accessible in javascript using the window namespace.
     * ex: window['xxx'].
     */
    public function useComponent(string $component): JsChain
    {
        return $this->service->useComponent($component);
    }
}
