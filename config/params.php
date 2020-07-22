<?php
Yii::setAlias('@vendor', dirname(__FILE__, 2) . '/vendor');
Yii::setAlias('@runtime', dirname(__FILE__, 2) . '/runtime');
Yii::setAlias('@uploads', dirname(__FILE__, 2) . '/web/uploads');

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
];
