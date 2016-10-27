<?php

namespace tests;

use PeacefulBit\Packet\Context\JobQueue;
use PHPUnit\Framework\TestCase;

class JobQueueTest extends TestCase
{
    public function testOneJob()
    {
        $jobQueue = new JobQueue();
        $this->assertNull($jobQueue->getLastResult());

        $job = function () {
            return 100;
        };

        $jobQueue->push($job);

        $this->assertEquals(100, $jobQueue->getLastResult());
    }

    public function testNestedJobAsTailRecursion()
    {
        $jobQueue = new JobQueue();

        $job = function ($n) use (&$jobQueue, &$job) {
            if ($n > 1) {
                $jobQueue->push($job, [$n - 1]);
            }
            return $n;
        };

        $jobQueue->push($job, [100]);

        $this->assertEquals(1, $jobQueue->getLastResult());
    }
}
