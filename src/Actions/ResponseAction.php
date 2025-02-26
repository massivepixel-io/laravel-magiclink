<?php

namespace MagicLink\Actions;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Laravel\SerializableClosure\SerializableClosure;
use MagicLink\MagicLink;

class ResponseAction extends ActionAbstract
{
    protected $httpResponse;

    /**
     * Constructor to action.
     *
     * @param  mixed  $httpResponse
     */
    public function __construct($httpResponse = null)
    {
        $this->response($httpResponse);
    }

    public function response($response): self
    {
        $this->httpResponse = $this->serializeResponse($response);

        return $this;
    }

    public function redirect($response): self
    {
        $this->httpResponse = $this->serializeResponse($response);

        return $this;
    }

    protected function serializeResponse($httpResponse)
    {
        return serialize($this->formattedResponse($httpResponse));
    }

    protected function formattedResponse($response)
    {
        if (is_null($response)) {
            return new RedirectResponse(
                config('magiclink.url.redirect_default', '/'),
                302
            );
        }

        if ($response instanceof RedirectResponse) {
            return new RedirectResponse(
                $response->getTargetUrl(),
                $response->getStatusCode()
            );
        }

        if (is_callable($response)) {
            return new SerializableClosure(Closure::fromCallable($response));
        }

        if ($response instanceof View) {
            return $response->render();
        }

        return $response;
    }

    /**
     * Execute Action.
     */
    public function run()
    {
        return $this->callResponse(unserialize($this->httpResponse));
    }

    protected function callResponse($httpResponse)
    {
        if (is_callable($httpResponse)) {
            return $httpResponse(static::find($this->magiclinkId));
        }

        return $httpResponse;
    }
}
