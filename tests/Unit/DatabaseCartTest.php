<?php

use Joelwmale\Cart\Cart;
use Joelwmale\Cart\Tests\Helpers\MockCartModel;

beforeEach(function () {
    $events = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');
    $events->shouldReceive('dispatch');

    $storage = new MockCartModel;

    $this->cart = new Cart(
        $storage,
        $events,
        'cart',
        'SAMPLESESSIONKEY',
        require (__DIR__ . '/../Helpers/ConfigDatabaseMock.php')
    );
});

afterEach(function () {
    Mockery::close();
});

describe('database cart', function () {
    test('can add an item', function () {
        $this->cart->add(1, 'Sample Item', 100.99, 2, []);

        expect($this->cart->isEmpty())->toBeFalse('Cart should not be empty');
        expect($this->cart->getContent()->count())->toEqual(1, 'Cart content should be 1');
        expect($this->cart->getContent()->first()['id'])->toEqual(1, 'Item added has ID of 1 so first content ID should be 1');
        expect($this->cart->getContent()->first()['price'])->toEqual(100.99, 'Item added has price of 100.99 so first content price should be 100.99');

        expect(MockCartModel::all()->count())->toEqual(1, 'The carts database table should have 1 row');
    });

    test('can add and remove an item', function () {
        $this->cart->add(1, 'Sample Item', 100.99, 2, []);

        expect($this->cart->isEmpty())->toBeFalse('Cart should not be empty');
        expect($this->cart->getContent()->count())->toEqual(1, 'Cart content should be 1');
        expect($this->cart->getContent()->first()['id'])->toEqual(1, 'Item added has ID of 1 so first content ID should be 1');
        expect($this->cart->getContent()->first()['price'])->toEqual(100.99, 'Item added has price of 100.99 so first content price should be 100.99');

        expect(MockCartModel::all()->count())->toEqual(1, 'The carts database table should have 1 row');

        $this->cart->remove(1);

        $cartModel = MockCartModel::first();

        expect($this->cart->isEmpty())->toBeTrue('Cart should be empty');
        expect($this->cart->getContent()->count())->toEqual(0, 'Cart content should be 0');
        expect($cartModel->items)->toEqual([], 'The carts database table should have 0 row');
    });
});
