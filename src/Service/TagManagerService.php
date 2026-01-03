<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Tag;
use App\Entity\Trait\EntityValidatorTrait;
use App\Repository\TagRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TagManagerService
{
    use EntityValidatorTrait;

    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly ValidatorInterface $validator,
    ) {}

    public function getOrCreateQuizTags(array $tagsData, Quiz $quiz): array
    {
        $tags = [];

        foreach ($tagsData as $tagData) {
            $tagName = self::formatTagName($tagData['name']);
            $tagDisplayName = self::formatTagDisplayName($tagName);

            $tag = $this->tagRepository->findOneBy(['name' => $tagName]);

            if (!$tag) {
                $tag = (new Tag())
                    ->setName($tagName)
                    ->setDisplayName($tagDisplayName);

                self::validate($tag, $this->validator);
            }

            $tag->addQuiz($quiz);
            $tags[] = $tag;
        }

        return $tags;
    }

    private static function formatTagName(string $tagName): string
    {
        $name = trim(strtolower($tagName));
        return str_replace(' ', '-', $name);
    }

    private static function formatTagDisplayName(string $tagName): string
    {
        $name = trim($tagName);
        $name = str_replace('-', ' ', $name);

        $nameParts = explode(' ', $name);
        $nameParts = array_map(
            static fn($namePart) => ucfirst(substr($namePart[0], 0, 1)) . strtolower(substr($namePart, 1)),
            $nameParts
        );

        return implode(' ', $nameParts);
    }
}
