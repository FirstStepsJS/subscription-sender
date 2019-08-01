<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\Dotmailer;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class DailyArticles extends Command
{    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'send:daily';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Send daily articles Emails';

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    public function getDailySubData($userId) {

        $jwtAuthString = '';
        $client = new Client([
            'base_uri' => 'https://api.tobedeployednode.com/articles/daily/' . $userId,
            'headers' => [
                'authorization' => 'JWT example'
            ]
        ]);
        $response = $client->request('GET');
        $dailySubData = json_decode($response->getBody());

        return $dailySubData->data;
    }

    public function handle()
    {
        Log::info('starting daily subscription emails');

        $dotmailer = new Dotmailer();
        Log::info('finding subscribed articles');
        // list of subscribed members
        $members = DB::select(select * from users where subscribed_daily = 1);
        Log::info(sprintf('found %d subscribed members', sizeof($members)));

        // each member needs to be evaluated & check whether they have at least 1 article matching their preferences
        foreach ($members as $one) {
            Log::info(sprintf('sending to: ' .  $one->email_address));

            $data = [
                'FIRSTNAME'         =>  $one->first_name,
                'article_1_title'       => 'Placeholder article_1_title',
                'article_1_sub_title'      => 'Placeholder article_1_sub_title',
                'article_1_description' => 'Placeholder article_1_description',
                'article_1_author'    => 'Placeholder article_1_author',
                'article_1_url'         => 'Placeholder article_1_url',
                'button_1_text'     => 'Placeholder button_1_text',

                'article_2_title'       => 'Placeholder article_2_title',
                'article_2_sub_title'      => 'Placeholder article_2_sub_title',
                'article_2_description' => 'Placeholder article_2_description',
                'article_2_author'    => 'Placeholder article_2_author',
                'article_2_url'         => 'Placeholder article_2_url',
                'button_2_text'     => 'Placeholder button_2_text',

                'article_3_title'       => 'Placeholder article_3_title',
                'article_3_sub_title'      => 'Placeholder article_3_sub_title',
                'article_3_description' => 'Placeholder article_3_description',
                'article_3_author'    => 'Placeholder article_3_author',
                'article_3_url'         => 'Placeholder article_3_url',
                'button_3_text'     => 'Placeholder button_3_text',

                'article_4_title'       => 'Placeholder article_4_title',
                'article_4_sub_title'      => 'Placeholder article_4_sub_title',
                'article_4_description' => 'Placeholder article_4_description',
                'article_4_author'    => 'Placeholder article_4_author',
                'article_4_url'         => 'Placeholder article_4_url',
                'button_4_text'     => 'Placeholder button_4_text',
            ];
            $articles = $this->getDailySubData($one->id);
            $j = 1;
            $max_articles = 4;
            foreach ($articles as $article) {
                $data['article_'.$j.'_title']       = $article->title;
                $data['article_'.$j.'_sub_title']      = $article->sub_title;
                $data['article_'.$j.'_description'] = $article->description;
                $data['article_'.$j.'_author']    = $article->author;
                $data['article_'.$j.'_url']         = $article->application_url . "?utm_source=Email&utm_medium=Dotmailer&utm_campaign=Daily_articles";
                $data['button_'.$j.'_text']     = "View";
                if ($j++ > $max_articles) break;
            }
            $data['count'] = min($j, $max_articles);
            $dotmailer->sendCampaign('daily_subscription', $one->email_address, $data);
        }

        Log::info('finished');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        //$schedule->command(static::class)->everyDay();
    }
}
