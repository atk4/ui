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
    public $thumbnailRegion = 'AfterAfterInput';

    /** @var string|null The default thumbnail source. */
    public $defaultSrc;

    protected function init(): void
    {
        parent::init();

        if (!$this->accept) {
            $this->accept = ['.jpg', '.jpeg', '.png'];
        }

        $this->add($this->getThumbnail(), $this->thumbnailRegion);
    }

    public function getThumbnail(): View
    {
        if ($this->thumbnail === null) {
            $this->thumbnail = (new View(['element' => 'img', 'class' => ['right', 'floated', 'image'], 'ui' => true]))
                ->setAttr(['width' => 36, 'height' => 36]);

            if ($this->defaultSrc) {
                $this->thumbnail->setAttr(['src' => $this->defaultSrc]);
            }
        }

        return $this->thumbnail;
    }

    /**
     * Set the thumbnail img src value.
     */
    public function setThumbnailSrc(string $src): void
    {
        $this->thumbnail->setAttr(['src' => $src]);
        $action = $this->thumbnail->js();
        $action->attr('src', $src);
        $this->addJsAction($action);
    }

    /**
     * Clear the thumbnail src.
     */
    public function clearThumbnail(): void
    {
        $action = $this->thumbnail->js();
        if ($this->defaultSrc !== null) {
            $action->attr('src', $this->defaultSrc);
        } else {
            $action->removeAttr('src');
        }
        $this->addJsAction($action);
    }
}
