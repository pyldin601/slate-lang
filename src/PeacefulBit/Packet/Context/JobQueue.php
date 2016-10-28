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

    /**
     * @return void
     */
    public function run()
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

    public function prepend(callable $job, array $args = [])
    {
        array_unshift($this->queue, [$job, $args]);
    }

    /**
     * @return mixed
     */
    public function getLastResult()
    {
        $this->run();
        return $this->result;
    }

    /**
     * @return int
     */
    public function size()
    {
        return sizeof($this->queue);
    }
}
