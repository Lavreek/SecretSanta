<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Shuffle;
use App\Entity\User;
use App\Form\GameCreateType;
use App\Form\GameSearchType;
use App\Form\ShuffleChangeType;
use App\Form\ShuffleWishType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class FormController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(GameCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('game/create.stream.html.twig');
            }
        }

        return $this->render('game/preview.html.twig');
    }

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ManagerRegistry $registry): Response
    {
        $userPassport = $this->getUser();

        $form = $this->createForm(GameSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !is_null($userPassport)) {
            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                $games = [];

                $user = $registry->getRepository(User::class)
                    ->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

                $shuffles = $registry->getRepository(Shuffle::class)
                    ->findBy(['user' => $user->getId()]);

                foreach ($shuffles as $shuffle) {
                    $games[] = $registry->getRepository(Game::class)
                        ->findOneBy(['id' => $shuffle->getGame()]);
                }

                return $this->render('game/search.stream.html.twig', [
                    'games' => $games
                ]);
            }
        }

        return $this->render('game/preview.html.twig');
    }

    #[Route('/shuffle/wish/change', name: 'app_shuffle_wish_change')]
    public function shuffleWishChange(Request $request): ?Response
    {
        $wishChangeForm = $this->createForm(ShuffleChangeType::class);
        $wishChangeForm->handleRequest($request);

        if ($wishChangeForm->isSubmitted()) {
            $options['shuffle_wish_form'] = $this->createForm(ShuffleWishType::class);

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('game/shuffle_wish.stream.html.twig', $options);
            }
        }

        return null;
    }
}
