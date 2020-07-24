<?php

namespace app\modules\generator;

use Exception;
use vladayson\zipper\Zipper;
use Yii;
use yii\base\Exception as BaseException;

/**
 * Class Generator
 * @package app\modules\generator
 *
 * @usage
 * $generator = new Generator();
 * $combo = $generator->getCombination();
 * $data = $generator->encryptData();
 * $password = $generator->decryptData($data['hash']);
 * var_export([$data, $password, $combo]);
 */
class Generator
{
    private const SALT = '05Mav6GAFXMp_a-WVqVbdJ5LW1lXwG_Q8X4xHQCR';

    /** @var array  */
    private $combination = [];

    /**
     * Generator constructor.
     * @param int $combinationLength
     * @throws Exception
     */
    public function __construct(int $combinationLength = 3)
    {
        for ($i = 0; $i < $combinationLength; $i++) {
            $this->combination[] = random_int(0, 9);
        }
    }

    /**
     * @return array
     */
    public function getCombination(): array
    {
        return $this->combination;
    }

    /**
     * @return string[]
     * @throws BaseException
     */
    public function encryptData()
    {
        $password = Yii::$app->security->generateRandomString(random_int(8, 16));
        $hash = base64_encode(Yii::$app->security->encryptByKey($password, self::SALT));

        $fileName = 'combination_'.time();
        $file = Yii::getAlias('@runtime') . "/{$fileName}.txt";
        file_put_contents($file, implode('', $this->combination));
        $archiveName = Yii::getAlias('@uploads') . "/{$fileName}.zip";
        Zipper::create([$file], $archiveName, $password);
        unset($file);

        return [
            'archive' => basename($archiveName),
            'hash' => $hash
        ];
    }

    /**
     * @param string $hash
     * @return string
     */
    public function decryptData(string $hash)
    {
        return Yii::$app->security->decryptByKey(base64_decode($hash), self::SALT);
    }
}