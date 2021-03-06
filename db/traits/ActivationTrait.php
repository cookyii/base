<?php
/**
 * ActivationTrait.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\db\traits;

/**
 * Trait ActivationTrait
 * @package cookyii\db\traits
 *
 * @property bool $activated_at
 */
trait ActivationTrait
{

    /**
     * @return bool
     */
    public function isActivated()
    {
        $result = !empty($this->activated_at);

        if (array_key_exists('cookyii\db\traits\SoftDeleteTrait', class_uses($this))) {
            $result = $result && !$this->isDeleted();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isNotActivated()
    {
        return !$this->isActivated();
    }

    /**
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws \yii\base\InvalidConfigException
     */
    public function activate()
    {
        if (!$this->hasAttribute('activated_at') && !$this->hasProperty('activated_at')) {
            throw new \yii\base\InvalidConfigException(sprintf('`%s` has no attribute named `%s`.', get_class($this), 'activated_at'));
        }

        $this->activated_at = time();

        return $this->update();
    }

    /**
     * @return integer|boolean the number of rows affected, or false if validation fails
     * or [[beforeSave()]] stops the updating process.
     * @throws \yii\base\InvalidConfigException
     */
    public function deactivate()
    {
        if (!$this->hasAttribute('activated_at') && !$this->hasProperty('activated_at')) {
            throw new \yii\base\InvalidConfigException(sprintf('`%s` has no attribute named `%s`.', get_class($this), 'activated_at'));
        }

        $this->activated_at = null;

        return $this->update();
    }
}
