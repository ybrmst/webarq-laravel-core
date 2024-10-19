<?php

namespace Webarq\Manager\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Wa;

class SimpleMailManager extends Mailable
{
    use Queueable, SerializesModels;

    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject($this->data['subject'])
                ->from(
                        Wa::config('system.email.sender', 'noreply@info.com'),
                        Wa::config('system.email.name', Wa::config('system.cms.title',
                                config('webarq.projectInfo.name', 'WEBARQ')))
                )
                ->view('webarq::template.email.general', $this->data);
    }
}
