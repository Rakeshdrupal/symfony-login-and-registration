<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationType;



/**
 * Description of SecurityController
 *
 * @author rakeshkumar
 */
class SecurityController extends Controller {

    public function registrationAction() {
        $registration = new User();

        $form = $this->createForm(new RegistrationType(), $registration, ['action' => '', 'method' => 'POST']);

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    public function createAction(Request $req)
    {
        $em   = $this->getDoctrine()->getManager();
        $form = $this->createForm(new RegistrationType(), new User());
        $form->handleRequest($req);

        $user= new User();
        $user= $form->getData();

        $user->setCreated(new \DateTime());
        $user->setRoles('ROLE_USER');
        $user->setGravatar('http://www.gravatar.com/avatar/'.md5(trim($req->get('email'))));
        $user->setActive(true);

        $pwd=$user->getPassword();
        $encoder=$this->container->get('security.password_encoder');
        $pwd= $encoder->encodePassword($user, $pwd);
        $user->setPassword($pwd);
        
        $em->persist($user);
        $em->flush();

        $url = $this->generateUrl('login');
        return $this->redirect($url);
    }



    
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render(
                        'security/login.html.twig', array(
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'error' => $error,
                        )
        );
    }

    public function doinviteAction(Request $req) {

        $email = $req->get('email');
        $userid = $req->get('user');

        $hash = $this->setInvite($userid, $email);
        $this->sendMail($email, $hash);

        $url = $this->generateUrl('invite');
        return $this->redirect($url);
    }

    private function setInvite($userid, $email) {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository('AppBundle:User');
        $user = $user_repo->find($userid); //The user who initiates the invitation

        $invite = new Invite();
        $invite->setInvited($email);
        $invite->setWhoinvite($user);

        $now = new \DateTime();
        $int = new \DateInterval('P1D');
        $now->add($int);
        $invite->setExpires($now); //Set invitation expirary 

        $random = rand(10000, 99999);

        $invite->setHash($random); //A random number is used but stricter method to create a verification code can be used. 

        $em->persist($invite);
        $em->flush();

        return $random;
    }

    private function sendMail($email, $hash) {
        $mailer = $this->get('mailer');

        $message = $mailer->createMessage()
                ->setSubject('Someone invites you to join thisdomain.com')
                ->setFrom('root@thisdomain.com')
                ->setTo($email)
                ->setBody(
                $this->renderView('AppBundle:Default:email.html.twig', ['email' => $email, 'hash' => $hash]), 'text/html'
        );

        $mailer->send($message);
    }

    //put your code here
}
