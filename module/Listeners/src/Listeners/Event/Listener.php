<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/11/14
 * Time: 11:15 AM
 */

namespace Listeners\Event;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventInterface;

class Listener implements ListenerAggregateInterface{
    protected $listeners = array();

    public function attach(EventManagerInterface $events, $id = null, $event = null, $callback = null, $priority = 1)   //add event name to this;
    {
//        $sharedEventManager->attach('*', 'log', function($e){
//                $writer = new Db($e->getParam('dbAdapter'), 'logger', $e->getParam('mapping'));
//                $logger = new Logger();
//                $logger->addWriter($writer);
//                $logger->info(null,$e->getParam('extra'));
//        },100);
//        $this->listeners[] = $events->attach('event_name',array($this, 'doEvent'));
        if(isset($id) && isset($event) && isset($callback) && isset($priority)){
            $this->listeners[] = $events->attach($id, $event, $callback, $priority);
        }
    }

    public function detach(EventManagerInterface $events)
    {
        foreach($this->listeners as $index  =>  $listener){
            if($events->detach($listener)){
                unset($this->$listener[$index]);
            }
        }
    }

    public function doEvent(EventInterface $event)  //add param arg to this.
    {
        return $event->getParam('param');
    }
} 