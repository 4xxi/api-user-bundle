<?php

namespace Fourxxi\ApiUserBundle\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Fourxxi\ApiUserBundle\Controller\RegistrationController;
use Fourxxi\ApiUserBundle\Event\Controller\RegistrationFormValidationFailedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;

final class RegistrationControllerTest extends TestCase
{
    private const EXPECTED_USER_CLASS = 'user_class';
    /** @var FormFactoryInterface|MockObject */
    private $formFactory;
    /** @var AbstractType|MockObject */
    private $registrationFormType;
    /** @var EventDispatcherInterface|MockObject */
    private $eventDispatcher;
    /** @var EntityManagerInterface|MockObject */
    private $entityManager;

    /** @var RegistrationController */
    private $registrationController;

    protected function setUp(): void
    {
        $this->registrationFormType = $this->createMock(AbstractType::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->registrationController = $this->createMock(AbstractType::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->registrationController = new RegistrationController(
            $this->registrationFormType,
            self::EXPECTED_USER_CLASS,
            $this->formFactory,
            $this->eventDispatcher,
            $this->entityManager
        );
    }

    /**
     * @test
     */
    public function expectsJsonBody(): void
    {
        $form = $this->getForm();
        $form->expects($this->atLeastOnce())
            ->method('submit')
            ->with($this->equalTo(['expected' => 'test']));


        $this->registrationController->__invoke($this->getRequest());
    }
    
    /**
     * @test
     */
    public function getsFailedResponseFromEventDispatcher(): void
    {
        $form = $this->getForm();
        $form->method('isValid')
            ->willReturn(false);
        
        $expectedResponse = new Response('test');

        $this->eventDispatcher->method('dispatch')
            ->willReturnCallback(function (RegistrationFormValidationFailedEvent $event) use ($expectedResponse) {
                $event->setResponse($expectedResponse);
            });

        $actualResponse = $this->registrationController->__invoke($this->getRequest());
        
        $this->assertSame($expectedResponse, $actualResponse);
    }

    /**
     * @test
     */
    public function doesNotPersistUserIfValidationIsFailed(): void
    {
        $form = $this->getForm();
        $form->method('isValid')
            ->willReturn(false);

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->registrationController->__invoke($this->getRequest());
    }

    /**
     * @test
     */
    public function persistsTheNewUser(): void
    {
        $expectedUser = new User('test', 'test');

        $form = $this->getForm();
        $form->method('isValid')
            ->willReturn(true);
        $form->method('getData')->willReturn($expectedUser);

        $this->entityManager->expects($this->atLeastOnce())->method('persist')->with($expectedUser);
        $this->entityManager->expects($this->atLeastOnce())->method('flush');

        $this->registrationController->__invoke($this->getRequest());

   }

    private function getRequest(): Request
    {
        return new Request([], [], [], [], [], [], '{"expected": "test"}');
    }

    /**
     * @return MockObject|FormInterface
     */
    private function getForm()
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('getErrors')->willReturn([]);
        $this->formFactory->method('create')->willReturn($form);

        return $form;
    }
}
