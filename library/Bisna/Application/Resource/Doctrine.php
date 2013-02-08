<?php

namespace Bisna\Application\Resource;

use Bisna\Doctrine\Container as DoctrineContainer;

/**
 * Zend Application Resource Doctrine class
 *
 * @author Guilherme Blanco <guilhermeblanco@hotmail.com>
 */
class Doctrine extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var \Bisna\Doctrine\Container
     */
    protected $container;
    
    /**
     * Initializes Doctrine Context.
     *
     * @return \Bisna\Doctrine\Container
     */
    public function init()
    {
        if (\Zend_Registry::isRegistered("doctrine_schema"))
                $this->setOptions(array("orm" => array("entityManagers" => array("default" =>
                array("metadataDrivers" => array("drivers" => array(0 =>
                array("mappingDirs" => \Zend_Registry::get("doctrine_schema")))
            ))))));

        $config = $this->getOptions();

        // Starting Doctrine container
        $this->container = new DoctrineContainer($config);

        $em = $this->container->getEntityManager();
        $em->getEventManager()->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
        $em->getEventManager()->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());

        \Zend_Registry::set('doctrine', $this->container);
        \Zend_Registry::set('em', $em);

        return $this->container;
    }
    
    /**
     * Retrieve the Doctrine Container.
     *
     * @return \Bisna\Doctrine\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}