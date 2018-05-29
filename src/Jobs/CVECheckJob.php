<?php
/**
 * Composer security checker job. Runs the task which does the check as a queuedjob.
 *
 * @author Peter Thaleikis
 * @license BSD-3-Clause
 */
class CVECheckJob extends AbstractQueuedJob implements QueuedJob
{
    private static $dependencies = [
        'CVECheckTask' => '%$' . CVECheckTask::class,
    ];

    /**
     * @var CVECheckTask
     */
    protected $checkTask;

    /**
     * @return CVECheckTask
     */
    public function getCheckTask()
    {
        return $this->checkTask;
    }

    /**
     * @param CVECheckTask $checkTask
     * @return CVECheckJob
     */
    public function setCheckTask(CVECheckTask $checkTask)
    {
        $this->checkTask = $checkTask;
        return $this;
    }

    public function getTitle()
    {
        return _t(
            __CLASS__ . '.Title',
            'Check if any composer managed modules have known security vulnerabilities.'
        );
    }

    public function getJobType()
    {
        $this->totalSteps = 1;

        return QueuedJob::QUEUED;
    }

    public function process()
    {
        // run the task
        $task = $this->getCheckTask();
        $task->run(null);

        // mark job as completed
        $this->isComplete = true;
    }
}