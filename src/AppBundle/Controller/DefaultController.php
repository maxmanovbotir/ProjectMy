<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Regestr;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/registr/", name="test_number")
     */
    public function registrAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Regestr');
        $user = $repository->findAll();

        return $this->render('default/registr.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/create/", name="test_create")
     */
    public function createAction()
    {
        $user = new Regestr();
        $user->setUsername('kljhkl');
        $user->setInitial('SALOM');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $em->flush();

        return new Response('Saved new product with id ' . $user->getId());
    }

    /**
     * @Route("/new/", name="test_new")
     */
    public function newAction(Request $request)
    {
        $user = new Regestr();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('initial', TextType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('role', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('created_at', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('save', SubmitType::class, array('label' => 'Create'))
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('test_number');
        }

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/update/{id}", name="test_update")
     */

    public function updateAction($id, Request $request)
    {
        if (!$id) {
            throw $this->createNotFoundException('No se encuentra la tarea con id = ' . $id);
        }

        $em = $this->getDoctrine()->getEntityManager();
        $task = $em->getRepository('AppBundle:Regestr')->find($id);
        if (!$task) {
            throw $this->createNotFoundException('No se encuentra la tarea con id = ' . $id);
        }

        $form = $this->createFormBuilder($task)
            ->add('username', TextType::class, array(
                'required' => true,
                'constraints' => array(new NotBlank())
            ))
            ->add('initial', TextType::class)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('role', TextType::class)
            ->add('email', TextType::class, array(
                'required' => true,
                'constraints' => array(new Email())
            ))
            ->add('phone', IntegerType::class, array(
                'attr' => array(
                    'min' => 1,
                    'max' => 10
                )
            ))
            ->add('created_at', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('save', SubmitType::class, array('label' => 'Create'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em->flush();
                return $this->redirectToRoute('test_number');
            }
        }

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
            'id' => $task->getId(),
        ));
    }


    /**
     * @Route("/delete/{id}", name="test_delete")
     */

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $guest = $em->getRepository('AppBundle:Regestr')->find($id);

        if (!$guest) {
            throw $this->createNotFoundException('No guest found for id ' . $id);
        }

        $em->remove($guest);
        $em->flush();

        return $this->redirectToRoute('test_number');
    }
}
