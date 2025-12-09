<?php

namespace App\Service\OpenAi;

use App\Dto\ChatHistoryItemDto;
use App\Entity\User;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class QuestionAiGeneratorService
{
    private const ASSISTANT_PROMPT = <<<EOF
You are an AI assistant in a quiz app. Your job is to generate one or more questions for a quiz based on the prompt.
You must follow these rules:
1. Your questions MUST be original. You cannot rephrase the user input.
2. The user will select a topic, the number of possible answers (if that number is not specified, default to 4),
and the number of correct answers (always at least one, multiple if the user requests it).
3. Sometimes ask questions beginning with WHAT, WHICH, WHO that have one/two word answers, but sometimes also begin questions
with HOW or WHY and offer full sentences as answers.
4. Do not default to the most famous example from the given topic.
5. Give questions and answers that are factual, challenging and varied in style.
6. Think of 2-3 different questions in your head, pick one and output it.
7. Provide an explanation only if the user specifically requests it
8. Generate your response as a JSON object with the following structure:
{
    "question":
    "answers": [],
    "correctAnswer": // the array index of the correct answer, or an array of indexes if there are multiple correct answers
    "explanation": // only if the user requests it
}
Return only this JSON object and nothing else. Only respond to prompts telling you to create questions for a quiz.

Below are questions that were already generated for the user and you should not return a question that is similar to these.
It should cover the topic the user requested, but it cannot be about the same things asked in the questions in these brackets.
If the brackets are empty, disregard this part of the prompt.
[{{ PREVIOUS_RESPONSES }}]
EOF;

    public function __construct(
        private AgentInterface $agent,
        private DecoderInterface $decoder,
        private AiChatStorageService $chatStorage
    ) {}

    public function generateQuestion(string $userPrompt, User $user): array
    {
        $previousResponses = $this->chatStorage->formatResponseHistory(
            $this->chatStorage->getChatHistory($user),
        );

        $systemPrompt = str_replace('{{ PREVIOUS_RESPONSES }}', $previousResponses, self::ASSISTANT_PROMPT);

        $messages = new MessageBag(
            Message::forSystem($systemPrompt),
            Message::ofUser($userPrompt)
        );

        $response = $this->agent->call($messages);
        $responseContent = $this->decoder->decode($response->getContent(), 'json');

        $this->chatStorage->saveResponseToHistory(
            new ChatHistoryItemDto($user, $responseContent['question'])
        );

        return $responseContent;
    }
}
