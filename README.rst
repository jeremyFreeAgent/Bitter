Bitter Documentation
====================
Bitter is a simple but powerful real-time highly-scalable analytics library

    "Use Bitter and you have time to drink a bitter beer !"

-- **Jérémy Romey**

Installation
------------
Use `Composer <https://github.com/composer/composer/>`_ to install: `free-agent/bitter`.

Bitter uses `Redis <http://redis.io>`_ (version >=2.6).

Usage
-----
Create a Bitter with a Redis client (Predis as example):

.. code-block:: php

    $redisClient = new \Predis\Client();
    $bitter = new Bitter($redisClient);

Mark user 13003 as active and has been kicked by Chuck Norris:

.. code-block:: php

    $bitter = markEvent('active', 13003);
    $bitter = markEvent('kicked_by_chuck_norris', 13003);

.. note::

    Please don't use huge ids (e.g. 2^32 or bigger) cause this will require large amounts of memory.

Test if user 13003 as been kicked by Chuck Norris this week:

.. code-block:: php

    $currentWeek = new \Bitter\Event\Week('kicked_by_chuck_norris');

    if ($bitter->contain($currentWeek, 13003) {
        echo 'User with id 13003 has been kicked by Chuck Norris this week.';
    } else {
        echo 'User with id 13003 has not been kicked by Chuck Norris this week.';
    }

How many users have been active yesterday:

.. code-block:: php

    $yesterday = new \Bitter\Event\Day('active', new DateTime('yesterday'));

    echo 'Yesterday: ' . $bitter->count($yesterday) . ' users has been active.';

Thanks
------
This library is a port of `bitmapist <https://github.com/Doist/bitmapist/>`_ (Python) by `Amir Salihefendic <http://amix.dk/>`_.
