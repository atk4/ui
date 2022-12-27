<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\View;

class UploadImage extends Upload
{
    /** @var View|null The thumbnail view to add to this input. */
    public $thumbnail;

    /**
     * The template region where to add the thumbnail view.
     * Default to AfterAfterInput.
     *
     * @var string
     */
    public $thumnailRegion = 'AfterAfterInput';

    /** @var string The default thumbnail source. */
    public $defaultSrc;

    protected function init(): void
    {
        parent::init();

        if (!$this->accept) {
            $this->accept = ['.jpg', '.jpeg', '.png'];
        }

        if (!$this->thumbnail) {
            $this->thumbnail = (new View(['element' => 'img', 'class' => ['right', 'floated', 'image'], 'ui' => true]))
                ->setAttr(['width' => 36, 'height' => 36]);
        }

        if ($this->defaultSrc) {
            $this->thumbnail->setAttr(['src' => $this->defaultSrc]);
        }

        $this->add($this->thumbnail, $this->thumnailRegion);
    }

    /**
     * Set the thumbnail img src value.
     *
     * @param string $src
     */
    public function setThumbnailSrc($src): void
    {
        $this->thumbnail->setAttr(['src' => $src]);
        $action = $this->thumbnail->js();
        $action->attr('src', $src);
        $this->addJsAction($action);
    }

    /**
     * Clear the thumbnail src.
     * You can also supply a default thumbnail src.
     *
     * @param string $defaultThumbnail
     */
    public function clearThumbnail($defaultThumbnail = null): void
    {
        $action = $this->thumbnail->js();
        if ($defaultThumbnail !== null) {
            $action->attr('src', $defaultThumbnail);
        } else {
            $action->removeAttr('src');
        }
        $this->addJsAction($action);
    }
}
