<?php

namespace PeacefulBit\Packet\Context;

class JobQueue
{
    /**
     * @var array
     */
    private $queue = [];

    /**
     * @var mixed
     */
    private $result = null;

    private function proceed()
    {
        while (sizeof($this->queue) > 0) {
            list ($job, $args) = array_shift($this->queue);
            $this->result = $job(...$args);
        }
    }

    /**
     * @param callable $job
     * @param array $args
     * @return void
     */
    public function push(callable $job, array $args = [])
    {
        array_push($this->queue, [$job, $args]);
    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        $this->proceed();

        return $this->result;
    }
}
