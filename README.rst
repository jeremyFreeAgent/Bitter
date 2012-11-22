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

Bitter is very easy to use and enables you to create your own reports easily.

.. note::
    Please look `Bitter<http://bitter.free-agent.fr/>`_ website for more info and documentation about this project.

Installation
------------
Use `Composer <https://github.com/composer/composer/>`_ to install: `free-agent/bitter`.

In your `composer.json` you should have:
.. code-block:: yaml

    {
        "require": {
            "free-agent-bitter": "*"
        }
    }

Bitter uses `Redis <http://redis.io>`_ (version >=2.6).

.. note::
    Every keys created in `Redis` will be prefixed by `bitter:` ; temp keys by `bitter_temp:`.

Basic usage
-----------
Create a Bitter with a Redis client (Predis as example):

.. code-block:: php

    $redisClient = new \Predis\Client();
    $bitter = new \Bitter($redisClient);

Mark user 404 as active and has been kicked by Chuck Norris:

.. code-block:: php

    $bitter->mark('active', 404);
    $bitter->mark('kicked_by_chuck_norris', 404);

.. note::

    Please don't use huge ids (e.g. 2^32 or bigger) cause this will require large amounts of memory.

Pass a DateTime as third argument:

.. code-block:: php

    $bitter->mark('damned_by_jack_bauer', 404, new \DateTime('yesterday'));

Test if user 404 as been kicked by Chuck Norris this week:

.. code-block:: php

    $currentWeek = new \Bitter\Event\Week('kicked_by_chuck_norris');

    if ($bitter->in(404, $currentWeek) {
        echo 'User with id 404 has been kicked by Chuck Norris this week.';
    } else {
        echo 'User with id 404 has not been kicked by Chuck Norris this week.';
    }

How many users have been active yesterday:

.. code-block:: php

    $yesterday = new \Bitter\Event\Day('active', new \DateTime('yesterday'));

    echo 'Yesterday: ' . $bitter->count($yesterday) . ' users has been active.';

Using BitOp
-----------
How many users that were active yesterday are active today:

.. code-block:: php

    $today     = new \Bitter\Event\Day('active', new \DateTime());
    $yesterday = new \Bitter\Event\Day('active', new \DateTime('yesterday'));

    $count = $bitter
        ->bitOpAnd('bit_op_example', $today, $yesterday)
        ->count('bit_op_example')
    ;
    echo $count . ' were active yesterday are active today.';

.. note::
    The `bit_op_example` key will expire after 60 seconds.

Test if user 13 was active yesterday and is active today:

.. code-block:: php

    $today     = new \Bitter\Event\Day('active', new \DateTime());
    $yesterday = new \Bitter\Event\Day('active', new \DateTime('yesterday'));

    $active = $bitter
        ->bitOpAnd('bit_op_example', $today, $yesterday)
        ->in(13, 'bit_op_example')
    ;
    if ($active) {
        echo 'User 13 was active yesterday and today.';
    } else {
        echo 'User 13 was not active yesterday and today.';
    }

.. note::
    Please look at `Redis BITOP Command <http://redis.io/commands/bitop>`_ for performance considerations.

Unit Tests
----------

.. code-block:: sh

    bin/atoum -mcn 1 -d tests/units

Todo
----
* Implements the `Redis BITOP NOT Command <http://redis.io/commands/bitop>`_.

Thanks
------
This library is a port of `bitmapist <https://github.com/Doist/bitmapist/>`_ (Python) by `Amir Salihefendic <http://amix.dk/>`_.
