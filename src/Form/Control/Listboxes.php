<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsExpressionable;
use Atk4\Ui\Js\JsFunction;

class Listboxes extends Input
{
    public $defaultTemplate = 'form/control/listboxes.html';

    public string $inputType = 'hidden';

    /**
     * @var array<array-key, mixed>
     */
    public ?array $values = null;


    /**
     * Any other options you'd like to pass to DualListbox.
     * See https://github.com/maykinmedia/dual-listbox for all possible options.
     *
     * @var array<string, mixed>
     */
    public array $options = [
        'upButtonText' => 'Up',
        'downButtonText' => 'Down',
        'searchPlaceholder' => 'Search',
        'removeAllButtonText' => 'Remove all',
        'addAllButtonText' => 'Add all',
        'removeButtonText' => 'Remove',
        'addButtonText' => 'Add',
        'availableTitle' => 'Available options',
        'selectedTitle' => "Selected options",
        'showRemoveAllButton' => true,
        'showAddAllButton' => true,
        'showRemoveButton' => true,
        'showAddButton' => true,
        'enableDoubleClick' => true,
        'draggable' => true,
        'showSortButtons' => true,
    ];


    /**
     * Set Dual-Listbox option.
     *
     * @param mixed $value
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     *
     * @var \Closure<T of Model>(T): array{title: mixed, icon?: mixed}|\Closure(mixed, array-key): array{value: mixed, title: mixed, icon?: mixed}
     */
    public ?\Closure $renderRowFunction = null;

    /** Subtemplate for a single dropdown item. */
    protected HtmlTemplate $_tItem;

    /**
     *
     * @var inputField
     *
     */
    private $availableInput;

    private $selectedInput;

    private $owner;
    private $listbox;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->owner = $this->getOwner();

        /*        $this->availableInput = $this->owner->addControl(
            $this->shortName . '_available_input',
            [
                Hidden::class
            ]
        );//->set($this->availableValues);
     
        $this->selectedInput = $this->owner->addControl(
            $this->shortName . '_selected_input',
            [
                Hidden::class
            ]
        );//->set($this->selectedValues);
        */

        $onChange =  [
            'onChange' => new JsFunction(
                ['v'],
                [
                    new JsExpression($this->js()->find('input')->jsRender().".val($(document.querySelector('.[]').getElementsByTagName('option')))", [$this->shortName]),
                ])
        ];

        print_r($this->entityField);
        $this->_tItem = $this->template->cloneRegion('Item');
        $this->template->del('Item');
    }

    protected function htmlRenderValue(): void
    {

        if ($this->renderRowFunction) {
            foreach ($this->values as $key => $value) {
                $this->_addCallBackRow($value, $key);
            }
        } else {
            $this->_renderItemsForValues();
        }
    }

    #[\Override]
    protected function renderView(): void
    {
        /*
        if ($this->readOnly) {
            $this->options['clickOpens'] = false;
        }
        */

        $this->jsInput(true, new JsExpression('new DualListbox([], [])', ['.' . $this->shortName, $this->options]));
        $this->htmlRenderValue();

        parent::renderView();
    }

    /**
     * Sets the dropdown items from $this->values array.
     */
    protected function _renderItemsForValues(): void
    {
        foreach ($this->values as $key => $val) {
            $this->_tItem->set('value', (string) $key);
            if (is_array($val)) {
                //print_r($val);
                if (array_key_exists('icon', $val)) {
                    $this->_tIcon->set('iconClass', $val['icon'] . ' icon');
                    $this->_tItem->dangerouslySetHtml('Icon', $this->_tIcon->renderToHtml());
                } else {
                    $this->_tItem->del('Icon');
                }
                $this->_tItem->set('title',  $val['title'] || is_numeric($val['title']) ? (string) $val['title'] : '');
                if($val['selected'] == true) {
                    $this->_tItem->set('selected', 'selected');
                }
            } else {
                $this->_tItem->set('title', $val || is_numeric($val) ? (string) $val : '');
            }

            // add item to template
            $this->template->dangerouslyAppendHtml('Item', $this->_tItem->renderToHtml());
        }
    }



    /**
     * @param JsExpressionable $action
     */
    #[\Override]
    public function onChange($action, $default = []): void
    {
        if (!$action instanceof JsBlock) {
            $action = [$action];
        }
        $onChange = new JsExpression($this->js()->find('input')->jsRender().".val($(document.querySelector('.[]').getElementsByTagName('option')))", [$this->shortName]);
        //$this->options['onChange'] = new JsFunction(['date', 'text', 'mode'], $action);
    }

    /**
     * Get the FlatPickr instance of this input in order to
     * get it's properties like selectedDates or run it's methods.
     * Ex: clearing date via JS
     *     $button->on('click', $f->getControl('date')->jsFlatpickr()->clear());.
     *
     * @return JsChain
     */
    public function jsDualListBox(): JsExpressionable
    {
        return (new Jquery('#' . $this->name . '_input'))->get(0)->_DualListbox;
    }

}
