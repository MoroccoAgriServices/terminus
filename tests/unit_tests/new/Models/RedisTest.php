<?php

namespace Pantheon\Terminus\UnitTests\Models;

use Terminus\Collections\Workflows;
use Terminus\Models\Environment;
use Terminus\Models\Redis;
use Terminus\Models\Site;
use Terminus\Models\Workflow;

/**
 * Testing class for Terminus\Models\Redis
 */
class RedisTest extends ModelTestCase
{
    /**
     * @var Site
     */
    protected $site;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->site = $this->getMockBuilder(Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new Redis(null, ['site' => $this->site,]);
        $this->model->setRequest($this->request);
    }

    /**
     * Tests Redis::clear()
     */
    public function testClear()
    {
        $environment = $this->getMockBuilder(Environment::class)
          ->disableOriginalConstructor()
          ->getMock();
        $workflow = $this->getMockBuilder(Workflow::class)
          ->disableOriginalConstructor()
          ->getMock();
        $workflows = $this->getMockBuilder(Workflows::class)
          ->disableOriginalConstructor()
          ->getMock();
        $environment->workflows = $workflows;

        $this->site->id = 'site_id';
        $environment->id = 'env_id';

        $workflows->expects($this->once())
            ->method('create')
            ->with('clear_redis_cache')
            ->willReturn($workflow);

        $return_workflow = $this->model->clear($environment);
        $this->assertEquals($workflow, $return_workflow);
    }

    /**
     * Tests Redis::disable()
     */
    public function testDisable()
    {
        $this->site->id = 'site_id';

        $this->request->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo("sites/{$this->site->id}/settings"),
                $this->equalTo(['method' => 'put', 'form_params' => ['allow_cacheserver' => false,],])
            );
        $out = $this->model->disable();
        $this->assertNull($out);
    }

    /**
     * Tests Redis::enable()
     */
    public function testEnable()
    {
        $this->site->id = 'site_id';

        $this->request->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo("sites/{$this->site->id}/settings"),
                $this->equalTo(['method' => 'put', 'form_params' => ['allow_cacheserver' => true,],])
            );
        $out = $this->model->enable();
        $this->assertNull($out);
    }
}
