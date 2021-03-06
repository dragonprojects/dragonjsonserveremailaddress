<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerEmailaddress
 */

namespace DragonJsonServerEmailaddress\Service;

/**
 * Serviceklasse zur Verwaltung einer E-Mail Adressvalidierung
 */
class Validationrequest
{
    use \DragonJsonServer\ServiceManagerTrait;
	use \DragonJsonServer\EventManagerTrait;
	use \DragonJsonServerDoctrine\EntityManagerTrait;
	
	/**
	 * Erstellt eine Anfrage für eine E-Mail Adressvalidierung
	 * @param \DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress
     * @param string $language
	 * @return Validationrequest
	 */
	public function createValidationrequest(\DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress, $language)
	{
		$entityManager = $this->getEntityManager();

		$emailaddress_id = $emailaddress->getEmailaddressId();
		$validationrequest = $this->getServiceManager()->get('\DragonJsonServerEmailaddress\Service\Validationrequest')
			->getValidationrequestByEmailaddressId($emailaddress_id, false);
		if (null === $validationrequest) {
			$validationrequest = (new \DragonJsonServerEmailaddress\Entity\Validationrequest())
				->setEmailaddressId($emailaddress_id)
				->setValidationrequesthash(md5($emailaddress_id . microtime(true)));
			$entityManager->persist($validationrequest);
			$entityManager->flush();
		}
		$this->sendValidationrequest($emailaddress, $validationrequest, $language);
		return $this;
	}
	
	/**
	 * Sendet die E-Mail Adressvalidierung
	 * @param \DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress
	 * @param \DragonJsonServerEmailaddress\Entity\Validationrequest $validationrequest
	 * @param string $language
	 * @return Validationrequest
	 */
	public function sendValidationrequest(\DragonJsonServerEmailaddress\Entity\Emailaddress $emailaddress,
										  \DragonJsonServerEmailaddress\Entity\Validationrequest $validationrequest,
                                          $language)
	{
        $serviceManager = $this->getServiceManager();

        $serviceTranslate = $serviceManager->get('translator');
		$message = (new \Zend\Mail\Message())
			->addTo($emailaddress->getEmailaddress())
			->addFrom($serviceManager->get('Config')['dragonjsonserveremailaddress']['from'])
			->setSubject($serviceTranslate->translate('validationrequest.subject', 'dragonjsonserveremailaddress', $language))
			->setBody(str_replace(
                '%validationrequesthash%',
                $validationrequest->getValidationrequesthash(),
                $serviceTranslate->translate('validationrequest.body', 'dragonjsonserveremailaddress', $language)
			));
		(new \Zend\Mail\Transport\Sendmail())->send($message);
		return $this;
	}
	
	/**
	 * Gibt die E-Mail Adressvalidierung zur übergebenen EmailaddressID zurück
	 * @param integer $emailaddress_id
	 * @param boolean $throwException
	 * @return \DragonJsonServerEmailaddress\Entity\Validationrequest|null
     * @throws \DragonJsonServer\Exception
	 */
	public function getValidationrequestByEmailaddressId($emailaddress_id, $throwException = true)
	{
		$entityManager = $this->getEntityManager();

		$conditions = ['emailaddress_id' => $emailaddress_id];
		$validationrequest = $entityManager
			->getRepository('\DragonJsonServerEmailaddress\Entity\Validationrequest')
			->findOneBy($conditions);
		if (null === $validationrequest && $throwException) {
			throw new \DragonJsonServer\Exception('invalid emailaddress_id', $conditions);
		}
		return $validationrequest;
	}
	
	/**
	 * Validiert die E-Mail Adresse der E-Mail Adressverknüpfung
	 * @param string $validationrequesthash
	 * @return Validationrequest
	 * @throws \DragonJsonServer\Exception
	 */
	public function validateEmailaddress($validationrequesthash)
	{
		$entityManager = $this->getEntityManager();

		$conditions = ['validationrequesthash' => $validationrequesthash];
		$validationrequest = $entityManager
			->getRepository('\DragonJsonServerEmailaddress\Entity\Validationrequest')
			->findOneBy($conditions);
		if (null === $validationrequest) {
			throw new \DragonJsonServer\Exception('invalid validationrequesthash', $conditions);
		}
		$this->getServiceManager()->get('\DragonJsonServerDoctrine\Service\Doctrine')->transactional(function ($entityManager) use ($validationrequest) {
			$this->getEventManager()->trigger(
				(new \DragonJsonServerEmailaddress\Event\ValidateEmailaddress())
					->setTarget($this)
					->setValidationrequest($validationrequest)
			);
			$entityManager->remove($validationrequest);
			$entityManager->flush();
		});
		return $this;
	}
}
