<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MessageConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:consume {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Messages Consumer.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $queue = $this->argument('queue');

        $this->warn('Consuming  ' . $queue . '.');

        \Amqp::consume($queue, function ($message, $resolver) use($queue) {                    
            
            $routingKey = $message->delivery_info['routing_key'];
            
            $eventName = $queue. ':' . $routingKey;

            $this->warn('Started  ' . $eventName . '.');
                
            $body = $message->body;
            
            $jsonDecoded = @json_decode($message->body, true);
            
            if(is_array($jsonDecoded)){
                $body = $jsonDecoded;
            }

            event($eventName, [
                'body' => $body
            ]);
            
            $resolver->acknowledge($message);
            
            $this->info('Finished  ' . $eventName . '.');
         },[
            'persistent' => true
         ]);
    }
}