<?php

namespace atk4\ui\FormLayout;

/**
 * A form layout with Accordion view.
 * Field can be added to each accordion section.
 *
 * @package atk4\ui\FormLayout
 */
class Accordion extends Generic
{
    /**
     * The accordion view.
     *
     * @var null|Accordion
     */
    public $accordion = null;

    /**
     * The Accordion style type.
     * @var string
     */
    public $type = 'styled fluid';

    /**
     * The form layout class use by each accordion section.
     *
     * @var string
     */
    public $sectionLayoutClass = 'FormLayout/Generic';

    public function init()
    {
        parent::init();
        $this->accordion = $this->add(['Accordion', 'type' => $this->type]);
        $this->add(['ui' => 'hidden divider']);
    }

    /**
     * Add an accordion section to this layout.
     * Return an accordion section containing
     * a form layout view where field can be added to.
     *
     * @param $title
     *
     * @return mixed
     */
    public function addSection($title)
    {
        $v = $this->accordion->addSection($title);

        return $v->add([$this->sectionLayoutClass, 'form' => $this->form]);
    }

    /**
     * Accordion getter.
     * Usefull when setting up accordion js action.
     *
     * @return Accordion|null
     */
    public function getAccordion()
    {
        return $this->accordion;
    }

    /**
     * Return the accordion section from a layout section.
     *
     * @param AccordionSection $layoutSection
     *
     * @return mixed
     */
    public function getSection($layoutSection)
    {
        return $layoutSection->owner;
    }
}
