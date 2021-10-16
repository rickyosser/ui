<?php

declare(strict_types=1);
/**
 * Behat testing.
 * Test for triggerOnReload = false for Callback.
 */

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$m = (new CountryLock($app->db))->setLimit(5);

$vp = \Atk4\Ui\VirtualPage::addTo($app);
$vp->cb->triggerOnReload = false;

$form = Form::addTo($vp);
$form->setModel($m->tryLoadAny(), [$m->fieldName()->name]);
$form->getControl($m->fieldName()->name)->caption = 'TestName';

$table = \Atk4\Ui\Table::addTo($app);
$table->setModel($m);

$button = Button::addTo($app, ['First', ['ui' => 'atk-test']]);
$button->on('click', new \Atk4\Ui\JsModal('Edit First Record', $vp));

$form->onSubmit(function ($form) use ($table) {
    $form->model->save();

    return [
        $table->jsReload(),
        new JsToast('Save'),
        (new Jquery('.ui.modal.visible.active.front'))->modal('hide'),
    ];
});
