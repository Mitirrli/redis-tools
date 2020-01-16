<?php

namespace Mitirrli\Queue;

class Semaphore
{
    protected $id;

    protected $shmId;

    protected $signal;

    protected $permission = 0655;

    protected $config = [];

    public function __construct(array $config = [])
    {
        if ($config) {
            $this->config = array_merge($this->config, $config);
        }

        $this->init();
    }

    /**
     * 初始化
     */
    public function init()
    {
        if (isset($this->config['id'])) {
            $this->id = $this->config['id'];
        } else {
            $this->generateId();
        }

        $this->setSignal();

        if (isset($this->config['permission'])) {
            $this->permission = $this->config['permission'];
        }

        $this->checkArea();
    }

    public function checkArea()
    {
        $this->shmId = shm_attach($this->id, 1024, $this->permission);
    }

    /**
     * 通过信号量保证原子性
     */
    public function getIncreasing($lLen)
    {
        sem_acquire($this->signal);

        if (shm_has_var($this->shmId, 1)) {
            $count = shm_get_var($this->shmId, 1);
            if ($count < $lLen - 1) {
                $count++;
            }else{
                $count = 0;
            }

            shm_put_var($this->shmId, 1, $count);
        } else {
            $count = 0;
            shm_put_var($this->shmId, 1, $count);
        }

        sem_release($this->signal);
        $this->remove();
        return shm_get_var($this->shmId, 1);
    }

    /**
     *  设置信号量
     */
    public function setSignal()
    {
        $sem_id = ftok(__FILE__, 's');

        $this->signal = sem_get($sem_id); // 信号量
    }

    /**
     * remove
     */
    public function remove()
    {
        sem_remove($this->signal);
    }

    /**
     * getId
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * generateId
     */
    protected function generateId()
    {
        $id = ftok(__FILE__, 't');

        $this->id = $id;
    }
}
