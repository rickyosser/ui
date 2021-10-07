<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Core\HookTrait;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\JsToast;
use Atk4\Ui\Loader;
use Atk4\Ui\Panel\Right;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/**
 * A Step Action Executor that use a VirtualPage.
 */
class PanelExecutor extends Right implements JsExecutorInterface
{
    use HookTrait;
    use StepExecutorTrait;

    /** @const string */
    public const HOOK_STEP = self::class . '@onStep';

    /** @var array No need for dynamic content. It is manage with step loader. */
    public $dynamic = [];
    public $hasClickAway = false;

    /** @var string */
    public $title;

    /** @var Header */
    public $header;

    /** @var View */
    public $stepList;

    /** @var string[] */
    public $stepListItems = ['args' => 'Fill argument(s)', 'fields' => 'Edit Record(s)', 'preview' => 'Preview', 'final' => 'Complete'];

    protected function init(): void
    {
        parent::init();
        $this->initExecutor();
    }

    protected function initExecutor(): void
    {
        $this->header = Header::addTo($this);
        $this->stepList = View::addTo($this)->addClass('ui horizontal bulleted link list');
    }

    public function getAction(): Model\UserAction
    {
        return $this->action;
    }

    /**
     * Make sure modal id is unique.
     * Since User action can be added via callbacks, we need
     * to make sure that view id is properly set for loader and button
     * js action to run properly.
     */
    public function afterActionInit(Model\UserAction $action): void
    {
        $this->loader = Loader::addTo($this, ['ui' => $this->loaderUi, 'shim' => $this->loaderShim, 'loadEvent' => false]);
        $this->actionData = $this->loader->jsGetStoreData()['session'];
    }

    /**
     * Will associate executor with the action.
     */
    public function setAction(Model\UserAction $action): self
    {
        $this->action = $action;
        $this->afterActionInit($action);

        // get necessary step need prior to execute action.
        if ($this->steps = $this->getSteps($action)) {
            $this->header->set($this->title ?? $action->getDescription());
            $this->step = $this->stickyGet('step') ?? $this->steps[0];
            $this->add($this->createButtonBar($this->action)->addStyle(['text-align' => 'end']));
            $this->addStepList();
        }

        $this->actionInitialized = true;

        return $this;
    }

    public function jsExecute(array $urlArgs = []): array
    {
        $urlArgs['step'] = $this->step;

        return [$this->jsOpen(), $this->loader->jsLoad($urlArgs)];
    }

    /**
     * Perform model action by stepping through args - fields - preview.
     */
    public function executeModelAction(): void
    {
        $id = $this->stickyGet($this->name);
        if ($id && $this->action->appliesTo === Model\UserAction::APPLIES_TO_SINGLE_RECORD) {
            $this->action->setEntity($this->action->getModel()->tryLoad($id));
        }

        if ($this->action->fields === true) {
            $this->action->fields = array_keys($this->action->getModel()->getFields('editable'));
        }

        $this->jsSetBtnState($this->loader, $this->step);
        $this->jsSetListState($this->loader, $this->step);
        $this->runSteps();
    }

    protected function addStepList(): void
    {
        if (count($this->steps) === 1) {
            return;
        }

        foreach ($this->steps as $step) {
            View::addTo($this->stepList)->set($this->stepListItems[$step])->addClass('item')->setAttr(['data-list-item' => $step]);
        }
    }

    protected function jsSetListState(View $view, string $currentStep): void
    {
        $view->js(true, $this->stepList->js()->find('.item')->removeClass('active'));
        foreach ($this->steps as $step) {
            if ($step === $currentStep) {
                $view->js(true, $this->stepList->js()->find("[data-list-item='{$step}']")->addClass('active'));
            }
        }
    }

    /**
     * Return proper js statement need after action execution.
     *
     * @param mixed      $obj
     * @param string|int $id
     */
    protected function jsGetExecute($obj, $id): array
    {
        //@phpstan-ignore-next-line
        $success = $this->jsSuccess instanceof \Closure
            ? ($this->jsSuccess)($this, $this->action->getModel(), $id, $obj)
            : $this->jsSuccess;

        return [
            $this->jsClose(),
            $this->hook(BasicExecutor::HOOK_AFTER_EXECUTE, [$obj, $id]) ?:
                $success ?: new JsToast('Success' . (is_string($obj) ? (': ' . $obj) : '')),
            $this->loader->jsClearStoreData(true),
        ];
    }
}