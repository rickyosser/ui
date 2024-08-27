<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\View;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsFunction;
use Atk4\Ui\Js\JsReload;

class Slider extends Input
{
    public string $inputType = 'hidden';

    /** The lowest value the slider can be. */
    public int $min = 0;

    /** The max value the slider can be. */
    public int $max = 10;

    /** @var float|null The slider step. Set to 0 to disable step. */
    public $step = 0;

    /** The value the slider will start at. */
    public int $start = 0;

    /** @var int|null The second value to set in case of a range slider. */
    public $end = null;

    /** @var int|false Makes sure that the two thumbs of a range slider always need to have a difference of the given value. */
    public $minRange = false;

    /**  @var int|false Makes sure that the two thumbs of a range slider don't exceed a difference of the given value. */
    public $maxRange = false;

    /** @var 'number'|'letter' The type of label to display for a labeled slider. Can be number or letter. */
    public $labelType = 'number';

    /** @var array|null An array of label values which restrict the displayed labels to only those which are defined. */
    public $restrictedLabels = null;

    /** Whether a tooltip should be shown to the thumb(s) on hover. Will contain the current slider value. */
    public bool $showThumbTooltip = false;
    
    /**
     * Tooltip configuration used when showThumbTooltip is true
     * Refer to Tooltip Variations for possible values.
     *
     * @var array|null
     */
    public $tooltipConfig = null;

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

    /** @var object */
    private $slider;

    /** @var object */
    private $owner;

    /** @var object */
    private $firstInput;

    /** @var object */
    private $secondInput;
    
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->owner = $this->getOwner();

        $this->slider = View::addTo($this->owner)->addClass('ui slider');

        if ($this->end) {
            $this->slider->addClass('range');
        }

        if ($this->ticked) {
            $this->slider->addClass('ticked');
        }

        if ($this->labeled) {
            $this->slider->addClass('labeled');
        }

        if ($this->bottom) {
            $this->slider->addClass('bottom aligned');
        }

        $sliderSettings = [];
        $sliderSettings = $sliderSettings + ['min' => $this->min];
        $sliderSettings = $sliderSettings + ['max' => $this->max];
        if ($this->start) {
            $sliderSettings = $sliderSettings + ['start' => $this->start];
        }
        if ($this->step) {
            $sliderSettings = $sliderSettings + ['step' => $this->step];
        }
        if ($this->end) {
            $sliderSettings = $sliderSettings + ['end' => $this->end];
            if ($this->minRange) {
                $sliderSettings = $sliderSettings + ['minRange' => $this->minRange];
            }
            if ($this->maxRange) {
                $sliderSettings = $sliderSettings + ['maxRange' => $this->maxRange];
            }
        }
        $sliderSettings = $sliderSettings + ['labelType' => $this->labelType];
        if ($this->restrictedLabels) {
            $sliderSettings = $sliderSettings + ['restrictedLabels' => $this->restrictedLabels];
        }
        if ($this->showThumbTooltip) {
            $sliderSettings = $sliderSettings + ['showThumbTooltip' => $this->showThumbTooltip];
            if ($this->tooltipConfig) {
                $sliderSettings = $sliderSettings + ['tooltipConfig' => $this->tooltipConfig];
            }
        }
        $sliderSettings = $sliderSettings + ['showLabelTicks' => $this->showLabelTicks];
        $sliderSettings = $sliderSettings + ['smooth' => $this->smooth];
        $sliderSettings = $sliderSettings + ['autoAdjustLabels' => $this->autoAdjustLabels];
        $sliderSettings = $sliderSettings + ['labelDistance' => $this->labelDistance];
        $sliderSettings = $sliderSettings + ['decimalPlaces' => $this->decimalPlaces];
        $sliderSettings = $sliderSettings + ['pageMultiplier' => $this->pageMultiplier];
        $sliderSettings = $sliderSettings + ['decimalPlaces' => $this->decimalPlaces];
        $sliderSettings = $sliderSettings + ['preventCrossover' => $this->preventCrossover];
        
        /*
         * First input value, always present
         */
        $this->firstInput = $this->owner->addControl(
            $this->shortName . '_first',
            [
                Hidden::class
            ]
        )->set($this->start);

        $onChange = [
            'onChange' => new JsFunction(
                ['v'],
                [
                    new JsExpression($this->firstInput->js()->find('input')->jsRender().".val($('div#" . $this->slider->getHtmlId() . "').slider('get thumbValue', 'first'))"),
                ])
        ];
        
        /*
         * Second input value, optional, depending on $this->end
         */
        if ($this->end) {
            $this->secondInput = $this->owner->addControl(
                $this->shortName . '_second',
                [
                    Hidden::class
                ]
            )->set($this->end);
            
            $onChange = [
                'onChange' => new JsFunction(
                    ['v'],
                    [
                        new JsExpression($this->firstInput->js()->find('input')->jsRender().".val($('div#" . $this->slider->getHtmlId() . "').slider('get thumbValue', 'first'))"),
                        new JsExpression($this->secondInput->js()->find('input')->jsRender().".val($('div#" . $this->slider->getHtmlId() . "').slider('get thumbValue', 'second'))")
                    ])
            ];
        }
        $sliderSettings = $sliderSettings + $onChange;

        $this->slider->js(true)->slider(
            $sliderSettings,
        );
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        parent::recursiveRender();
    }
}
