<?php


namespace Tests\Spacebib\Saga\Emails;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class FailedSagaMail extends Mailable implements ShouldQueue
{

}
