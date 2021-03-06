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
 * API Klasse zur Verwaltung von Passwort vergessen Anfragen
 */
class Passwordrequest
{
	use \DragonJsonServer\ServiceManagerTrait;

	/**
	 * Sendet eine E-Mail mit dem Hash zum Zurücksetzen des Passwortes
	 * @param string $emailaddress
	 * @param string $language
	 * @throws \DragonJsonServer\Exception
	 */
	public function requestPassword($emailaddress, $language)
	{
		$serviceManager = $this->getServiceManager();
		
		$serviceManager->get('\DragonJsonServerEmailaddress\Service\Passwordrequest')
            ->requestPassword($emailaddress, $language);
	}
	
	/**
	 * Setzt das Passwort des übergebenen Hashes
	 * @param string $passwordrequesthash
	 * @param string $newpassword
	 */
	public function resetPassword($passwordrequesthash, $newpassword)
	{
		$serviceManager = $this->getServiceManager();
		
		$serviceManager->get('\DragonJsonServerEmailaddress\Service\Passwordrequest')->resetPassword($passwordrequesthash, $newpassword);
	}
}
