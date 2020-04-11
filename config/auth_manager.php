<?php

use yii\rbac\DbManager;

return [
    'class'           => DbManager::class,
    'itemTable'       => 'auth_item',
    'itemChildTable'  => 'auth_item_child',
    'assignmentTable' => 'auth_assignment',
    'ruleTable'       => 'auth_rule',
];