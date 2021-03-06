<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerEmailaddress
 */

namespace DragonJsonServerEmailaddress\Api;

/**
 * API Klasse zur Verwaltung von E-Mail Adressverknüpfungen
 */
class Emailaddress
{
	use \DragonJsonServer\ServiceManagerTrait;

	/**
	 * Validiert die übergebene E-Mail Adresse
	 * @param string $emailaddress
     * @throws \DragonJsonServer\Exception
	 */
	public function validateEmailaddress($emailaddress)
	{
		$serviceManager = $this->getServiceManager();
		
		$serviceEmailaddress = $serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress');
		$serviceEmailaddress->validateEmailaddress($emailaddress);
		if (null !== $serviceEmailaddress->getEmailaddressByEmailaddress($emailaddress, false)) {
			throw new \DragonJsonServer\Exception('emailaddress not unique', ['emailaddress' => $emailaddress]);
		}
	}
	
	/**
	 * Erstellt eine neue E-Mail Adressverknüpfung für den Account
	 * @param string $emailaddress
	 * @param string $password
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function createEmailaddress($emailaddress, $password)
	{
		$this->validateEmailaddress($emailaddress);
		$serviceManager = $this->getServiceManager();

        $serviceSession = $serviceManager->get('\DragonJsonServerAccount\Service\Session');
		$session = $serviceSession->getSession();
		$emailaddress = $serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress')
            ->createEmailaddress($session->getAccountId(), $emailaddress, $password, $session->getData()['account']['language']);
		$data = $session->getData();
		$data['emailaddress'] = $emailaddress->toArray();
        $serviceSession->changeData($session, $data);
		return $emailaddress->toArray();
	}
	
    /**
	 * Entfernt die E-Mail Adressverknüpfung für den aktuellen Account
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function removeEmailaddress()
	{
		$serviceManager = $this->getServiceManager();

        $serviceSession = $serviceManager->get('\DragonJsonServerAccount\Service\Session');
		$session = $serviceSession->getSession();
		$serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress')->removeEmailaddress($session->getAccountId());
		$data = $session->getData();
		unset($data['emailaddress']);
        $serviceSession->changeData($session, $data);
	}
	
    /**
	 * Meldet den Account mit der übergebenen E-Mail Adressverknüpfung an
	 * @param string $emailaddress
	 * @param string $password
	 * @return array
	 */
	public function loginEmailaddress($emailaddress, $password)
	{
		$serviceManager = $this->getServiceManager();

		$emailaddress = $serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress')
			->getEmailaddressByEmailaddressAndPassword($emailaddress, $password);
		$serviceSession = $serviceManager->get('\DragonJsonServerAccount\Service\Session');
		$session = $serviceSession->createSession($emailaddress->getAccountId(), ['emailaddress' => $emailaddress->toArray()]);
		$serviceSession->setSession($session);
		return $session->toArray();
	}
	
	/**
	 * Gibt die E-Mail Adressverknüpfung des aktuellen Accounts zurück
	 * @return array|null
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function getEmailaddress()
	{
		$serviceManager = $this->getServiceManager();
		
		$session = $serviceManager->get('\DragonJsonServerAccount\Service\Session')->getSession();
		$emailaddress = $serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress')->getEmailaddressByAccountId($session->getAccountId(), false);
		if (null !== $emailaddress) {
			return $emailaddress->toArray();
		}
		return;
	}
	
	/**
	 * Ändert die E-Mail Adresse der E-Mail Adressverknüpfung
	 * @param string $newemailaddress
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function changeEmailaddress($newemailaddress)
	{
		$this->validateEmailaddress($newemailaddress);
		$serviceManager = $this->getServiceManager();

        $serviceSession = $serviceManager->get('\DragonJsonServerAccount\Service\Session');
		$session = $serviceSession->getSession();
		$serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress')->changeEmailaddress(
			$session->getAccountId(), 
			$newemailaddress,
            $session->getData()['account']['language']
		);
		$data = $session->getData();
		if (isset($data['emailaddress'])) {
			$data['emailaddress']['emailaddress'] = $newemailaddress;
            $serviceSession->changeData($session, $data);
		}
	}
	
	/**
	 * Ändert das Passwort der E-Mail Adressverknüpfung
	 * @param string $newpassword
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function changePassword($newpassword)
	{
		$serviceManager = $this->getServiceManager();

        $serviceSession = $serviceManager->get('\DragonJsonServerAccount\Service\Session');
		$session = $serviceSession->getSession();
		$serviceEmailaddress = $serviceManager->get('\DragonJsonServerEmailaddress\Service\Emailaddress'); 
		$emailaddress = $serviceEmailaddress->getEmailaddressByAccountId($session->getAccountId());
		$serviceEmailaddress->changePassword($emailaddress, $newpassword);
	}
}
