<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerEmailaddress
 */

namespace DragonJsonServerEmailaddress\Event;

/**
 * Eventklasse für die Trennung eines Accounts von einer E-Mail Adresse
 */
class RemoveEmailaddress extends \Zend\EventManager\Event
{
	/**
	 * @var string
	 */
	protected $name = 'RemoveEmailaddress';

    /**
     * Setzt die E-Mail Adresse die mit dem Account getrennt wurde
     * @param \DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress
     * @return RemoveEmailaddress
     */
    public function setEmailaddress(\DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress)
    {
        $this->setParam('emailaddress', $emailaddress);
        return $this;
    }

    /**
     * Gibt die E-Mail Adresse die mit dem Account getrennt wurde zurück
     * @return \DragonJsonServerEmailaddress\Entity\Emailaddress
     */
    public function getEmailaddress()
    {
        return $this->getParam('emailaddress');
    }
}
