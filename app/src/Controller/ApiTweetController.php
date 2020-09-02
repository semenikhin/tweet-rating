<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\TwitterService;

/**
 * Class ApiTweetController
 * @package App\Controller
 */
final class ApiTweetController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    /** @var TwitterService */
    private $twitterService;

    /**
     * ApiPostController constructor.
     * @param SerializerInterface $serializer
     * @param PostService $postService
     * @param TwitterService $twitterService
     */
    public function __construct(
        SerializerInterface $serializer,
        TwitterService $twitterService
    ) {
        $this->serializer = $serializer;
        $this->twitterService = $twitterService;
    }

    /**
     * @Rest\Get("/api/tweet", name="getTweet")
     * @param Request $request
     * @return JsonResponse
     */
    public function getTweet(Request $request): JsonResponse
    {
        $data = $this->twitterService->getTweets($request->get('tweetID'), $request->get('sortBy'));

        return new JsonResponse($data);
    }
}
