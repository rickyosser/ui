<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Message;
use Atk4\Ui\Text;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';


$form = Form::addTo($app);
// standard with model: use idField as Value, titleField as Title for each Dropdown option
$values1 = [
    '1' => ['title' => 'One', 'selected' => false],
    '2' => 'Two',
    '3' => 'Three',
    '4' => ['title' => 'Four', 'selected' => true]
];

$values2 = [
    '5' => 'Five',
    '6' => 'Six',
    '7' => 'Seven',
    '8' => 'Eight'
];

$form->addControl('withValues', [
    Form\Control\Listboxes::class,
    'caption' => 'Dual Listbox with data from values',
    'values' => $values1
]);

$form->addControl('withValues2', [
    Form\Control\Listboxes::class,
    'caption' => 'Dual Listbox with data from values',
    'values' => $values2
]);

$form->addControl('multi', [
    Form\Control\Dropdown::class,
    'caption' => 'Multiple selection',
    'placeholder' => 'Choose has many options needed',
    'multiple' => true,
    'values' => ['default' => 'Default', 'option1' => 'Option 1', 'option2' => 'Option 2'],
]);

$form->onSubmit(static function (Form $form) use ($app) {
    $message = $app->encodeJson($form->entity->get());
    $view = new Message('Values:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($message);

    return $view;
});

$msg = Message::addTo($app, [
    'Page end'
]);
