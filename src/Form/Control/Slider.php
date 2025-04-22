<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\View;

class Slider extends Input
{
    public string $defaultInputTemplateVertical = 'form/layout/slider-vertical.html';

    public string $inputType = 'hidden';

    /** @var float|string The lowest value the slider can be. */
    public $min = 0;

    /** @var float|string The max value the slider can be. */
    public $max = 10;

    /** @var float|null The slider step. Set to 0 to disable step. */
    public $step = 0;

    /** @var float The value the slider will start with. */
    public float $start = 0;

    /** @var float|null The end value to set in case of a range slider. */
    public ?float $end = null;

    /** @var float|false Makes sure that the two thumbs of a range slider always need to have a difference of the given value. */
    public $minRange = false;

    /** @var float|false Makes sure that the two thumbs of a range slider don't exceed a difference of the given value. */
    public $maxRange = false;

    /** @var 'number'|'letter' The type of label to display for a labeled slider. Can be number or letter. */
    public $labelType = 'number';

    /** @var array<string, mixed>|null An array of label values which restrict the displayed labels to only those which are defined. */
    public $restrictedLabels;

    /** @vat bool Whether a tooltip should be shown to the thumb(s) on hover. Will contain the current slider value. */
    public bool $showThumbTooltip = false;

    /**
     * Tooltip configuration used when showThumbTooltip is true
     * Refer to Tooltip Variations for possible values.
     *
     * @var array<string, mixed>|null
     */
    public $tooltipConfig;

    /**
     * Show ticks on a labeled slider.
     *'always'will always show the ticks for all labels (even if not shown)
     * true will display the ticks only if the related label is also shown.
     *
     * @var 'always'|bool
     */
    public $showLabelTicks = false;

    /** Define smoothness when the slider is moving. */
    public bool $smooth = false;

    /** Whether labels should auto adjust on window resize. */
    public bool $autoAdjustLabels = true;

    /** The distance between labels. */
    public int $labelDistance = 100;

    /** Number of decimals to use with an unstepped slider. */
    public int $decimalPlaces = 2;

    /** Page up/down multiplier. Define how many more times the steps to take on page up/down press. */
    public int $pageMultiplier = 2;

    /** Prevents the lower thumb to crossover the thumb handle. */
    public bool $preventCrossover = true;

    /** Settings for the slider-class */
    /** Whether the slider should be ticked or not. */
    public bool $ticked = false;

    /** Whether the slider should be labeled or not. */
    public bool $labeled = false;

    /** Whether the ticks and labels should be at the bottom. */
    public bool $bottom = false;

    /** Whether the slider should be vertical. */
    public bool $vertical = false;

    /** @var int Default height for vertical slider. */
    public $verticalHeight = 200;

    /** @var bool Right aligned vertical slider. Default true. */
    public bool $verticalRightAligned = true;

    /** @var bool Default reversed slider. */
    public bool $reversed = false;

    /**
     * Custom interpreted labels.
     * Provide an array which will be used for populating the labels.
     *
     * @var array<string, mixed>|null
     */
    public $customLabels;

    /** @var string|null Color of the slider. */
    public $color;

    /**
     * Size of the slider.
     * Can be: small, large, big, not set is normal size.
     *
     * @var string
     */
    public $size;

    /** @var object The input object so it can be addressed from the code, ie addClass. */
    public object $input;

    /** @var object */
    private $owner;

    /** @var object */
    private $endInput;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->owner = $this->getOwner();

        $this->input = View::addTo($this->owner);

        if ($this->reversed) {
            $this->input->ui = 'reversed';
        }

        if (!$this->vertical) {
            $this->input->ui .= ' slider';
        } else {
            $this->owner->defaultInputTemplate = $this->defaultInputTemplateVertical; // @phpstan-ignore variable.undefined
            $this->owner->inputTemplate = $this->getApp()->loadTemplate($this->defaultInputTemplateVertical); // @phpstan-ignore variable.undefined
            $this->input->ui .= ' vertical slider';
            $this->input->setAttr(['style' => 'height: ' . $this->verticalHeight . 'px']);
            if ($this->verticalRightAligned) {
                $this->input->addClass('right aligned');
            }
        }

