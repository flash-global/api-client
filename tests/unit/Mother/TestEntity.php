<?php
namespace Tests\Fei\ApiClient\Mother;

use Fei\Entity\AbstractEntity;

class TestEntity extends AbstractEntity
{
    protected $id;

    /**
     * Get Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
