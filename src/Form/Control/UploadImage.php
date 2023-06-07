<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\View;

class UploadImage extends Upload
{
    /** @var View|null The thumbnail view to add to this input. */
    public $thumbnail;

    /** @var string The template region where to add the thumbnail view. */
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

            if ($this->defaultSrc !== null) {
                $this->thumbnail->setAttr(['src' => $this->defaultSrc]);
            }
        }

        return $this->thumbnail;
    }

    public function setThumbnailSrc(string $src): void
    {
        $this->thumbnail->setAttr(['src' => $src]);
        $js = $this->thumbnail->js();
        $js->attr('src', $src);
        $this->addJsAction($js);
    }

    public function clearThumbnail(): void
    {
        $js = $this->thumbnail->js();
        if ($this->defaultSrc !== null) {
            $js->attr('src', $this->defaultSrc);
        } else {
            $js->removeAttr('src');
        }
        $this->addJsAction($js);
    }
}
