<?php

namespace PeacefulBit\Packet\Context;

class JobQueue
{
    /**
     * @var array
     */
    private $queue = [];

    /**
     * @var bool
     */
    private $working = false;

    /**
     * @var mixed
     */
    private $result = null;

    /**
     * @param callable $job
     * @param array $args
     * @return void
     */
    public function push(callable $job, array $args = [])
    {
        array_push($this->queue, [$job, $args]);

        if (!$this->working) {
            $this->working = true;
            while (sizeof($this->queue) > 0) {
                list ($callable, $args) = array_shift($this->queue);
                $this->result = $callable(...$args);
            }
            $this->working = false;
        }
    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        return $this->result;
    }
}
