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
     * @return void
     */
    public function push(callable $job)
    {
        array_push($this->queue, $job);

        if (!$this->working) {
            $this->working = true;
            while (sizeof($this->queue) > 0) {
                $callable = array_shift($this->queue);
                $this->result = $callable();
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
