<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(100);

        // More advanced way:
        $questions = QuestionFactory::createMany(20, function () {
            return [
                'questionTags' => QuestionTagFactory::new(function () {
                    return [
                        'tag' => TagFactory::random()
                    ];
                })->many(1, 5),
            ];
        });

        // simpler way to do:
        // TagFactory::createMany(100);
        // QuestionFactory::createMany(100);
        // And then:
//        $questions = QuestionTagFactory::createMany(100, function () {
//            return [
//                'tag' => TagFactory::random(),
//                'question' => QuestionFactory::random(),
//            ];
//        });


        QuestionFactory::new()
            ->unpublished()
            ->createMany(5);

        AnswerFactory::createMany(100, function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });

        AnswerFactory::new(function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        })->needsApproval()->many(20)->create();

// Manually saving ManyToMany relationship
//        $question = QuestionFactory::createOne()->object();
//
//        $tag1 = new Tag();
//        $tag1->setName('dinosaurs');
//        $tag2 = new Tag();
//        $tag2->setName('monster trucks');
//
////        $question->addTag($tag1);
////        $question->addTag($tag2);
//        $tag1->addQuestion($question);
//        $tag2->addQuestion($question);
//
//        $manager->persist($tag1);
//        $manager->persist($tag2);

        $manager->flush();
    }
}
