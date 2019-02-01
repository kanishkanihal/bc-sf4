<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Bigcommerce\Api\Connection;
use App\Service\RedisConnector;

class BigcommerceController extends AbstractController
{
    public $redis;

    public function __construct()
    {
        $this->redis = new RedisConnector();
    }

    /**
     * @Route("/bigcommerce", name="bigcommerce")
     */
    public function index()
    {
        return $this->render('bigcommerce/index.html.twig', [
            'access_token' => $this->redis->get('access_token'),
            'user_email' => $this->redis->get('user_email'),
            'context' => $this->redis->get('context'),
        ]);
    }

    /**
     * @Route("/bigcommerce/callback", name="callback")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callback(Request $request)
    {
        $tokenUrl = "https://login.bigcommerce.com/oauth2/token";
        $connection = new Connection();
        /*$connection->useUrlencoded();*/
        $param = array(
            "client_id" => getenv('BC_CLIENT_ID'),
            "client_secret" => getenv('BC_CLIENT_SECRET'),
            "redirect_uri" => getenv('BC_CALLBACK_URL'),
            "grant_type" => "authorization_code",
            "code" => $request->get("code"),
            "scope" => $request->get("scope"),
            "context" => $request->get("context"),
        );
        $response = $connection->post($tokenUrl, $param);

        $token = $response->access_token;
        $context = $response->context;
        $user_email = $response->user->email;
        $this->redis->set('access_token', $token);
        $this->redis->set('user_email', $user_email);
        $this->redis->set('context', $context);

        return $this->render('bigcommerce/index.html.twig', [
            'controller_name' => 'BigcommerceController',
        ]);
    }

    /**
     * @Route("/bigcommerce/load", name="bigcommerce-load")
     */
    public function load()
    {
        return $this->render('bigcommerce/load.html.twig', [
            'controller_name' => 'BigcommerceController',
        ]);
    }

    /**
     * @Route("/bigcommerce/uninstall", name="bigcommerce-uninstall")
     */
    public function uninstall()
    {
        return $this->render('bigcommerce/index.html.twig', [
            'controller_name' => 'BigcommerceController',
        ]);
    }

    /**
     * @Route("/bigcommerce/remove", name="bigcommerce-remove")
     */
    public function remove()
    {
        return $this->render('bigcommerce/index.html.twig', [
            'controller_name' => 'BigcommerceController',
        ]);
    }

}
