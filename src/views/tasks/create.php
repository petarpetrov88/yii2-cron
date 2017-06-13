<?php
$this->title = 'Task Manager - Create task';
echo $this->render('_menu');
echo $this->render('_form', [
    'model' => $model,
    'methods' => $methods
]);

