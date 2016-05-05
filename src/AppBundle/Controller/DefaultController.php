<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\YoutubeChannel;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/hello/{name}", name="hello")
     */
    public function helloAction(Request $request, $name)
    {
        // replace this example code with whatever you need
        return $this->render('default/hello.html.twig', [
                    'name' => $name
        ]);
    }

    /**
     * @Route("/myChannel", name="myChannel")
     */
    public function myChannelAction(Request $request)
    {
        // Authorize
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        // $user = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->getUser();
        
        // Grab channel and videos
        $channel = $user->getYoutubeChannel();
        $latestVideos = [];
        if ($channel) {
            $channelid = $channel->getChannelId();
            
            // Youtube API
            $client = $this->get('guzzle.client.api_youtube');
            $response = $client->get("/youtube/v3/search?channelId=$channelid&type=video&maxResults=10&part=snippet&key=AIzaSyD4ox3IABRzANj11HqIjgFZqmxJ2-x05a0");
            $json = $response->getBody();
            $decoded = json_decode($json, true);
            $latestVideos = $decoded['items'];
        }

        return $this->render('default/saveChannel.html.twig', [
                    'channel' => $channel,
                    'latestVideos' => $latestVideos,
        ]);
    }

    /**
     * @Route("/saveChannelSubmit", name="saveChannelSubmit")
     */
    public function saveChannelSubmitAction(Request $request)
    {
        // Authorize
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();

        // Input
        $channelname = $request->get("_channelname");

        // Youtube API
        $client = $this->get('guzzle.client.api_youtube');
        $response = $client->get("/youtube/v3/channels?forUsername=$channelname&part=id&key=AIzaSyD4ox3IABRzANj11HqIjgFZqmxJ2-x05a0");

        // Found youtube channel
        $found = false;
        if ($response->getStatusCode() == 200) {

            $json = $response->getBody();
            $decoded = json_decode($json, true);

            if ($decoded['pageInfo']['totalResults'] == 1) {
                $found = true;

                // Create new channel if user does not have one
                $y;
                if (!($y = $user->getYoutubeChannel())) {
                    $y = new YoutubeChannel();
                }
                $y->setUsername($channelname);
                //$y->setChannelId('UCuFFtHWoLl5fauMMD5Ww2jA');
                $y->setChannelId($decoded['items'][0]['id']);
                //$y->setChannelId('test');

                $user->setYoutubeChannel($y);

                $em = $this->getDoctrine()->getManager();
                $em->persist($y);
                $em->persist($user);
                $em->flush();
            }
        }

        if (!$found) {
            $session = $request->getSession();
            $session->getFlashBag()->add('channelNameError', $channelname);
        }

        return $this->redirectToRoute('myChannel');
    }

}
