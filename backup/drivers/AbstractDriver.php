<?php
/**
 * AbstractDriver.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\backup\drivers;

use cookyii\Facade as F;
use yii\helpers\FileHelper;

/**
 * Class AbstractDriver
 * @package cookyii\backup\drivers
 */
abstract class AbstractDriver extends \yii\base\Object implements DriverInterface
{

    /**
     * @var \cookyii\backup\Controller
     */
    public $controller;

    /**
     * @var string
     */
    private $path;

    /**
     * @return string
     * @throws \yii\console\Exception
     */
    protected function prepareDump()
    {
        if (empty($this->path)) {
            $path = implode(DIRECTORY_SEPARATOR, [
                \Yii::getAlias($this->controller->backupPath, false),
                F::Formatter()->asDate(time(), 'yyyy-MM-dd'),
                F::Formatter()->asTime(time(), 'HH:mm:ss'),
            ]);

            if (!file_exists($path)) {
                FileHelper::createDirectory($path);
            }

            if (!file_exists($path) || !is_dir($path)) {
                throw new \yii\console\Exception('Backup path not found.');
            }

            if (!is_readable($path)) {
                throw new \yii\console\Exception('Backup path is not readable.');
            }

            if (!is_writable($path)) {
                throw new \yii\console\Exception('Backup path is not writable.');
            }

            $this->path = $path;
        }

        return $this->path;
    }
}
