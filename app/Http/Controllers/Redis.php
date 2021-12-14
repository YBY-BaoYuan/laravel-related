<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class Redis extends Controller
{
    public function redisDelayQueue()
    {
        $queueName = 'delay:queue:nts';

        Log::info('111');
        die;
        while (true)
        {
            try
            {
                $queueData = RedisHelper::zRangeByScore($queueName, 0, time(), ['limit' => [0, 1]]);
                if (!$queueData)
                {
                    //cronOutlog("INFO: no data");
                    usleep(100000);
                }
                $task = $queueData['0'];
                if (RedisHelper::zRem($queueName, $task))
                {
                    $data = json_decode($task, true);
                    //cronOutlog("INFO:" . $data['task_name'] . " 运行时间:" . formatLongDate().' 内容:'.json_encode($data['body']));
                    //todo 执行逻辑
                }
                usleep(100000);
            } catch (\Exception $ex)
            {
                //cronOutlog("INFO:异常操作，再次放回队列");
                RedisHelper::zAdd($queueName, time() + 5 * 60, json_encode($task, JSON_UNESCAPED_UNICODE));
            }
        }
        //推送数据加入延迟队列示例代码:
        $queueName = 'delay:queue:nts';
        $data      = [
            'task_name' => time(),
            'body'      => [
                'id'   => time(),
                'name' => NeoString::getRandString(10)
            ],
        ];
        RedisHelper::zAdd($queueName, time() + 30, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
