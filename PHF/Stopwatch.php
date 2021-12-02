<?php

/**
 * 计时器
 */

namespace PHF;

class Stopwatch
{
    /**
     * 开始计时
     */
    public function start()
    {
        list($usec, $sec) = explode(" ", microtime());

        $this->start = (float) $sec + (float) $usec;
    }

    /**
     * 停止计时
     */
    public function stop()
    {
        list($usec, $sec) = explode(" ", microtime());

        $this->end = (float) $sec + (float) $usec;
    }

    /**
     * 返回时长
     */
    public function duration()
    {
        $duration = (int) (($this->end - $this->start) * 1000);

        return $duration;
    }

    private $start = 0;
    private $end   = 0;
}
