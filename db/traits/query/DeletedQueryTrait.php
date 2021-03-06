<?php
/**
 * DeletedQueryTrait.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\db\traits\query;

/**
 * Trait DeletedQueryTrait
 * @package cookyii\db\traits\query
 */
trait DeletedQueryTrait
{

    /**
     * @return static
     */
    public function onlyDeleted()
    {
        $this->andWhere(['not', ['deleted_at' => null]]);

        return $this;
    }

    /**
     * @return static
     */
    public function onlyNotDeleted()
    {
        $this->andWhere(['deleted_at' => null]);

        return $this;
    }

    /**
     * @return static
     */
    public function withoutDeleted()
    {
        return $this->onlyNotDeleted();
    }

    /**
     * @return static
     */
    public function withoutNotDeleted()
    {
        return $this->onlyDeleted();
    }
}
