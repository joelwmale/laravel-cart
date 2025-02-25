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
        if ($this->isInternalSession($session)) {
            $this->customStorage = false;
            $this->session = $session;
            $this->itemsKey = $sessionKey . '_cart_items';
            $this->conditionsKey = $sessionKey . '_cart_conditions';
        } else {
            $this->customStorage = true;

            // @TODO validate these 3 exist
            $this->sessionId = $config['storage_id'];
            $this->itemsKey = $config['storage_items'];
            $this->conditionsKey = $config['storage_conditions'];

            // get the base class to do findOrCreate
            $this->session = $config['storage'];
            $this->session = (new $this->session)->firstOrNew([$this->sessionId => $sessionKey]);

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
