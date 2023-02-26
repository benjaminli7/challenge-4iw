<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user1 = $manager->getRepository(User::class)->findOneBy(['email' => '0client@user.fr']);
        $comments = [
            "Le bar est l'endroit idéal pour les amateurs de sports. Les télévisions diffusent des matchs en direct et la bière est fraîche. C'est un excellent endroit pour regarder un match entre amis.",
            "J'ai été agréablement surpris par le bar Z. L'atmosphère y est paisible et relaxante, et le choix de bières est excellent. Je le recommande vivement pour une soirée détendue.",
            "Le bar est un endroit idéal pour se détendre après une longue journée de travail. Les serveurs sont très sympathiques et le choix de bières est excellent. Je le recommande vivement.",
            "Le bar est un endroit parfait pour prendre un verre entre amis après le travail. Les cocktails sont savoureux et les serveurs sont très sympathiques.",
            "Le bar B est un endroit branché avec une décoration unique. Les cocktails sont créatifs et délicieux. C'est un endroit idéal pour une soirée chic entre amis.",
            "Si vous cherchez un bar animé avec une ambiance électrique, ne cherchez pas plus loin que le bar Y. La musique est forte, les gens sont festifs et l'alcool coule à flots."
        ];

        foreach($comments as $comment) {
            $review = (new Review())
                ->setUser($user1)
                ->setApproved(true)
                ->setNote(rand(1,5))
                ->setComment($comment)
            ;
    
            $manager->persist($review);
        }

        $manager->flush();
    }
}
