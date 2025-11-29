<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Shuffle;
use App\Entity\User;
use App\Form\GameBuilderType;
use App\Form\GameEnterType;
use App\Form\GameInviteType;
use App\Form\GameStartType;
use App\Form\ShuffleChangeType;
use App\Form\ShuffleWishType;
use App\Service\MailerHandler;
use App\Service\UserHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class GameController extends AbstractController
{
    #[Route('/game/build', name: 'app_game_build')]
    public function build(Request $request, ManagerRegistry $registry): Response
    {
        $form = $this->createForm(GameBuilderType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $manager = $registry->getManager();
            $task = $form->getData();

            $userPassport = $this->getUser();
            $user = $registry->getRepository(User::class)
                ->findOneBy(['email' => $userPassport->getUserIdentifier()]);

            $identifier = hash('sha256', uniqid());

            $game = new Game();
            $game->setIdentifier($identifier);
            $game->setName($task->getName());
            // $game->setStart($task->getStart());
            // $game->setEnd($task->getEnd());
            $game->setOwner($user);
            $game->setShuffled(false);

            $manager->persist($game);

            $shuffle = new Shuffle();
            $shuffle->setGame($game);
            $shuffle->setUser($user);

            $manager->persist($shuffle);

            $manager->flush();

            return $this->redirectToRoute('app_game_identifier', ['identifier' => $identifier]);
        }

        return $this->render('game/preview.html.twig');
    }

    #[Route('/game/lobby/{identifier}', name: 'app_game_identifier')]
    public function gameIdentifier($identifier, ManagerRegistry $registry, Request $request, MailerInterface $mailer): Response
    {
        $mailerHandler = new MailerHandler($mailer);
        $manager = $registry->getManager();

        $options = [];

        $options['start_form'] = $this->createForm(GameStartType::class);
        $options['enter_form'] = $this->createForm(GameEnterType::class);
        $options['invite_form'] = $this->createForm(GameInviteType::class);
        $options['shuffle_change_form'] = $this->createForm(ShuffleChangeType::class);
        $options['shuffle_wish_form'] = $this->createForm(ShuffleWishType::class);


        $userHandler = new UserHandler($registry);
        $user = $userHandler->getUserByEmail($this->getUser()->getUserIdentifier());
        $game = $registry->getRepository(Game::class)->findOneBy(['identifier' => $identifier]);
        $options['isShuffled'] = $game->isShuffled();

        $shuffleRepository = $registry->getRepository(Shuffle::class);
        $shuffled = $shuffleRepository->findOneBy(['game' => $game, 'user' => $user]);


        $options['shuffle_wish_form']->handleRequest($request);

        if ($options['shuffle_wish_form']->isSubmitted()) {
            $wishForm = $options['shuffle_wish_form']->getData();
            $shuffled->setWish($wishForm['wish']);

            $manager->persist($shuffled);
            $manager->flush();

            return $this->redirectToRoute('app_game_identifier', ['identifier' => $identifier]);
        }

        $options['isOwner'] = false;
        $options['game_name'] = $game->getName();

        if (is_null($shuffled)) {
            if ($game->isShuffled()) {
                return $this->redirectToRoute('app_game_closed');
            }

            $options['enter_form']->handleRequest($request);

            if ($options['enter_form']->isSubmitted()) {

                $shuffle = new Shuffle();
                $shuffle->setGame($game);
                $shuffle->setUser($user);

                $manager->persist($shuffle);
                $manager->flush();

                return $this->redirectToRoute('app_game_identifier', ['identifier' => $identifier]);
            }

            return $this->render('game/enter.html.twig', $options);
        }

        $target = $shuffleRepository->findOneBy(['game' => $game, 'giver' => $user]);

        $options['user_name'] = $user->getUsername();
        $options['user_wish'] = empty($shuffled->getWish()) ? '' : $shuffled->getWish();

        $options['target_name'] = is_null($target) ? 'Ваша цель ещё неизвестена :(' : $target->getUser()->getUsername();
        $options['target_wish'] = is_null($target) ? 'Возможно ей надо подумать...' : $target->getWish();

        $options['users'] = $shuffleRepository->findBy(['game' => $game]);

        $options['start_form']->handleRequest($request);

        if ($options['start_form']->isSubmitted()) {
            $email = [];
            $givers = $options['users'];

            foreach ($options['users'] as $userIndex => $userEntity) {
                $giverKey = $userIndex;

                while ($giverKey == $userIndex) {
                    $giverKey = array_rand($givers, 1);
                }

                $giver = $givers[$giverKey];
                unset($givers[$giverKey]);

                $userEntity->setGiver($giver->getUser());
                $manager->persist($userEntity);
                $emails[] = $userEntity->getUser()->getEmail();
            }

            $game->setShuffled(true);
            $manager->persist($game);

            $manager->flush();

            foreach ($emails as $email) {
                if (!preg_match('#example#', $email)) {
                    $mailerHandler->sendStartGame($email, $identifier);
                }
            }

            return $this->redirectToRoute('app_game_identifier', ['identifier' => $identifier]);
        }

        if ($user->getId() == $game->getOwner()->getId()) {
            $options['invite_form']->handleRequest($request);

            if ($options['invite_form']->isSubmitted()) {
                $task = $options['invite_form']->getData();
                $mailerHandler->sendInviteToGame($task['email'], $identifier);
            }

            $options['isOwner'] = true;
        }

        return $this->render('game/view.html.twig', $options);
    }

    #[Route('/game/closed', name: 'app_game_closed')]
    public function closed(): Response
    {
        return $this->render('game/closed.html.twig');
    }

    #[Route('/game/join/{identifier}', name: 'app_game_closed')]
    public function join($identifier): Response
    {
        return $this->redirectToRoute('app_game_identifier', ['identifier' => $identifier]);
    }
}
