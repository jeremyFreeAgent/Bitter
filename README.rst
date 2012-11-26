Bitter Documentation
====================

.. image:: https://secure.travis-ci.org/jeremyFreeAgent/Bitter.png?branch=master
   :target: http://travis-ci.org/jeremyFreeAgent/Bitter

Bitter is a simple but powerful analytics library

    "Use Bitter and you have time to drink a bitter beer !"

-- **Jérémy Romey**

Bitter can answer following questions:

* Has user X been online today? This week? This month?
* Has user X performed action "Y"?
* How many users have been active have this month? This hour?
* How many unique users have performed action "Y" this week?
* How many % of users that were active last week are still active?
* How many % of users that were active last month are still active this month?

Bitter is very easy to use and enables you to create your own reports easily - see the `Bitter Library <http://bitter.free-agent.fr/>`_ website for more info and documentation about this project.

Installation
------------
Use `Composer <https://github.com/composer/composer/>`_ to install: ``free-agent/bitter``.

In your `composer.json` you should have:

.. code-block:: yaml

    {
        "require": {
            "free-agent/bitter": "*"
        }
    }

Requirements
~~~~~~~~~~~~
Bitter uses `Redis <http://redis.io>`_  with version **>=2.6**.

**Note**: Every key created in Redis will be prefixed by ``bitter:``, temp keys by ``bitter_temp:``.

Basic usage
-----------
Create a Bitter with a Redis client (Predis as example):

.. code-block:: php

    $redisClient = new \Predis\Client();
    $bitter = new \FreeAgent\Bitter\Bitter($redisClient);

Mark user 123 as active and has played a song:

.. code-block:: php

    $bitter->mark('active', 123);
    $bitter->mark('song:played', 123);

**Note**: Please don't use huge ids (e.g. 2^32 or bigger) cause this will require large amounts of memory.

Pass a DateTime as third argument:

.. code-block:: php

    $bitter->mark('song:played', 123, new \DateTime('yesterday'));

Test if user 123 has played a song this week:

.. code-block:: php

    $currentWeek = new FreeAgent\Bitter\Event\Week('song:played');

    if ($bitter->in(123, $currentWeek) {
        echo 'User with id 123 has played a song this week.';
    } else {
        echo 'User with id 123 has not played a song this week.';
    }

How many users were active yesterday:

.. code-block:: php

    $yesterday = new \FreeAgent\Bitter\Event\Day('active', new \DateTime('yesterday'));

    echo $bitter->count($yesterday) . ' users were active yesterday.';

Using BitOp
-----------
How many users that were active yesterday are also active today:

.. code-block:: php

    $today     = new \FreeAgent\Bitter\Event\Day('active', new \DateTime());
    $yesterday = new \FreeAgent\Bitter\Event\Day('active', new \DateTime('yesterday'));

    $count = $bitter
        ->bitOpAnd('bit_op_example', $today, $yesterday)
        ->count('bit_op_example')
    ;
    echo $count . ' were active yesterday and today.';

**Note**: The ``bit_op_example`` key will expire after 60 seconds.

Test if user 123 was active yesterday and is active today:

.. code-block:: php

    $today     = new \FreeAgent\Bitter\Event\Day('active', new \DateTime());
    $yesterday = new \FreeAgent\Bitter\Event\Day('active', new \DateTime('yesterday'));

    $active = $bitter
        ->bitOpAnd('bit_op_example', $today, $yesterday)
        ->in(123, 'bit_op_example')
    ;
    if ($active) {
        echo 'User 123 was active yesterday and today.';
    } else {
        echo 'User 123 was not active yesterday and today.';
    }

**Note**: Please look at `Redis BITOP Command <http://redis.io/commands/bitop>`_ for performance considerations.

Unit Tests
----------

You can run tests with:

.. code-block:: sh

    bin/atoum -mcn 1 -d tests/units

Todo
----
* Implements the `Redis BITOP NOT Command <http://redis.io/commands/bitop>`_.

Thanks
------
This library is a port of `bitmapist <https://github.com/Doist/bitmapist/>`_ (Python) by `Amir Salihefendic <http://amix.dk/>`_.
