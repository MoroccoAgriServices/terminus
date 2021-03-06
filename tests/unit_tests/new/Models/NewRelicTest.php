<?php

namespace Pantheon\Terminus\UnitTests\Models;

use Terminus\Collections\Workflows;
use Terminus\Models\NewRelic;
use Terminus\Models\Site;
use Terminus\Models\Workflow;

/**
 * Testing class for Terminus\Models\NewRelic
 */
class NewRelicTest extends ModelTestCase
{
    /**
     * @var Site
     */
    protected $site;
    /**
     * @var Workflow
     */
    protected $workflow;
    /**
     * @var Workflows
     */
    protected $workflows;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->workflow = $this->getMockBuilder(Workflow::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->workflows = $this->getMockBuilder(Workflows::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->site = $this->getMockBuilder(Site::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->site->workflows = $this->workflows;
        $this->model = new NewRelic(null, ['site' => $this->site,]);
        $this->model->setRequest($this->request);
    }

    /**
     * Tests NewRelic::disable()
     */
    public function testDisable()
    {
        $this->site->id = 'site_id';

        $this->workflows->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('disable_new_relic_for_site'),
                $this->equalTo(['site' => $this->site->id,])
            )
            ->willReturn($this->workflow);

        $workflow = $this->model->disable();
        $this->assertEquals($workflow, $this->workflow);
    }

    /**
     * Tests NewRelic::enable()
     */
    public function testEnable()
    {
        $this->site->id = 'site_id';

        $this->workflows->expects($this->once())
          ->method('create')
          ->with(
              $this->equalTo('enable_new_relic_for_site'),
              $this->equalTo(['site' => $this->site->id,])
          )
          ->willReturn($this->workflow);

        $workflow = $this->model->enable();
        $this->assertEquals($workflow, $this->workflow);
    }

    /**
     * Tests NewRelic::serialize()
     */
    public function testSerialize()
    {
        $attributes = (object)[
            'name' => 'site_name',
            'status' => 'new_relic_status',
            'subscription' => (object)['starts_on' => '1884/10/11',],
            'primary admin' => (object)['state' => 'new_relic_state',],
        ];
        $desired_data = [
            'name' => 'site_name',
            'status' => 'new_relic_status',
            'subscribed' => '1884-10-11 00:00:00',
            'state' => 'new_relic_state',
        ];

        $this->request->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo("sites/{$this->site->id}/new-relic"),
                $this->equalTo(['options' => ['method' => 'get',],])
            )
            ->willReturn(['data' => $attributes,]);

        $data = $this->model->fetch()->serialize();
        $this->assertEquals($data, $desired_data);
    }
}
