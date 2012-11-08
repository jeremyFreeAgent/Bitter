Bitter Documentation
====================
Bitter is a simple but powerful analytics library

    "Use Bitter and you have time to drink a bitter beer !"

-- **Jérémy Romey**

Installation
------------
Use `Composer <https://github.com/composer/composer/>`_ to install: `free-agent/bitter`.

Bitter uses `Redis <http://redis.io>`_ (version >=2.6).

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

    //Can pass a \DateTime as third argument
    //$bitter->mark('jack_bauer_is_so', 404, new \DateTime('yesterday'));

.. note::

    Please don't use huge ids (e.g. 2^32 or bigger) cause this will require large amounts of memory.

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

    $yesterday = new \Bitter\Event\Day('active', new DateTime('yesterday'));

    echo 'Yesterday: ' . $bitter->count($yesterday) . ' users has been active.';

Using BitOp
-----------
How many users that were active yesterday are active today:

.. code-block:: php

    $today     = new Bitter\Event\Day('active', new DateTime());
    $yesterday = new Bitter\Event\Day('active', new DateTime('yesterday'));

    $count = $bitter->bitOpAnd('bit_op_example', $today, $yesterday)->count('bit_op_example');
    echo $count . ' were active yesterday are active today.';

.. note::
    Please look at `Redis BITOP Command <http://redis.io/commands/bitop>`_ for performance considerations.

TODO
----
* Better prefix key.
* Better tests.

Thanks
------
This library is a port of `bitmapist <https://github.com/Doist/bitmapist/>`_ (Python) by `Amir Salihefendic <http://amix.dk/>`_.