        if ($this->end) {
            $this->input->addClass('range');
        }

        if ($this->ticked) {
            $this->input->addClass('ticked');
        }

        if ($this->labeled) {
            $this->input->addClass('labeled');
        }

        if ($this->bottom) {
            $this->input->addClass('bottom aligned');
        }

        if ($this->color) {
            $this->input->addClass($this->color);
        }

        if ($this->size) {
            $this->input->addClass($this->size);
        }

        $sliderSettings = [];
        $sliderSettings['min'] = $this->min;
        $sliderSettings['max'] = $this->max;
        if ($this->start) {
            $sliderSettings['start'] = $this->start;
        }
        if ($this->step) {
            $sliderSettings['step'] = $this->step;
        }
        if ($this->end) {
            $sliderSettings['end'] = $this->end;
            if ($this->minRange) {
                $sliderSettings['minRange'] = $this->minRange;
            }
            if ($this->maxRange) {
                $sliderSettings['maxRange'] = $this->maxRange;
            }
        }
        $sliderSettings['labelType'] = $this->labelType;
        if ($this->restrictedLabels) {
            $sliderSettings['restrictedLabels'] = $this->restrictedLabels;
        }
        if ($this->showThumbTooltip) {
            $sliderSettings['showThumbTooltip'] = $this->showThumbTooltip;
            if ($this->tooltipConfig) {
                $sliderSettings['tooltipConfig'] = $this->tooltipConfig;
            }
        }
        $sliderSettings['showLabelTicks'] = $this->showLabelTicks;
        $sliderSettings['smooth'] = $this->smooth;
        $sliderSettings['autoAdjustLabels'] = $this->autoAdjustLabels;
        $sliderSettings['labelDistance'] = $this->labelDistance;
        $sliderSettings['decimalPlaces'] = $this->decimalPlaces;
        $sliderSettings['pageMultiplier'] = $this->pageMultiplier;
        $sliderSettings['decimalPlaces'] = $this->decimalPlaces;
        $sliderSettings['preventCrossover'] = $this->preventCrossover;

        if ($this->customLabels) {
            $sliderSettings['interpretLabel'] = new JsFunction(
                [
                    'value',
                ],
                [
                    new JsExpression(
                        <<<'EOF'
                                var labels = [];
                                return labels.slice(value, value + 1);
                            EOF,
                        [
                            $this->customLabels,
                        ],
                    ),
                ],
            );
        }

        if ($this->disabled || $this->readOnly) {
            $this->input->addClass('disabled');
        }

        if (!$this->disabled) {
            // Set first input value, always present
            $this->set($this->start);

            if (!$this->readOnly) {
                $onChange = [
                    'onChange' => new JsFunction(
                        ['v'],
                        [
                            new JsExpression(
                                $this->js()->find('input')->jsRender() . '.val($(\'div#\' + [] + \'\').slider(\'get thumbValue\', \'first\'))',
                                [$this->input->getHtmlId()]
                            ),
                        ]
                    ),
                ];
            }

            // End input value, optional, depending on $this->end
            if ($this->end) {
                $this->endInput = $this->owner->addControl(
                    $this->shortName . '_end',
                    [
                        Hidden::class,
                    ]
                )->set($this->end);

                if (!$this->readOnly) {
                    $onChange = [
                        'onChange' => new JsFunction(
                            ['v'],
                            [
                                new JsExpression(
                                    $this->js()->find('input')->jsRender() . '.val($(\'div#\' + [] + \'\').slider(\'get thumbValue\', \'first\'))',
                                    [$this->input->getHtmlId()]
                                ),
                                new JsExpression(
                                    $this->endInput->js()->find('input')->jsRender() . '.val($(\'div#\' + [] + \'\').slider(\'get thumbValue\', \'second\'))',
                                    [$this->input->getHtmlId()]
                                ),
                            ]
                        ),
                    ];
                }
            }
        }
        if ($this->readOnly) {
            $this->addClass('readOnly');
            $this->setAttr(['readOnly' => '']);
        }
        if (isset($onChange)) {
            $sliderSettings += $onChange;
        }
        $this->input->js(true)->slider(
            $sliderSettings,
        );
    }
}
