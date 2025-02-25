<?php

namespace Joelwmale\Cart;

class CartSession
{
    private $customStorage;

    private $session;

    private $sessionId = null;
    private $itemsKey = null;
    private $conditionsKey = null;

    public function __construct($session, $sessionKey, $config)
    {
        if ($config['driver'] === 'session') {
            $this->customStorage = false;
            $this->session = $session;
            $this->itemsKey = $sessionKey . '_cart_items';
            $this->conditionsKey = $sessionKey . '_cart_conditions';
        } elseif ($config['driver'] === 'database') {
            $this->customStorage = true;

            $this->sessionId = $config['storage']['database']['id'];
            $this->itemsKey = $config['storage']['database']['items'];
            $this->conditionsKey = $config['storage']['database']['conditions'];

            // find or create the session in the database
            $this->session = $session->firstOrNew([$this->sessionId => $sessionKey]);

            // initialize the items and conditions
            $this->session[$this->itemsKey] = $this->session[$this->itemsKey] ?? [];
            $this->session[$this->conditionsKey] = $this->session[$this->conditionsKey] ?? [];
        }
    }

    public function isInternalSession($session)
    {
        return in_array(is_object($session) ? get_class($session) : $session, [
            'Illuminate\Session\SessionManager',
            'Joelwmale\Cart\Tests\Helpers\SessionMock',
        ]);
    }

    public function has($key)
    {
        return $this->session->has($key) ?? $this->session;
    }

    public function getItems()
    {
        return ! $this->customStorage ? $this->session->get($this->itemsKey) : $this->session[$this->itemsKey];
    }

    public function getConditions()
    {
        return ! $this->customStorage ? $this->session->get($this->conditionsKey) : $this->session[$this->conditionsKey];
    }

    public function putItems($value)
    {
        if (! $this->customStorage) {
            return $this->session->put($this->itemsKey, $value);
        }

        $this->session[$this->itemsKey] = $value;
        $this->session->save();

        return $this->session;
    }

    public function putConditions($value)
    {
        if (! $this->customStorage) {
            return $this->session->put($this->conditionsKey, $value);
        }

        if (! empty($value)) {
            $value = $value->mapWithKeys(function ($condition) {
                return [$condition->getName() => $condition->toArray()];
            });
        }

        $this->session[$this->conditionsKey] = $value;
        $this->session->save();

        return $this->session;
    }

    public function clear()
    {
        if ($this->customStorage) {
            return $this->session->delete();
        }

        $this->session->forget($this->itemsKey);
        $this->session->forget($this->conditionsKey);

        return true;
    }
}
