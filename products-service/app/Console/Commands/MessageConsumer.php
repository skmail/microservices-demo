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
        \Amqp::consume($this->argument('queue'), function ($message, $resolver) {                    
            $routingKey = $message->delivery_info['routing_key'];
            
            $resolver->acknowledge($message);
            
            $resolver->stopWhenProcessed();                
         },[
            'persistent' => true
         ]);
    }
}