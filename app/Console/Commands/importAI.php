<?php

namespace App\Console\Commands;

use Orhanerday\OpenAi\OpenAi;
use Statamic\Facades\Entry;
use Illuminate\Console\Command;





class importAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import {question}';

    /**
     * The console command description.
     * 
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $open_ai_key = getenv('OPENAI_API_KEY');
        $open_ai = new OpenAi($open_ai_key);

        $question = $this->argument('question');

        $complete = $open_ai->completion([
            'model' => 'davinci',
            'prompt' => $question,
            'temperature' => 0.9,
            'max_tokens' => 150,
            'frequency_penalty' => 0,
            'presence_penalty' => 0.6,
        ]);

        // var_dump($complete);

        $parse = json_decode($complete);


        $post = [
            'title' => $question,
            'content' => $parse->choices[0]->text,
        ];


        $this->createPost($post);
        return Command::SUCCESS;
    }

    public function questions()
    {
        $glossary = ['What is the difference between a hop and a malt?', 'Fermentation', 'Hops', 'Malt', 'Mash', 'Yeast', 'Wort', 'Lautering', 'Sparge', 'Gravity'];
        return $glossary;
    }

    public function createPost($post)
    {

        $entry = Entry::make()->collection('articles');
        $entry->set('title', $post['title'])
            ->set('content', $post['content']);

        $entry->save();
    }
}
