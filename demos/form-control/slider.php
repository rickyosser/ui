<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Header;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Sliders', 'size' => 2]);

$form = Form::addTo($app);

$form->addControl(
    'slider_simple1',
    [
        Form\Control\Slider::class,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'caption' => 'Simple Slider',
    ]
);

$slider2 = $form->addControl(
    'slider_simple2',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'caption' => 'Blue ticked and labeled simple slider',
    ]
);
$slider2->slider->addClass('blue');

$form->addControl(
    'slider_simple3',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 5,
        'smooth' => true,
        'caption' => 'Smooth Blue ticked and labeled simple slider',
    ]
);

$form->addControl(
    'slider_ranged1',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 10,
        'step' => 1,
        'start' => 3,
        'end' => 6,
        'smooth' => true,
        'showThumbTooltip' => true,
        'tooltipConfig' =>  ['position' => 'top center', 'variation' => 'big blue'],
        'color' => 'blue',
        'caption' => 'Smooth Blue, ticked and labeled with tool-tips Ranged slider',
    ]
);

$form->addControl(
    'slider_custom1',
    [
        Form\Control\Slider::class,
        'labeled' => true,
        'ticked' => true,
        'min' => 0,
        'max' => 6,
        'step' => 1,
        'start' => 2,
        'smooth' => true,
        'color' => 'green',
        'customLabels' => [
            'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'
        ],
        'caption' => 'Smooth Green, ticked and custom labeled slider',
    ]
);

$form->onSubmit(static function (Form $form) {
    print_r($form->entity->get());
});
