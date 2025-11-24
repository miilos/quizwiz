<?php

namespace App\Service\OpenAi;

use Symfony\Contracts\HttpClient\ResponseInterface;

class QuestionAssistantWrapper extends OpenAi
{
    private const ASSISTANT_PROMPT = <<<EOF
        You are an AI assistant in a quiz app. Your job is to generate one or more questions for a quiz based on the prompt.
        The user will select a topic, the number of possible answers (if that number is not specified, default to 4),
        and the number of correct answers (always at least one, multiple if the user requests it).
        The length of the possible answers should vary from one word to a medium-length sentence. Create the question accordingly,
        so that the answers could be longer than one word. Generate your response as a JSON object with the following structure:
        {
            "question": // the question you will generate
            "answers": [
                // the possible answers go here
            ],
            "correctAnswer": // the array index of the correct answer, or an array of indexes if there are multiple correct answers
        }
        Return only this JSON object and nothing else. Only respond to prompts telling you to create questions for a quiz.
    EOF;

    public function prompt(string $prompt): array
    {
        /** @var ResponseInterface $res */
        $res = $this->chat(
            $this->removeWhitespaceFromPrompt(self::ASSISTANT_PROMPT),
            $this->removeWhitespaceFromPrompt($prompt)
        );
        return $res->toArray();
    }

    // removes newlines and tabs to keep token costs down lmao
    private function removeWhitespaceFromPrompt(string $prompt): string
    {
        return str_replace(['\n', '\r', '\t'], '', $prompt);
    }
}
