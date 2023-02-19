<?php

declare(strict_types=1);

namespace Atk4\Ui\Js;

use Atk4\Core\WarnDynamicPropertyTrait;

/**
 * Shortcut handler for calling method of the atk JS vue service.
 */
class JsVueService
{
    use WarnDynamicPropertyTrait;

    public function createServiceChain(): JsChain
    {
        return new JsChain('atk.vueService');
    }

    /**
     * Create a new Vue instance using a component managed by ATK.
     *
     * This output js: atk.vueService.createAtkVue('id', 'component', {});
     */
    public function createAtkVue(string $id, string $componentName, array $data = []): JsChain
    {
        return $this->createServiceChain()->createAtkVue($id, $componentName, $data);
    }

    /**
     * Create a new Vue instance using an external component.
     * External component should be load via js file and define properly.
     *
     * This output js: atk.vueService.createVue('id', 'component', {}, {});
     */
    public function createVue(string $id, string $componentName, JsExpressionable $component, array $data = []): JsChain
    {
        return $this->createServiceChain()->createVue($id, $componentName, $component, $data);
    }
}
