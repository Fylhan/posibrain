TchatBot
========

A re-usable tchatbot PHP implementation to never be alone again. Hum, wait... What?
Nothing very intelligent at the moment, a set of predefined sentances lead to an other set of predefined answers. But still, synonyms can be defined, data from the sentance can be used in the answer, and of course the same answer won't be use everytime.

Based on this [Eliza PHP tchatbot implementation](http://www.perkiset.org/forum/all_things_general_tech/artificial_intelligence_as_we_know_it_today-t1177.5.html;wap2=), at least for the concept.

Installation & Usage
------------
User Composer, or just download, copy and paste. No configuration is required because it is just an algorithm.

You can easily use it in your code:

    require '../vendor/autoload.php';
    use Fylhan\TchatBot\TchatBot;

    header("Content-Type: text/html; charset=UTF-8");
    $bot = new TchatBot();
    list($botName, $botMessage) = $bot->generateAnswer('Bnmaster', 'Bonjour mon ami', time());
    echo $botName.' : '.$botMessage.'<br />';
    // May display for example: 'Hari S.: Salutations noble ami.'


Or even using command line:

    Usage: php app/console submit "User message" ["User name"]
    > php app/console submit "Bonjour mon ami"
    Hari S.: Salutations noble ami.

How to test?
-----------
Launch the file [test.php](https://github.com/Fylhan/tchatbot/blob/master/test/test.php) in a browser. A list of sentances, and their bot answer, will be displayed.

![Tchatbot discussion example](https://raw.github.com/Fylhan/tchatbot/master/doc/tchatbot-example.png)

An interactive tchat implementation will come. At the moment, this tchatbot is installed on the [Bnbox minitchat](http://la-bnbox.fr) (fr), you can try to speack with him in french by beginning your sentances by "@Hari".


How to use and modify?
-----------
A documentation will come.


Work in progress
----------------
A lot of things are in progress!
- [▒▒▒▒ 0% ] Update the first brain's knowledge
- [█▒▒▒ 25% ] Provide a way to select between several brains
- [▒▒▒▒ 0% ] Manage several langage
- [▒▒▒▒ 0% ] Add discussion log that will be used by the bot to learn and be a little bit more inteligent
- [████100%] Add Composer support
- [█▒▒▒ 25%] Check and improve folder structure
- [▒▒▒▒ 0% ] Add unit tests
- [▒▒▒▒ 0% ] Add documentation
- [▒▒▒▒ 0% ] Increase knowledge syntax possibilities
- [▒▒▒▒ 0% ] Manage several charset, not only UTF-8
- [▒▒▒▒ 0% ] Add more and more intelligence !
- [▒▒▒▒ 0% ] Create an interactive tchat example
- [██▒▒ 50%] Add command line support


Licencing
--------
This pieces of code are free software under under [LGPL v2.1](http://choosealicense.com/licenses/lgpl-v2.1/). See [LICENSE file](https://github.com/Fylhan/tchatbot/blob/master/LICENSE) for more information.

To summarize:
* Required
  * License and copyright notice
  * Disclose your modified Source if you re-distribute this software
* Permitted
  * Commercial Use
  * Modification
  * Distribution
  * Sublicensing
  * Patent Grant
* Forbidden 	
  * Hold Liable
